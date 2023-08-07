<?php

namespace App\Model;

use App\Database\DataBase;
use Exception;
use PDO;

class Recipe extends BaseModel
{
    protected static string $tableName = 'recipe';
    private static string $manyToManyTable = 'recipe_ingredient';

    /**
     * @throws Exception
     */
    public static function getById(int $id): array
    {
        $db = DataBase::getInstance();
        $tableName = static::getTableName();
        $ingredientTable = Ingredient::getTableName();
        $manyToManyTable = static::$manyToManyTable;
        $stmt = $db->prepare(
            "
                SELECT r.id   as r_id,
                       r.name as r_name,
                       r.steps,
                       r.path_to_photo,
                       i.id   as i_id,
                       i.name as i_name,
                       ri.amount,
                       i.unit
                FROM $tableName r
                         LEFT JOIN $manyToManyTable ri on r.id = ri.recipe_id
                         LEFT JOIN $ingredientTable i on i.id = ri.ingredient_id
                WHERE r.id = :id
                    "
        );
        $params = ['id' => $id];

        if ($stmt->execute($params)) {
            $dbData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $ingredientsData = [];
            foreach ($dbData as $row) {
                $ingredientsData[] = [
                    'id' => $row['i_id'],
                    'name' => $row['i_name'],
                    'amount' => $row['amount'],
                    'unit' => $row['unit'],
                ];
            }

            return !empty($dbData) ? [
                'id' => $dbData[0]['r_id'],
                'name' => $dbData[0]['r_name'],
                'steps' => $dbData[0]['steps'],
                'path_to_photo' => $dbData[0]['path_to_photo'],
                'ingredients' => $ingredientsData
            ] : [];
        } else {
            throw new Exception('Error when select by id');
        }
    }

    /**
     * @throws Exception
     */
    public static function add(array $data): string
    {
        $db = DataBase::getInstance();

        $db->beginTransaction();

        $tableName = static::getTableName();
        $stmt = $db->prepare(
            "INSERT INTO $tableName(name, steps, path_to_photo) 
                    VALUES (:name, :steps, :path_to_photo)"
        );
        $params = ['name' => $data['name'], 'steps' => $data['steps'], 'path_to_photo' => $data['path_to_photo']];

        if ($stmt->execute($params)) {
            $recipeId = $db->lastInsertId();
        } else {
            $db->rollBack();
            throw new Exception('Error when add recipe');
        }

        $manyToManyTable = static::$manyToManyTable;
        $stmt = $db->prepare(
            "INSERT INTO $manyToManyTable(recipe_id, ingredient_id, amount) 
                    VALUES (:recipe_id, :ingredient_id, :amount)"
        );
        foreach ($data['ingredients'] as $ingredient) {
            $params = [
                'recipe_id' => $recipeId,
                'ingredient_id' => $ingredient['id'],
                'amount' => $ingredient['amount']
            ];
            if (!$stmt->execute($params)) {
                $db->rollBack();
                throw new Exception('Error when add recipe');
            }
        }

        $db->commit();

        return $recipeId;
    }

    /**
     * @throws Exception
     */
    public static function update(int $id, array $data): bool
    {
        $db = DataBase::getInstance();

        $db->beginTransaction();

        $tableName = static::getTableName();
        $stmt = $db->prepare("SELECT 1 FROM $tableName WHERE id = :id LIMIT 1");
        $params = ['id' => $id,];
        if ($stmt->execute($params)) {
            if (empty($stmt->fetchAll())) {
                return false;
            }
        } else {
            $db->rollBack();
            throw new Exception('Error when update recipe');
        }

        $stmt = $db->prepare(
            "
                UPDATE $tableName
                SET name          = COALESCE(:name, name),
                    steps         = COALESCE(:steps, steps),
                    path_to_photo = COALESCE(:path_to_photo, path_to_photo)
                WHERE id = :id
                  "
        );
        $params = [
            'id' => $id,
            'name' => $data['name'],
            'steps' => $data['steps'],
            'path_to_photo' => $data['path_to_photo']
        ];

        if (!$stmt->execute($params)) {
            $db->rollBack();
            throw new Exception('Error when update recipe');
        }

        $manyToManyTable = static::$manyToManyTable;
        $stmt = $db->prepare(
            "SELECT ingredient_id, amount FROM $manyToManyTable WHERE recipe_id = :id"
        );
        $params = ['id' => $id];

        if ($stmt->execute($params)) {
            $dbData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $ingredientIds = array_column($dbData, 'ingredient_id', 'ingredient_id');
            $oldAmounts = array_column($dbData, 'amount', 'ingredient_id');
        } else {
            $db->rollBack();
            throw new Exception('Error when update recipe');
        }

        foreach ($data['ingredients'] as $ingredient) {
            if (in_array($ingredient['id'], $ingredientIds)) {
                if ($ingredient['amount'] != $oldAmounts[$ingredient['id']]) {
                    $stmt = $db->prepare(
                        "
                    UPDATE $manyToManyTable
                    SET amount          = COALESCE(:amount, amount)
                    WHERE recipe_id = :recipe_id AND ingredient_id = :ingredient_id
                          "
                    );
                    $params = [
                        'recipe_id' => $id,
                        'amount' => $ingredient['amount'],
                        'ingredient_id' => $ingredient['id']
                    ];
                    if (!$stmt->execute($params)) {
                        $db->rollBack();
                        throw new Exception('Error when add recipe');
                    }
                }

                unset($ingredientIds[$ingredient['id']]);
            } else {
                $stmt = $db->prepare(
                    "INSERT INTO $manyToManyTable(recipe_id, ingredient_id, amount) 
                    VALUES (:recipe_id, :ingredient_id, :amount)"
                );
                $params = [
                    'recipe_id' => $id,
                    'ingredient_id' => $ingredient['id'],
                    'amount' => $ingredient['amount']
                ];
                if (!$stmt->execute($params)) {
                    $db->rollBack();
                    throw new Exception('Error when add recipe');
                }
            }
        }


        foreach ($ingredientIds as $iId) {
            $stmt = $db->prepare(
                "DELETE FROM $manyToManyTable WHERE recipe_id = :recipe_id AND ingredient_id = :ingredient_id"
            );
            $params = [
                'recipe_id' => $id,
                'ingredient_id' => $iId,
            ];
            if (!$stmt->execute($params)) {
                $db->rollBack();
                throw new Exception('Error when add recipe');
            }
        }

        $db->commit();

        return true;
    }
}