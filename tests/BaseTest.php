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
            'dsn' => $this->dsn(),
            'driverOptions' => $this->driverOptions(),
        ];

        $connection = DriverManager::getConnection($params);

        try {
            $connection->connect();
        } catch (\Doctrine\DBAL\Exception $exception) {
            throw new \Exception(
                \sprintf(
                    "You should create a DSN pointing to 'var\%s' called '%s'",
                    $this->filename(),
                    $this->dsn(),
                ),
            );
        }

        yield [$connection];
    }

    protected function filename(): string
    {
        return 'default.mdb';
    }

    protected function dsn(): string
    {
        return 'dbal-msaccess';
    }

    protected function driverOptions(): array
    {
        return [];
    }

    private function isMicrosoftWindows(): bool
    {
        return false !== \strpos(\php_uname(), 'Windows NT');
    }

    private function createDatabase(): string
    {
        $ds = \DIRECTORY_SEPARATOR;
        $root = __DIR__ . $ds . '..' . $ds;

        $source = $root . 'tests' . $ds . 'Databases' . $ds . $this->filename();
        $dest = $root . 'var' . $ds . $this->filename();

        \copy($source, $dest);

        return $dest;
    }
}
