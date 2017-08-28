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

namespace spec\Sylius\Component\Grid\Data;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\DataSourceProviderInterface;
use Sylius\Component\Grid\Data\DriverInterface;
use Sylius\Component\Grid\Data\UnsupportedDriverException;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Registry\ServiceRegistryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class DataSourceProviderSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $driversRegistry): void
    {
        $this->beConstructedWith($driversRegistry);
    }

    function it_implements_grid_data_provider_interface(): void
    {
        $this->shouldImplement(DataSourceProviderInterface::class);
    }

    function it_uses_a_correct_driver_to_get_the_data_for_a_grid(
        ServiceRegistryInterface $driversRegistry,
        DataSourceInterface $dataSource,
        DriverInterface $driver,
        Grid $grid
    ): void {
        $parameters = new Parameters();

        $grid->getDriver()->willReturn('doctrine/orm');
        $grid->getDriverConfiguration()->willReturn(['resource' => 'sylius.tax_category']);

        $driversRegistry->has('doctrine/orm')->willReturn(true);
        $driversRegistry->get('doctrine/orm')->willReturn($driver);
        $driver->getDataSource(['resource' => 'sylius.tax_category'], $parameters)->willReturn($dataSource);

        $this->getDataSource($grid, $parameters)->shouldReturn($dataSource);
    }

    function it_throws_an_exception_if_driver_is_not_supported(Grid $grid, ServiceRegistryInterface $driversRegistry): void
    {
        $parameters = new Parameters();

        $grid->getDriver()->willReturn('doctrine/banana');
        $driversRegistry->has('doctrine/banana')->willReturn(false);

        $this
            ->shouldThrow(new UnsupportedDriverException('doctrine/banana'))
            ->during('getDataSource', [$grid, $parameters])
        ;
    }
}
