<?php

declare(strict_types=1);

namespace Sylius\Tests\Functional;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\SqlWalker\OrderByIdentifierSqlWalker;

class OrderByIdentifierSqlWalkerTest extends AbstractOrmTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $config = $this->getTestOrmConfiguration();
        $this->entityManager = new EntityManager($this->getTestOrmConnection($config), $config);
    }

    /** @test */
    public function it_appends_order_by_identifier_to_the_query(): void
    {
        self::assertStringEndsWith(
            'ORDER BY m0_.id ASC',
            $this->generateSql(
                'select u from Sylius\Tests\Functional\Doctrine\Dump\Model u'
            )
        );

        self::assertStringEndsWith(
            'ORDER BY m0_.email DESC, m0_.id ASC',
            $this->generateSql(
                'select u from Sylius\Tests\Functional\Doctrine\Dump\Model u order by u.email desc'
            )
        );

        self::assertStringEndsWith(
            'ORDER BY m0_.email DESC, m0_.id ASC',
            $this->generateSql(
                'select u.id, (CASE WHEN u.id = 1 THEN \'yolo\' ELSE u.email END) AS HIDDEN yoloOrEmail from Sylius\Tests\Functional\Doctrine\Dump\Model u order by u.email desc'
            )
        );
    }

    /** @test */
    public function it_appends_order_by_identifier_composite_to_the_query(): void
    {
        self::assertStringEndsWith(
            'ORDER BY c0_.email ASC, c0_.organization_name ASC',
            $this->generateSql(
                'select u from Sylius\Tests\Functional\Doctrine\Dump\CompositeKeysModel u'
            )
        );

        self::assertStringEndsWith(
            'ORDER BY c0_.description DESC, c0_.email ASC, c0_.organization_name ASC',
            $this->generateSql(
                'select u from Sylius\Tests\Functional\Doctrine\Dump\CompositeKeysModel u order by u.description desc'
            )
        );

        self::assertStringEndsWith(
            'ORDER BY c0_.description DESC, c0_.email ASC, c0_.organization_name ASC',
            $this->generateSql(
                'select (CASE WHEN u.email = \'admin@example.com\' THEN \'yolo\' ELSE u.email END) AS HIDDEN yoloOrEmail from Sylius\Tests\Functional\Doctrine\Dump\CompositeKeysModel u order by u.description desc'
            )
        );
    }

    /** @test */
    public function it_does_not_append_order_by_identifier_to_the_query_if_query_is_grouped(): void
    {
        self::assertStringEndsWith(
            'GROUP BY m0_.email',
            $this->generateSql(
                'select u from Sylius\Tests\Functional\Doctrine\Dump\Model u group by u.email'
            )
        );
    }

    /** @test */
    public function it_does_not_append_order_by_identifier_to_the_query_if_aggregate_function_is_used(): void
    {
        self::assertStringEndsWith(
            'FROM model m0_',
            $this->generateSql(
                'select max(u) from Sylius\Tests\Functional\Doctrine\Dump\Model u'
            )
        );
    }

    private function generateSql(string $dqlToBeTested): string
    {
        $treeWalkers = [OrderByIdentifierSqlWalker::class];

        return $this->entityManager
            ->createQuery($dqlToBeTested)
            ->setHint(Query::HINT_CUSTOM_TREE_WALKERS, $treeWalkers)
            ->useQueryCache(false)
            ->getSQL()
        ;
    }
}
