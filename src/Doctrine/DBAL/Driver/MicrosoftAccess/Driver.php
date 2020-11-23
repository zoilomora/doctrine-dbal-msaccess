<?php
declare(strict_types=1);

namespace ZoiloMora\Doctrine\DBAL\Driver\MicrosoftAccess;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use PDOException;
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
        $this->assertRequiredDriverOptions($driverOptions);

        try {
            $conn = new PDOConnection(
                $this->constructPdoDsn($driverOptions),
                $username,
                $password,
                $driverOptions,
            );
            $this->odbcConnection = new ODBCConnection(
                $this->constructOdbcDsn($driverOptions),
            );
        } catch (PDOException $e) {
            throw Exception::driverException($this, $e);
        }

        return $conn;
    }

    public function getName(): string
    {
        return 'pdo_msaccess';
    }

    public function getDatabase(Connection $conn): string
    {
        return 'unknown';
    }

    public function getDatabasePlatform(): AbstractPlatform
    {
        return new MicrosoftAccessPlatform();
    }

    public function getSchemaManager(Connection $conn): AbstractSchemaManager
    {
        return new MicrosoftAccessSchemaManager($conn, $this->odbcConnection);
    }

    private function assertRequiredDriverOptions(array $driverOptions): void
    {
        if (false === \array_key_exists('dsn', $driverOptions)) {
            throw new Exception\InvalidArgumentException("The driver option 'dsn' is mandatory");
        }
    }

    protected function constructPdoDsn(array $driverOptions): string
    {
        return 'odbc:' . $this->getDsn($driverOptions);
    }

    protected function constructOdbcDsn(array $driverOptions): string
    {
        return $this->getDsn($driverOptions);
    }

    private function getDsn(array $driverOptions): string
    {
        return $driverOptions['dsn'];
    }
}
