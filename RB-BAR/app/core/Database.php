<?php

declare(strict_types=1);

class Database
{
    private static ?PDO $connection = null;

    public static function connect(): PDO
    {
        if (self::$connection !== null) {
            return self::$connection;
        }

        $configPath = __DIR__ . '/../../config/config.php';
        $localConfigPath = __DIR__ . '/../../config/database.local.php';

        if (!is_file($configPath)) {
            throw new RuntimeException(
                'No se encontró config/config.php.'
            );
        }

        if (!is_file($localConfigPath)) {
            throw new RuntimeException(
                'No se encontró config/database.local.php.'
            );
        }

        $config = require $configPath;
        $localConfig = require $localConfigPath;

        if (!is_array($config) || !is_array($localConfig)) {
            throw new RuntimeException(
                'La configuración de la aplicación no es válida.'
            );
        }

        $config = array_replace($config, $localConfig);

        foreach (['host', 'port', 'database', 'username', 'password'] as $key) {
            if (!array_key_exists($key, $config)) {
                throw new RuntimeException(
                    "Falta la configuración de conexión: {$key}."
                );
            }
        }

        $host = $config['host'];
        $port = $config['port'];
        $database = $config['database'];
        $username = $config['username'];
        $password = $config['password'];
        $charset = $config['charset'] ?? 'utf8mb4';

        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset={$charset}";

        try {
            self::$connection = new PDO(
                $dsn,
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );

            return self::$connection;
        } catch (PDOException $e) {
            error_log('Error de conexión con MySQL: ' . $e->getMessage());

            throw new RuntimeException(
                'No fue posible conectar con la base de datos.'
            );
        }
    }
}
