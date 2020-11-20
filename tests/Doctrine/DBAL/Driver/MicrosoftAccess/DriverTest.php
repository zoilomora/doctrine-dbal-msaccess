<?php
declare(strict_types=1);

namespace ZoiloMora\Tests\Doctrine\DBAL\Driver\MicrosoftAccess;

use Doctrine\DBAL\Connection;
use ZoiloMora\Tests\BaseTest;

class DriverTest extends BaseTest
{
    /**
     * @test
     * @dataProvider connections
     */
    public function given_an_connection_when_get_database_name_then_returns_an_empty_text(Connection $connection)
    {
        $this->assertSame(
            'unknown',
            $connection->getDatabase(),
        );
    }

    /**
     * @test
     * @dataProvider connections
     */
    public function given_an_connection_when_try_get_last_insert_id_then_does_not_return_error(Connection $connection)
    {
        $this->assertSame(
            '0',
            $connection->lastInsertId(),
        );
    }
}
