<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Resource\EventDispatcher;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Resource\Event\ResourceEvents;
use Sylius\Component\Resource\Metadata\ResourceMetadataInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourceEventDispatcherSpec extends ObjectBehavior
{
    function let(ResourceMetadataInterface $metadata, EventDispatcherInterface $dispatcher)
    {
        $this->beConstructedwith($metadata, $dispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Resource\EventDispatcher\ResourceEventDispatcher');
    }

    function it_is_a_resource_event_dispatcher()
    {
        $this->shouldImplement('Sylius\Component\Resource\EventDispatcher\ResourceEventDispatcherInterface');
    }

    function it_dispatches_events_for_resources(EventDispatcherInterface $dispatcher, ResourceMetadataInterface $metadata, ResourceInterface $resource)
    {
        $dispatcher->dispatch('sylius.product.pre_create', Argument::type('Sylius\Component\Resource\Event\ResourceEvent'))->shouldBeCalled();

        $metadata->getApplicationName()->shouldBeCalled()->willReturn('sylius');
        $metadata->getResourceName()->shouldBeCalled()->willReturn('product');

        $this->dispatchResourceEvent(ResourceEvents::PRE_CREATE, $resource);
    }
}
