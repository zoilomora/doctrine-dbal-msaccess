<?php
declare(strict_types=1);

namespace ZoiloMora\Doctrine\DBAL\Platforms;

use Doctrine\DBAL\Platforms\SQLServer2012Platform;
use ZoiloMora\Doctrine\DBAL\Platforms\Keywords\MicrosoftAccessKeywords;

final class MicrosoftAccessPlatform extends SQLServer2012Platform
{
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

    protected function getReservedKeywordsClass(): string
    {
        return MicrosoftAccessKeywords::class;
    }

    /**
     * {@inheritDoc}
     *
     * @see \Doctrine\DBAL\Platforms\SQLServerPlatform::doModifyLimitQuery
     */
    protected function doModifyLimitQuery($query, $limit, $offset = null)
    {
        $where = [];

        if ($offset > 0) {
            $where[] = \sprintf('doctrine_rownum >= %d', $offset + 1);
        }

        if (null !== $limit) {
            $where[] = \sprintf('doctrine_rownum <= %d', $offset + $limit);
            $top = \sprintf('TOP %d', $offset + $limit);
        } else {
            $top = 'TOP 9223372036854775807';
        }

        if (0 === \count($where)) {
            return $query;
        }

        if (!\preg_match('/^(\s*SELECT\s+(?:DISTINCT\s+)?)(.*)$/is', $query, $matches)) {
            return $query;
        }

        $query = $matches[1] . $top . ' ' . $matches[2];

        if (\stristr($query, 'ORDER BY')) {
            $query = $this->scrubInnerOrderBy($query);
        }

        return $query;
    }

    /**
     * {@inheritDoc}
     *
     * @see \Doctrine\DBAL\Platforms\SQLServerPlatform::scrubInnerOrderBy
     */
    private function scrubInnerOrderBy(string $query): string
    {
        $count = \substr_count(\strtoupper($query), 'ORDER BY');
        $offset = 0;

        while ($count-- > 0) {
            $orderByPos = \stripos($query, ' ORDER BY', $offset);
            if (false === $orderByPos) {
                break;
            }

            $qLen = \strlen($query);
            $parenCount = 0;
            $currentPosition = $orderByPos;

            while ($parenCount >= 0 && $currentPosition < $qLen) {
                if ('(' === $query[$currentPosition]) {
                    $parenCount++;
                }

                if (')' === $query[$currentPosition]) {
                    $parenCount--;
                }

                $currentPosition++;
            }

            if ($this->isOrderByInTopNSubquery($query, $orderByPos)) {
                // If the order by clause is in a TOP N subquery, do not remove
                // it and continue iteration from the current position.
                $offset = $currentPosition;

                continue;
            }

            if ($currentPosition >= $qLen - 1) {
                continue;
            }

            $query = \substr($query, 0, $orderByPos) . \substr($query, $currentPosition - 1);
            $offset = $orderByPos;
        }

        return $query;
    }

    /**
     * {@inheritDoc}
     *
     * @see \Doctrine\DBAL\Platforms\SQLServerPlatform::isOrderByInTopNSubquery
     */
    private function isOrderByInTopNSubquery(string $query, int $currentPosition): bool
    {
        $subQueryBuffer = '';
        $parenCount = 0;

        while ($parenCount >= 0 && $currentPosition >= 0) {
            if ('(' === $query[$currentPosition]) {
                $parenCount--;
            }

            if (')' === $query[$currentPosition]) {
                $parenCount++;
            }

            $subQueryBuffer = (0 === $parenCount ? $query[$currentPosition] : ' ') . $subQueryBuffer;

            $currentPosition--;
        }

        return (bool)\preg_match('/SELECT\s+(DISTINCT\s+)?TOP\s/i', $subQueryBuffer);
    }
}
