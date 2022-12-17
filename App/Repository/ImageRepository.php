<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

class ImageRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAllByHash(string $hash): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM image WHERE hash = :hash");
        $stmt->execute(['hash' => $hash]);

        return $stmt->fetchAll();
    }

    public function add(string $hash, string $source): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO image (hash, source) VALUES (:hash, :source)");
        $stmt->execute(['hash' => $hash, 'source' => $source]);
    }
}