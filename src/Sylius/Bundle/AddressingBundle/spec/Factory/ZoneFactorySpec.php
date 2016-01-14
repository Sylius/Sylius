<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AddressingBundle\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\AddressingBundle\Factory\ZoneFactoryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ZoneFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $factory)
    {
        $this->beConstructedWith($factory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Factory\ZoneFactory');
    }

    function it_implements_factory_interface()
    {
        $this->shouldImplement(FactoryInterface::class);
    }

    function it_implements_zone_factory_interface()
    {
        $this->shouldImplement(ZoneFactoryInterface::class);
    }

    function it_creates_zone_with_type(FactoryInterface $factory, ZoneInterface $zone)
    {
        $factory->createNew()->willReturn($zone);
        $zone->setType('country')->shouldBeCalled();

        $this->createTyped('country')->shouldReturn($zone);
    }
}
