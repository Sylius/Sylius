<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Resource\Event;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Metadata\ResourceMetadataInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourceEventSpec extends ObjectBehavior
{
    function let(ResourceInterface $resource, ResourceMetadataInterface $metadata)
    {
        $this->beConstructedwith($resource, $metadata);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Resource\Event\ResourceEvent');
    }

    function it_is_an_event()
    {
        $this->shouldHaveType('Symfony\Component\EventDispatcher\Event');
    }

    function it_returns_the_resource($resource)
    {
        $this->getResource()->shouldReturn($resource);
    }
}
