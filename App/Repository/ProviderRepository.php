<?php

declare(strict_types=1);

namespace App\Repository;

use App\Provider\Provider;
use PDO;

class ProviderRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return Provider[]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM provider");
        $stmt->execute();

        $results = $stmt->fetchAll();

        $providers = [];
        foreach ($results as $result) {
            $providers[] = new Provider(
                (int) $result['id'],
                $result['source'],
                (int) $result['last_id'],
                $result['url'],
                $result['comment'],
                (bool) $result['is_hidden'],
                (int) $result['pages']
            );
        }

        return $providers;
    }

    public function updateLastId(int $id, int $lastId): void
    {
        $stmt = $this->pdo->prepare("UPDATE provider SET last_id = :lastId WHERE id = :id");
        $stmt->execute(['lastId' => $lastId, 'id' => $id]);
    }
}