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
use Sylius\Bundle\AddressingBundle\Factory\ZoneFactory;
use Sylius\Component\Addressing\Model\Zone;
use Sylius\Component\Resource\Factory\Factory;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ZoneFactorySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(Zone::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ZoneFactory::class);
    }

    function it_extends_factory()
    {
        $this->shouldHaveType(Factory::class);
    }

    function it_creates_zone_with_type()
    {
        $this->createTyped('country')->shouldHaveType(Zone::class);
    }
}
