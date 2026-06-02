<?php

namespace DataAccess;

use PDO;
use PDOException;

class DbContext
{
    private static ?PDO $connection = null;

    private function __construct() {}

    private function __clone() {}

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            
            $host = 'localhost';
            $dbName = 'financial_planner';
            $username = 'root';
            $password = ''; 

            $dsn = "mysql:host=$host;dbname=$dbName;charset=utf8mb4";

            try {
                self::$connection = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                die("Exeption " . $e->getMessage());
            }
        }

        return self::$connection;
    }
}