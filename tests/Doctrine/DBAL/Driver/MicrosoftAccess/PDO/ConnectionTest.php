<?php
declare(strict_types=1);

namespace ZoiloMora\Tests\Doctrine\DBAL\Driver\MicrosoftAccess\PDO;

use Doctrine\DBAL\Connection;
use ZoiloMora\Tests\BaseTest;

class ConnectionTest extends BaseTest
{
    /**
     * @test
     * @dataProvider connections
     */
    public function given_an_connection_when_try_create_table_then_does_not_return_error(Connection $connection)
    {
        $stmt = $connection->executeQuery('SELECT * FROM Table1');

        $this->assertNotEmpty(
            $stmt->fetchAllAssociative(),
        );
    }

    /**
     * @test
     * @dataProvider connections
     */
    public function given_an_connection_when_try_get_rows_count_then_does_not_return_error(Connection $connection)
    {
        $stmt = $connection->executeQuery('SELECT * FROM Table1');

        $this->assertIsInt(
            $stmt->rowCount(),
        );
    }

    /**
     * @test
     * @dataProvider connections
     */
    public function given_an_connection_when_try_insert_row_then_does_not_return_error(Connection $connection)
    {
        $affectedRows = $connection->insert(
            'Table1',
            [
                'first_name' => 'Ray',
                'last_name' => 'Sanders',
                'birthday' => '05/02/1983',
                'points' => 4,
            ],
        );

        $this->assertIsInt($affectedRows);
        $this->assertSame(1, $affectedRows);
    }
}
