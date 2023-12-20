<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\QueryHandler;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Loggable\LogEntryInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Query\GetAddressLogEntry;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\ResourceLogEntryRepositoryInterface;

final class GetAddressLogEntryHandlerSpec extends ObjectBehavior
{
    function let(ResourceLogEntryRepositoryInterface $addressLogEntryRepository): void
    {
        $this->beConstructedWith($addressLogEntryRepository);
    }

    function it_returns_address_log_entries_for_a_given_customer(
        ResourceLogEntryRepositoryInterface $addressLogEntryRepository,
        QueryBuilder $queryBuilder,
        AbstractQuery $query,
        LogEntryInterface $addressLogEntryOne,
        LogEntryInterface $addressLogEntryTwo,
    ): void {
        $query->getResult()->willReturn([$addressLogEntryOne, $addressLogEntryTwo]);
        $queryBuilder->getQuery()->willReturn($query);
        $addressLogEntryRepository->createByObjectIdQueryBuilder('3')->willReturn($queryBuilder);

        $this(new GetAddressLogEntry(3))->shouldIterateAs([$addressLogEntryOne, $addressLogEntryTwo]);
    }
}
