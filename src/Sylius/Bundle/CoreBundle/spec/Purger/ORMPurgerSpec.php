<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Purger;

use Doctrine\Common\DataFixtures\Purger\PurgerInterface;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Purger\QueryLoggerInterface;

/**
 * @mixin \Sylius\Bundle\CoreBundle\Purger\ORMPurger
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ORMPurgerSpec extends ObjectBehavior
{
    function let(PurgerInterface $purger, EntityManagerInterface $entityManager, QueryLoggerInterface $queryLogger)
    {
        $this->beConstructedWith($purger, $entityManager, $queryLogger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Purger\ORMPurger');
    }

    function it_implements_Doctrine_Purger_interface()
    {
        $this->shouldImplement(PurgerInterface::class);
    }

    function it_uses_doctrine_orm_purger_for_the_first_time(
        PurgerInterface $purger,
        EntityManagerInterface $entityManager,
        QueryLoggerInterface $queryLogger,
        Connection $connection,
        Configuration $configuration
    ) {
        $entityManager->getConnection()->willReturn($connection);
        $connection->getConfiguration()->willReturn($configuration);

        $configuration->setSQLLogger($queryLogger)->shouldBeCalled();
        $purger->purge()->shouldBeCalled();

        $queryLogger->getLoggedQueries()->willReturn([[
            'sql' => 'SQL QUERY',
            'params' => [],
            'types' => [],
        ]]);
        $queryLogger->clearLoggedQueries()->shouldBeCalled();

        $this->purge();
    }

    function it_executes_cached_queries_after_the_first_purging(
        PurgerInterface $purger,
        EntityManagerInterface $entityManager,
        QueryLoggerInterface $queryLogger,
        Connection $connection,
        Configuration $configuration
    ) {
        $entityManager->getConnection()->willReturn($connection);
        $connection->getConfiguration()->willReturn($configuration);

        $configuration->setSQLLogger($queryLogger)->shouldBeCalled();
        $purger->purge()->shouldBeCalled();

        $queryLogger->getLoggedQueries()->willReturn([[
            'sql' => 'SQL QUERY',
            'params' => [],
            'types' => [],
        ]]);
        $queryLogger->clearLoggedQueries()->shouldBeCalled();

        $this->purge();

        $configuration->setSQLLogger(null)->shouldBeCalled();

        $connection->executeUpdate('SQL QUERY', [], [])->shouldBeCalled();

        $this->purge();
    }
}
