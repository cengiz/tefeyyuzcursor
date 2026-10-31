<?php
declare(strict_types=1);

namespace Tefeyyuz\Support;

use PDO;
use PDOException;
use PDOStatement;

final class Database
{
    private PDO $pdo;

    public function __construct(string $path, private readonly Logger $logger)
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $this->pdo = new PDO('sqlite:' . $path, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        $this->pdo->exec('PRAGMA foreign_keys = ON');
        $this->pdo->exec('PRAGMA journal_mode = WAL');
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }

    /** @param array<string, mixed> $params */
    public function all(string $sql, array $params = []): array
    {
        return $this->run($sql, $params)->fetchAll();
    }

    /** @param array<string, mixed> $params */
    public function one(string $sql, array $params = []): ?array
    {
        $row = $this->run($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    /** @param array<string, mixed> $params */
    public function execute(string $sql, array $params = []): int
    {
        return $this->run($sql, $params)->rowCount();
    }

    public function lastId(): int
    {
        return (int) $this->pdo->lastInsertId();
    }

    /** @param array<string, mixed> $params */
    private function run(string $sql, array $params): PDOStatement
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $value) {
                $name = str_starts_with((string) $key, ':') ? (string) $key : ':' . $key;
                $type = match (true) {
                    is_int($value) => PDO::PARAM_INT,
                    is_bool($value) => PDO::PARAM_BOOL,
                    $value === null => PDO::PARAM_NULL,
                    default => PDO::PARAM_STR,
                };
                $stmt->bindValue($name, $value, $type);
            }
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            $this->logger->error('Veritabanı sorgusu başarısız', [
                'sql' => $sql,
                'hata' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
