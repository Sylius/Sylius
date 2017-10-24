<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\GridBundle\Doctrine\ORM;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Doctrine\ORM\DataSource;
use Sylius\Component\Grid\Data\DriverInterface;
use Sylius\Component\Grid\Parameters;

final class DriverSpec extends ObjectBehavior
{
    function let(ManagerRegistry $managerRegistry): void
    {
        $this->beConstructedWith($managerRegistry);
    }

    function it_implements_grid_driver(): void
    {
        $this->shouldImplement(DriverInterface::class);
    }

    function it_throws_exception_if_class_is_undefined(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getDataSource', [[], new Parameters()]);
    }

    function it_creates_data_source_via_doctrine_orm_query_builder(
        ManagerRegistry $managerRegistry,
        EntityManagerInterface $entityManager,
        EntityRepository $entityRepository,
        QueryBuilder $queryBuilder
    ): void {
        $managerRegistry->getManagerForClass('App:Book')->willReturn($entityManager);
        $entityManager->getRepository('App:Book')->willReturn($entityRepository);
        $entityRepository->createQueryBuilder('o')->willReturn($queryBuilder);

        $this->getDataSource(['class' => 'App:Book'], new Parameters())->shouldHaveType(DataSource::class);
    }
}
