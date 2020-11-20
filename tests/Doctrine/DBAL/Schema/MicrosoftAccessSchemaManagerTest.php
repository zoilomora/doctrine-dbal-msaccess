<?php
declare(strict_types=1);

namespace ZoiloMora\Tests\Doctrine\DBAL\Schema;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use ZoiloMora\Tests\BaseTest;

class MicrosoftAccessSchemaManagerTest extends BaseTest
{
    private const TABLE_NAME = 'Table1';

    /**
     * @test
     * @dataProvider connections
     */
    public function given_an_connection_when_try_list_tables_then_returns_array_tables(Connection $connection)
    {
        $expected = [
            self::TABLE_NAME,
        ];

        $tables = $connection->getSchemaManager()->listTableNames();

        $this->assertIsArray($tables);
        $this->assertSame($expected, $tables);
    }

    /**
     * @test
     * @dataProvider connections
     */
    public function given_an_connection_when_try_list_columns_then_returns_array_columns(Connection $connection)
    {
        $columns = $connection->getSchemaManager()->listTableColumns(self::TABLE_NAME);

        $this->assertIsArray($columns);
        $this->assertCount(5, $columns);
        $this->assertContainsOnlyInstancesOf(Column::class, $columns);
    }

    /**
     * @test
     * @dataProvider connections
     */
    public function given_an_connection_when_try_list_table_details_then_returns_table_object(Connection $connection)
    {
        $tableDetails = $connection->getSchemaManager()->listTableDetails(self::TABLE_NAME);

        $this->assertInstanceOf(Table::class, $tableDetails);
    }

    /**
     * @test
     * @dataProvider connections
     */
    public function given_an_connection_when_get_list_table_foreign_keys_then_returns_an_empty_array(
        Connection $connection
    ) {
        $this->assertEmpty(
            $connection->getSchemaManager()->listTableForeignKeys(self::TABLE_NAME),
        );
    }

    /**
     * @test
     * @dataProvider connections
     */
    public function given_an_connection_when_get_list_table_indexes_then_returns_an_empty_array(Connection $connection)
    {
        $this->assertEmpty(
            $connection->getSchemaManager()->listTableIndexes(self::TABLE_NAME),
        );
    }

    /**
     * @test
     * @dataProvider connections
     */
    public function given_an_connection_when_get_list_views_then_returns_an_empty_array(Connection $connection)
    {
        $this->assertEmpty(
            $connection->getSchemaManager()->listViews(),
        );
    }

    /**
     * @test
     * @dataProvider connections
     */
    public function given_an_connection_when_get_list_databases_then_returns_an_empty_array(Connection $connection)
    {
        $this->assertEmpty(
            $connection->getSchemaManager()->listDatabases(),
        );
    }

    /**
     * @test
     * @dataProvider connections
     */
    public function given_an_connection_when_get_list_namespace_names_then_returns_an_empty_array(
        Connection $connection
    ) {
        $this->assertEmpty(
            $connection->getSchemaManager()->listNamespaceNames(),
        );
    }

    /**
     * @test
     * @dataProvider connections
     */
    public function given_an_connection_when_get_list_sequences_then_returns_an_empty_array(Connection $connection)
    {
        $this->assertEmpty(
            $connection->getSchemaManager()->listSequences(),
        );
    }
}
