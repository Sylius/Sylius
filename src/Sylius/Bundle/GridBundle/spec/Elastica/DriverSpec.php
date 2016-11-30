<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\GridBundle\Elastica;

use Elastica\Index;
use Elastica\SearchableInterface;
use Sylius\Bundle\GridBundle\Elastica\DataSource;
use Sylius\Bundle\GridBundle\Elastica\Driver;
use Sylius\Component\Grid\Data\DriverInterface;
use Sylius\Component\Grid\Parameters;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin Driver
 *
 * @author Nicolas Adler <nicolas.adler@openizi.com>
 */
class DriverSpec extends ObjectBehavior
{
    function let(Index $index)
    {
        $this->beConstructedWith($index);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Driver::class);
    }

    function it_implements_grid_driver()
    {
        $this->shouldImplement(DriverInterface::class);
    }

    function it_throws_exception_if_type_is_undefined(Parameters $parameters)
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getDataSource', [[], $parameters]);
        ;
    }

    function it_creates_data_source_via_elastica_type(
        Index $index,
        SearchableInterface $type,
        Parameters $parameters
    ) {
        $index->getType('App:Book')->willReturn($type);

        $this->getDataSource(['type' => 'App:Book'], $parameters)->shouldHaveType(DataSource::class);
    }
}
