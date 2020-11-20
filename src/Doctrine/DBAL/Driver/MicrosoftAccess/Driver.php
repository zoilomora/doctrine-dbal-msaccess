<?php
declare(strict_types=1);

namespace ZoiloMora\Doctrine\DBAL\Driver\MicrosoftAccess;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use ZoiloMora\Doctrine\DBAL\Driver\MicrosoftAccess\PDO\Connection as PDOConnection;
use ZoiloMora\Doctrine\DBAL\Driver\MicrosoftAccess\ODBC\Connection as ODBCConnection;
use ZoiloMora\Doctrine\DBAL\Platforms\MicrosoftAccessPlatform;
use ZoiloMora\Doctrine\DBAL\Schema\MicrosoftAccessSchemaManager;

final class Driver implements \Doctrine\DBAL\Driver
{
    protected ?ODBCConnection $odbcConnection = null;

    public function connect(
        array $params,
        $username = null,
        $password = null,
        array $driverOptions = []
    ): \Doctrine\DBAL\Driver\Connection {
        $this->assertRequiredParameters($params);

        try {
            $conn = new PDOConnection(
                $this->constructPdoDsn($params),
                $username,
                $password,
                $driverOptions,
            );
            $this->odbcConnection = new ODBCConnection(
                $this->constructOdbcDsn($params),
            );
        } catch (\PDOException $e) {
            throw Exception::driverException($this, $e);
        }

        return $conn;
    }

    public function getName(): string
    {
        return 'pdo_msaccess';
    }

    public function getDatabase(\Doctrine\DBAL\Connection $conn): string
    {
        return 'unknown';
    }

    public function getDatabasePlatform(): AbstractPlatform
    {
        return new MicrosoftAccessPlatform();
    }

    public function getSchemaManager(\Doctrine\DBAL\Connection $conn): AbstractSchemaManager
    {
        return new MicrosoftAccessSchemaManager($conn, $this->odbcConnection);
    }

    private function assertRequiredParameters(array $params): void
    {
        if (false === \array_key_exists('dsn', $params)) {
            throw new \Exception("The parameter 'dsn' is mandatory");
        }
    }

    protected function constructPdoDsn(array $params): string
    {
        $dsn = 'odbc:';

        if (isset($params['dsn']) && '' !== $params['dsn']) {
            return $dsn . $params['dsn'];
        }

        return $dsn;
    }

    protected function constructOdbcDsn(array $params): string
    {
        $dsn = '';

        if (isset($params['dsn']) && '' !== $params['dsn']) {
            return $dsn . $params['dsn'];
        }

        return $dsn;
    }
}
