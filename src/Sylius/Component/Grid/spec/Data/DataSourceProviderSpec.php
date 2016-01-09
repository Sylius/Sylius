<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Grid\Data;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Grid\Data\DataSourceProvider;
use Sylius\Component\Grid\Data\DataSourceProviderInterface;
use Sylius\Component\Grid\Data\DriverInterface;
use Sylius\Component\Grid\Data\UnsupportedDriverException;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Registry\ServiceRegistryInterface;

/**
 * @mixin DataSourceProvider
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DataSourceProviderSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $driversRegistry)
    {
        $this->beConstructedWith($driversRegistry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Grid\Data\DataSourceProvider');
    }
    
    function it_implements_grid_data_provider_interface()
    {
        $this->shouldImplement(DataSourceProviderInterface::class);
    }

    function it_uses_a_correct_driver_to_get_the_data_for_a_grid(
        ServiceRegistryInterface $driversRegistry,
        DriverInterface $driver,
        Grid $grid,
        Parameters $parameters
    ) {
        $grid->getDriver()->willReturn('doctrine/orm');
        $grid->getDriverConfiguration()->willReturn(array('resource' => 'sylius.tax_category'));

        $driversRegistry->has('doctrine/orm')->willReturn(true);
        $driversRegistry->get('doctrine/orm')->willReturn($driver);
        $driver->getDataSource(array('resource' => 'sylius.tax_category'), $parameters)->willReturn(array('foo', 'bar'));

        $this->getDataSource($grid, $parameters)->shouldReturn(array('foo', 'bar'));
    }

    function it_throws_an_exception_if_driver_is_not_supported(Grid $grid, Parameters $parameters, ServiceRegistryInterface $driversRegistry)
    {
        $grid->getDriver()->willReturn('doctrine/banana');
        $driversRegistry->has('doctrine/banana')->willReturn(false);
        
        $this
            ->shouldThrow(new UnsupportedDriverException('doctrine/banana'))
            ->during('getDataSource', array($grid, $parameters))
        ;
    }
}
