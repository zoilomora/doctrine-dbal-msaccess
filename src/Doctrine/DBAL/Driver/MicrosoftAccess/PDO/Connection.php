<?php
declare(strict_types=1);

namespace ZoiloMora\Doctrine\DBAL\Driver\MicrosoftAccess\PDO;

use Doctrine\DBAL\Driver\PDO\Connection as PDOConnection;
use Doctrine\DBAL\Driver\PDO\SQLSrv\Statement;

final class Connection extends PDOConnection
{
    private ?bool $transactionsSupport = null;

    public function __construct($dsn, $user = null, $password = null, $options = null)
    {
        parent::__construct($dsn, $user, $password, $options);

        $this->setAttribute(
            \PDO::ATTR_STATEMENT_CLASS,
            [
                Statement::class,
                [],
            ],
        );
    }

    public function lastInsertId($name = null): string
    {
        return '0';
    }

    public function quote($value, $type = \PDO::PARAM_STR)
    {
        $val = parent::quote($value, $type);

        // Fix for a driver version terminating all values with null byte
        if (false !== \strpos($val, "\0")) {
            $val = \substr($val, 0, -1);
        }

        return $val;
    }

    public function beginTransaction()
    {
        return true === $this->transactionsSupported()
            ? parent::beginTransaction()
            : $this->exec('BEGIN TRANSACTION');
    }

    public function commit()
    {
        return true === $this->transactionsSupported()
            ? parent::commit()
            : $this->exec('COMMIT TRANSACTION');
    }

    public function rollback()
    {
        return true === $this->transactionsSupported()
            ? parent::rollback()
            : $this->exec('ROLLBACK TRANSACTION');
    }

    private function transactionsSupported(): bool
    {
        if (null !== $this->transactionsSupport) {
            return $this->transactionsSupport;
        }

        try {
            parent::beginTransaction();

            parent::commit();

            $this->transactionsSupport = true;
        } catch (\PDOException $e) {
            $this->transactionsSupport = false;
        }

        return $this->transactionsSupport;
    }
}
