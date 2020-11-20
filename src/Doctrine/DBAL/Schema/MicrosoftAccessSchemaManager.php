<?php
declare(strict_types=1);

namespace ZoiloMora\Doctrine\DBAL\Schema;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\SQLServerSchemaManager;
use Doctrine\DBAL\Schema\Table;
use ZoiloMora\Doctrine\DBAL\Driver\MicrosoftAccess\ODBC\Connection as ODBCConnection;

final class MicrosoftAccessSchemaManager extends SQLServerSchemaManager
{
    private ODBCConnection $odbcConnection;

    public function __construct(Connection $pdoConnection, ODBCConnection $odbcConnection)
    {
        parent::__construct($pdoConnection, null);

        $this->odbcConnection = $odbcConnection;
    }

    public function listTableColumns($table, $database = null): array
    {
        $tableColumns = $this->odbcConnection->listTableColumns($table);

        return parent::_getPortableTableColumnList($table, $database, $tableColumns);
    }

    public function listTableNames(): array
    {
        $tables = $this->odbcConnection->listTableNames();

        $tableNames = $this->_getPortableTablesList($tables);

        return parent::filterAssetNames($tableNames);
    }

    public function listTableForeignKeys($table, $database = null): array
    {
        return [];
    }

    public function listTableIndexes($table): array
    {
        return [];
    }

    public function listViews(): array
    {
        return [];
    }

    public function listDatabases(): array
    {
        return [];
    }

    public function listNamespaceNames(): array
    {
        return [];
    }

    public function listSequences($database = null): array
    {
        return [];
    }

    public function listTableDetails($name): Table
    {
        $columns = $this->listTableColumns($name);
        $foreignKeys = [];

        if ($this->_platform->supportsForeignKeyConstraints()) {
            $foreignKeys = $this->listTableForeignKeys($name);
        }

        $indexes = $this->listTableIndexes($name);

        return new Table($name, $columns, $indexes, $foreignKeys);
    }

    protected function _getPortableTablesList($tables): array
    {
        $list = [];

        foreach ($tables as $value) {
            if ('SYSTEM TABLE' === $value['Type']) {
                continue;
            }

            $list[] = $value['Name'];
        }

        return $list;
    }
}
