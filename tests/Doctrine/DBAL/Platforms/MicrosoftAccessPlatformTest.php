<?php
declare(strict_types=1);

namespace ZoiloMora\Tests\Doctrine\DBAL\Platforms;

use Doctrine\DBAL\Connection;
use ZoiloMora\Tests\BaseTest;

class MicrosoftAccessPlatformTest extends BaseTest
{
    private const TABLE_NAME = 'Table1';

    /**
     * @test
     * @dataProvider connections
     */
    public function given_an_connection_when_get_the_first_two_rows_then_returns_array_with_two_rows(Connection $connection)
    {
        $result = $connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE_NAME)
            ->setMaxResults(2)
            ->execute();

        $rows = $result->fetchAllAssociative();

        $this->assertIsArray($rows);
        $this->assertCount(2, $rows);
    }
}
