<?php
declare(strict_types=1);

namespace ZoiloMora\Tests\Doctrine\DBAL\Driver\MicrosoftAccess;

use Doctrine\DBAL\Connection;
use ZoiloMora\Tests\BaseTest;

class StatementTest extends BaseTest
{
    private const TABLE_NAME = 'test';

    protected function filename(): string
    {
        return 'Iurie-popovt-test.accdb';
    }

    protected function dsn(): string
    {
        return 'Iurie-popovt-test';
    }

    protected function driverOptions(): array
    {
        return [
            'charset' => 'UTF-8',
        ];
    }

    /**
     * @test
     * @dataProvider connections
     */
    public function given_a_french_database_when_has_driver_options_then_it_appears_in_utf8(Connection $connection)
    {
        $result = $connection
            ->createQueryBuilder()
            ->select('note')
            ->from(self::TABLE_NAME)
            ->execute();

        $item = $result->fetchAllAssociative()[0];

        $this->assertSame('Matériel Prêt un mail est envoyé', $item['note']);
    }
}
