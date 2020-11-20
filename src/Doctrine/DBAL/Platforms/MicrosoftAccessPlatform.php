<?php
declare(strict_types=1);

namespace ZoiloMora\Doctrine\DBAL\Platforms;

use Doctrine\DBAL\Platforms\SQLServer2012Platform;
use ZoiloMora\Doctrine\DBAL\Platforms\Keywords\MicrosoftAccessKeywords;

final class MicrosoftAccessPlatform extends SQLServer2012Platform
{
    public function supportsForeignKeyConstraints()
    {
        return false;
    }

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

    protected function getReservedKeywordsClass(): string
    {
        return MicrosoftAccessKeywords::class;
    }
}
