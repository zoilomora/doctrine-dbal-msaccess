<?php
declare(strict_types=1);

namespace ZoiloMora\Doctrine\DBAL\Driver\MicrosoftAccess;

use Doctrine\DBAL\ParameterType;

final class Statement extends \Doctrine\DBAL\Driver\PDO\Statement
{
    private const FROM_ENCODING = 'Windows-1252';

    private ?string $charset;

    protected function __construct(?string $charset = null)
    {
        $this->charset = $charset;
    }

    public function bindParam($param, &$variable, $type = ParameterType::STRING, $length = null, $driverOptions = null)
    {
        switch ($type) {
            case ParameterType::LARGE_OBJECT:
            case ParameterType::BINARY:
                if ($driverOptions === null) {
                    $driverOptions = \PDO::SQLSRV_ENCODING_BINARY;
                }

                break;

            case ParameterType::ASCII:
                $type          = ParameterType::STRING;
                $length        = 0;
                $driverOptions = \PDO::SQLSRV_ENCODING_SYSTEM;
                break;
        }

        return parent::bindParam($param, $variable, $type, $length, $driverOptions);
    }

    public function bindValue($param, $value, $type = ParameterType::STRING)
    {
        return $this->bindParam($param, $value, $type);
    }

    public function fetchOne()
    {
        return $this->convertStringEncoding(
            parent::fetchOne()
        );
    }

    public function fetchNumeric()
    {
        return $this->convertArrayEncoding(
            parent::fetchNumeric()
        );
    }

    public function fetchAssociative()
    {
        return $this->convertArrayEncoding(
            parent::fetchAssociative()
        );
    }

    public function fetchAllNumeric(): array
    {
        return $this->convertCollectionEncoding(
            parent::fetchAllNumeric()
        );
    }

    public function fetchFirstColumn(): array
    {
        return $this->convertArrayEncoding(
            parent::fetchFirstColumn()
        );
    }

    public function fetchAllAssociative(): array
    {
        return $this->convertCollectionEncoding(
            parent::fetchAllAssociative()
        );
    }

    private function convertCollectionEncoding(array $items): array
    {
        \array_walk(
            $items,
            function (&$item) {
                $item = $this->convertArrayEncoding($item);
            }
        );

        return $items;
    }

    private function convertArrayEncoding(array $items): array
    {
        foreach ($items as $key => $value) {
            $items[$key] = $this->convertStringEncoding($items[$key]);
        }

        return $items;
    }

    private function convertStringEncoding(?string $value): ?string
    {
        if (null === $this->charset) {
            return $value;
        }

        if (null === $value) {
            return null;
        }

        return \mb_convert_encoding($value, $this->charset, self::FROM_ENCODING);
    }
}
