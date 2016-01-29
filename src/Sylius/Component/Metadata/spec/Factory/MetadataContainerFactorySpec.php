<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Metadata\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Metadata\Factory\MetadataContainerFactory;
use Sylius\Component\Metadata\Factory\MetadataContainerFactoryInterface;
use Sylius\Component\Metadata\Model\MetadataContainer;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @mixin MetadataContainerFactory
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class MetadataContainerFactorySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(MetadataContainer::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Metadata\Factory\MetadataContainerFactory');
    }

    function it_implements_metadata_container_factory_interface()
    {
        $this->shouldImplement(MetadataContainerFactoryInterface::class);
    }

    function it_implements_resource_factory_interface()
    {
        $this->shouldImplement(FactoryInterface::class);
    }

    function it_creates_new_metadata_container()
    {
        $this->createNew()->shouldHaveType(MetadataContainer::class);
    }

    function it_creates_metadata_container_identified_by_given_id()
    {
        $this->createIdentifiedBy('string42')->shouldHaveType(MetadataContainer::class);
        $this->createIdentifiedBy('string42')->getId()->shouldReturn('string42');
    }
}
