<?php

namespace App\Model;

use App\Database\DataBase;
use Exception;
use PDO;

class BaseModel
{
    protected static string $tableName = '';

    public static function getTableName(): string
    {
        return static::$tableName;
    }

    /**
     * @throws Exception
     */
    public static function getById(int $id): array
    {
        $db = DataBase::getInstance();
        $tableName = static::$tableName;
        $stmt = $db->prepare("SELECT * FROM $tableName WHERE id = :id");
        $params = ['id' => $id];

        if ($stmt->execute($params)) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            throw new Exception('Error when select by id');
        }
    }

    /**
     * @throws Exception
     */
    public static function all(): array
    {
        $db = DataBase::getInstance();
        $tableName = static::$tableName;
        $stmt = $db->prepare("SELECT * FROM $tableName");

        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            throw new Exception('Error when select all');
        }
    }

    /**
     * @throws Exception
     */
    public static function delete(int $id): bool
    {
        $db = DataBase::getInstance();
        $tableName = static::$tableName;
        $stmt = $db->prepare("DELETE FROM $tableName WHERE id = :id");

        $params = ['id' => $id];

        if ($stmt->execute($params)) {
            return true;
        } else {
            throw new Exception('Error when deleting by id');
        }
    }
}