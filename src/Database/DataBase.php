<?php

namespace App\Database;

use PDO;
use PDOException;

final class DataBase
{
    private static ?DataBase $instance = null;
    private PDO $dbh;

    private function __construct()
    {
        $conStr = sprintf(
            "pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s",
            $_ENV['HOST'],
            $_ENV['PORT'],
            $_ENV['DATABASE'],
            $_ENV['USER'],
            $_ENV['PASSWORD']
        );

        try {
            $this->dbh = new PDO($conStr);
        } catch (PDOException $e) {
            die("Failed to connect to database: " . $e->getMessage());
        }
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = new self();
        } else {
            try {
                self::$instance->dbh->query('SELECT 1');
            } catch (PDOException $e) {
                self::$instance = new self();
            }
        }

        return self::$instance->dbh;
    }
}