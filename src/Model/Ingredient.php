<?php

namespace App\Model;

use App\Database\DataBase;
use Exception;

class Ingredient extends BaseModel
{
    protected static string $tableName = 'ingredient';

    /**
     * @throws Exception
     */
    public static function add(array $data): string
    {
        $db = DataBase::getInstance();
        $tableName = static::getTableName();
        $stmt = $db->prepare("INSERT INTO $tableName(name, unit) VALUES (:name, :unit)");

        $params = ['name' => $data['name'], 'unit' => $data['unit']];

        if ($stmt->execute($params)) {
            return $db->lastInsertId();
        } else {
            throw new Exception('Error when deleting by id');
        }
    }

    /**
     * @throws Exception
     */
    public static function update(int $id, array $data): bool
    {
        $db = DataBase::getInstance();
        $tableName = static::getTableName();
        $stmt = $db->prepare(
            "UPDATE $tableName SET name = COALESCE(:name, name), unit = COALESCE(:unit, unit) WHERE id = :id"
        );

        $params = ['id' => $id, 'name' => $data['name'], 'unit' => $data['unit']];

        if ($stmt->execute($params)) {
            return true;
        } else {
            throw new Exception('Error when deleting by id');
        }
    }
}