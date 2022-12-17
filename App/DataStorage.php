<?php

declare(strict_types=1);

namespace App;

use App\Provider\Provider;
use PDO;

class DataStorage
{
    private const FILE = 'providers.txt';

    private const DELIMITER = '|||';

    private const HIDDEN_SYMBOL = '#';

    private PDO $pdo;

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    /**
     * @return Provider[]
     */
    public function read(): array
    {
        $providers = [];




        $providers = [];

        $fp = fopen(self::FILE, "r");

        while (($buffer = fgets($fp)) !== false) {
            $isHidden = false;
            if ($buffer[0] === self::HIDDEN_SYMBOL) {
                $isHidden = true;
                $buffer = substr($buffer, 1);
            }

            $comment = null;
            if (preg_match('/\/\*.*\*\//', $buffer, $matches)) {
                $comment = $matches[0];
                $buffer = str_replace($matches[0], '', $buffer);
            }

            $data = explode(self::DELIMITER, $buffer);

            $source = Provider::SOURCE_MY_HOME;
            if (stripos($data[1], 'https://ss.ge') !== false) {
                $source = Provider::SOURCE_SS;
            } elseif (stripos($data[1], 'https://m2rent.ge') !== false) {
                $source = Provider::SOURCE_M2;
            }

            $providers[] = new Provider(
                $source,
                (int) $data[0],
                $data[1],
                $comment,
                $isHidden,
                2
            );
        }

        fclose($fp);

        return $providers;
    }

    /**
     * @param Provider[] $providers
     */
    public function write(array $providers): void
    {
        $fp = fopen(self::FILE, 'w+');

        foreach ($providers as $provider) {
            $buffer = sprintf(
                "%s%s%s%s%s",
                $provider->isHidden() ? self::HIDDEN_SYMBOL : '',
                $provider->getComment() ? $provider->getComment() : '',
                $provider->getLastId(),
                self::DELIMITER,
                $provider->getUrl()
            );

            fwrite($fp, $buffer);
        }

        fclose($fp);
    }
}