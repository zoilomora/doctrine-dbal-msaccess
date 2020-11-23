<?php
declare(strict_types=1);

namespace ZoiloMora\Doctrine\DBAL\Platforms;

use Doctrine\DBAL\Platforms\SQLServer2012Platform;
use ZoiloMora\Doctrine\DBAL\Platforms\Keywords\MicrosoftAccessKeywords;

final class MicrosoftAccessPlatform extends SQLServer2012Platform
{
    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'msaccess';
    }

    /**
     * {@inheritDoc}
     */
    public function supportsForeignKeyConstraints()
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsLimitOffset()
    {
        return false;
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

    /**
     * {@inheritDoc}
     */
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

    /**
     * {@inheritDoc}
     */
    protected function getReservedKeywordsClass(): string
    {
        return MicrosoftAccessKeywords::class;
    }
}
