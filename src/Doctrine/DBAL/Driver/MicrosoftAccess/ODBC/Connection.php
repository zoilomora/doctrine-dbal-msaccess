<?php
declare(strict_types=1);

namespace ZoiloMora\Doctrine\DBAL\Driver\MicrosoftAccess\ODBC;

final class Connection
{
    private $connection;

    public function __construct(string $dsn, string $user = '', string $password = '')
    {
        $this->connection = \odbc_connect($dsn, $user, $password);

        if (false === $this->connection) {
            throw new \Exception('Connection failure!');
        }
    }

    public function listTableNames(): array
    {
        $tables = \odbc_tables($this->connection);

        $list = [];

        while ($table = \odbc_fetch_array($tables)) {
            $list[] = [
                'Name' => $table['TABLE_NAME'],
                'Type' => $table['TABLE_TYPE'],
            ];
        }

        return $list;
    }

    public function listTableColumns(string $tableName): array
    {
        $columns = \odbc_columns($this->connection, null, '', $tableName);

        $list = [];

        while ($column = \odbc_fetch_array($columns)) {
            $list[] = [
                'name' => $column['COLUMN_NAME'],
                'comment' => $this->sanitizeComment($column['REMARKS']),
                'type' => \strtolower($column['TYPE_NAME']),
                'length' => (int) $column['COLUMN_SIZE'],
                'notnull' => '1' !== $column['NULLABLE'],
                'default' => null,
                'scale' => (int) $column['COLUMN_SIZE'],
                'precision' => (int) $column['COLUMN_SIZE'],
                'autoincrement' => 'COUNTER' === $column['TYPE_NAME'],
            ];
        }

        return $list;
    }

    private function sanitizeComment(?string $comment): ?string
    {
        if (null === $comment) {
            return null;
        }

        $comment = \utf8_encode($comment);

        return \substr($comment, 0, \strpos($comment, "\x00"));
    }
}
