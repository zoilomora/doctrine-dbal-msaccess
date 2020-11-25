<?php
declare(strict_types=1);

namespace ZoiloMora\Doctrine\DBAL\Driver\MicrosoftAccess\PDO;

use Doctrine\DBAL\Driver\Connection as ConnectionInterface;
use Doctrine\DBAL\Driver\ServerInfoAwareConnection;
use Doctrine\DBAL\ParameterType;
use PDO;
use PDOStatement;
use ZoiloMora\Doctrine\DBAL\Driver\MicrosoftAccess\Statement;

final class Connection extends PDO implements ConnectionInterface, ServerInfoAwareConnection
{
    private ?bool $transactionsSupport = null;

    /**
     * {@inheritdoc}
     */
    public function __construct($dsn, $user = null, $password = null, ?array $options = null)
    {
        parent::__construct($dsn, (string)$user, (string)$password, (array)$options);

        $this->setAttribute(
            PDO::ATTR_STATEMENT_CLASS,
            [
                Statement::class,
                [
                    true === \array_key_exists('charset', $options)
                        ? $options['charset']
                        : null,
                ],
            ],
        );
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getServerVersion(): string
    {
        return PDO::getAttribute(PDO::ATTR_SERVER_VERSION);
    }

    /**
     * {@inheritdoc}
     */
    public function quote($value, $type = ParameterType::STRING)
    {
        $val = parent::quote($value, $type);

        // Fix for a driver version terminating all values with null byte
        if (false !== \strpos($val, "\0")) {
            $val = \substr($val, 0, -1);
        }

        return $val;
    }

    /**
     * {@inheritdoc}
     */
    public function lastInsertId($name = null): string
    {
        return '0';
    }

    public function requiresQueryForServerVersion(): bool
    {
        return false;
    }

    public function beginTransaction(): bool
    {
        return true === $this->transactionsSupported()
            ? parent::beginTransaction()
            : $this->exec('BEGIN TRANSACTION');
    }

    public function commit(): bool
    {
        return true === $this->transactionsSupported()
            ? parent::commit()
            : $this->exec('COMMIT TRANSACTION');
    }

    public function rollback(): bool
    {
        return true === $this->transactionsSupported()
            ? parent::rollback()
            : $this->exec('ROLLBACK TRANSACTION');
    }

    /**
     * {@inheritdoc}
     */
    public function query(...$args): PDOStatement
    {
        return parent::query(...$args);
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
