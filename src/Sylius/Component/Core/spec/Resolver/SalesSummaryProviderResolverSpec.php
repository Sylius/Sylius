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

namespace spec\Sylius\Component\Core\Resolver;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Provider\MySQLSalesSummaryProvider;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class SalesSummaryProviderResolverSpec extends ObjectBehavior
{
    function let(ServiceLocator $locator): void
    {
        $this->beConstructedWith($locator);
    }

    function it_provides_a_sales_summary_provider_if_it_is_defined(
        Connection $connection,
        AbstractPlatform $abstractPlatform
    ): void {
        $connection->getDatabasePlatform()->willReturn($abstractPlatform);
        $abstractPlatform->getName()->willReturn('mysql');

        $this->getSalesSummaryProvider($connection)->shouldBeAnInstanceOf(MySQLSalesSummaryProvider::class);
    }

    function it_provides_a_default_sales_summary_provider_if_it_is_not_defined(
        Connection $connection,
        AbstractPlatform $abstractPlatform
    ): void {
        $connection->getDatabasePlatform()->willReturn($abstractPlatform);
        $abstractPlatform->getName()->willReturn('mysql');

        $this->getSalesSummaryProvider($connection)->shouldBeAnInstanceOf(MySQLSalesSummaryProvider::class);
    }

}
