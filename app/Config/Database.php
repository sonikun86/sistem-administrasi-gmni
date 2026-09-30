<?php
// Config/Database.php
declare(strict_types=1);

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $driver = strtolower((string) App::get('DB_CONNECTION', 'sqlite'));

        try {
            if ($driver === 'sqlite') {
                $dbPath = (string) App::get('DB_DATABASE', __DIR__ . '/../../database/database.sqlite');
                
                // Pastikan direktori database ada
                $dir = dirname($dbPath);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                if (!file_exists($dbPath)) {
                    touch($dbPath);
                }

                self::$pdo = new PDO("sqlite:" . $dbPath);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                // Aktifkan foreign keys di SQLite dan WAL mode untuk performa tinggi & concurrency
                self::$pdo->exec("PRAGMA foreign_keys = ON;");
                self::$pdo->exec("PRAGMA journal_mode = WAL;");
            } elseif ($driver === 'mysql') {
                $host = App::get('DB_HOST', '127.0.0.1');
                $port = App::get('DB_PORT', '3306');
                $dbname = App::get('DB_DATABASE', 'simas_gmni');
                $user = App::get('DB_USERNAME', 'root');
                $pass = App::get('DB_PASSWORD', '');

                $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
                self::$pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } else {
                throw new PDOException("Database driver '{$driver}' tidak didukung.");
            }
        } catch (PDOException $e) {
            die("Kesalahan Koneksi Database: " . $e->getMessage());
        }

        return self::$pdo;
    }
}

