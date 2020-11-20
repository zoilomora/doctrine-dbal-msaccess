<?php
declare(strict_types=1);

namespace ZoiloMora\Tests;

use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase;
use ZoiloMora\Doctrine\DBAL\Driver\MicrosoftAccess\Driver;

abstract class BaseTest extends TestCase
{
    public function connections(): \Iterator
    {
        if (false === $this->isMicrosoftWindows()) {
            throw new \Exception('It is necessary to run the tests in Microsoft Windows');
        }

        $this->createDatabase();

        $params = [
            'driverClass' => Driver::class,
            'dsn' => 'dbal-msaccess',
        ];

        $connection = DriverManager::getConnection($params);

        try {
            $connection->connect();
        } catch (\Doctrine\DBAL\Exception $exception) {
            throw new \Exception(
                "You should create a DSN pointing to 'var\database.mdb' called 'dbal-msaccess'",
            );
        }

        yield [$connection];
    }

    private function isMicrosoftWindows(): bool
    {
        return false !== \strpos(\php_uname(), 'Windows NT');
    }

    private function createDatabase(): string
    {
        $ds = \DIRECTORY_SEPARATOR;
        $root = __DIR__ . $ds . '..' . $ds;

        $source = $root . 'tests' . $ds . 'database.mdb';
        $dest = $root . 'var' . $ds . 'database.mdb';

        \copy($source, $dest);

        return $dest;
    }
}
