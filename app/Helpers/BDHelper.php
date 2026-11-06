<?php

class BDHelper
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $config = require BASE_PATH . '/config/database.php';
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['name']};charset=utf8mb4";
            self::$connection = new PDO($dsn, $config['user'], $config['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        return self::$connection;
    }

    public static function run(string $sql, array $params = []): PDOStatement
    {
        $statement = self::getConnection()->prepare($sql);
        $statement->execute($params);
        return $statement;
    }

    public static function all(string $sql, array $params = []): array
    {
        return self::run($sql, $params)->fetchAll();
    }

    public static function one(string $sql, array $params = []): ?array
    {
        $row = self::run($sql, $params)->fetch();
        return $row ?: null;
    }

    public static function value(string $sql, array $params = [])
    {
        return self::run($sql, $params)->fetchColumn();
    }

    public static function lastId(): int
    {
        return (int) self::getConnection()->lastInsertId();
    }
}
