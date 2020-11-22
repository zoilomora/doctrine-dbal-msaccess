<?php
declare(strict_types=1);

namespace ZoiloMora\Doctrine\DBAL\Platforms;

use Doctrine\DBAL\Platforms\SQLServer2012Platform;
use ZoiloMora\Doctrine\DBAL\Platforms\Keywords\MicrosoftAccessKeywords;

final class MicrosoftAccessPlatform extends SQLServer2012Platform
{
    /** @link \Doctrine\DBAL\Platforms\AbstractPlatform::getName */
    public function getName(): string
    {
        return 'msaccess';
    }

    /** @link \Doctrine\DBAL\Platforms\AbstractPlatform::supportsForeignKeyConstraints */
    public function supportsForeignKeyConstraints()
    {
        return false;
    }

    /** @link \Doctrine\DBAL\Platforms\SQLServer2005Platform::supportsLimitOffset */
    public function supportsLimitOffset()
    {
        return false;
    }

    /** @link \Doctrine\DBAL\Platforms\AbstractPlatform::initializeDoctrineTypeMappings */
    protected function initializeDoctrineTypeMappings()
    {
        $this->doctrineTypeMapping = [
            'bit' => 'boolean',
            'byte' => 'boolean',
            'counter' => 'bigint',
            'currency' => 'decimal',
            'datetime' => 'datetime',
            'double' => 'float',
            'integer' => 'integer',
            'longbinary' => 'binary',
            'longchar' => 'text',
            'real' => 'float',
            'smallint' => 'smallint',
            'varchar' => 'string',
        ];
    }

    /** @link \Doctrine\DBAL\Platforms\AbstractPlatform::getReservedKeywordsClass */
    protected function getReservedKeywordsClass(): string
    {
        return MicrosoftAccessKeywords::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getDateTimeFormatString()
    {
        return 'Y-m-d H:i';
    }

    /**
     * {@inheritDoc}
     */
    public function getDateFormatString()
    {
        return 'Y-m-d';
    }

    /**
     * {@inheritDoc}
     */
    public function getTimeFormatString()
    {
        return 'H:i:s';
    }
}
