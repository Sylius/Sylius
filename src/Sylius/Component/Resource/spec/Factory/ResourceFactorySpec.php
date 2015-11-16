<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Resource\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

require_once __DIR__.'/../FakeResource.php';

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourceFactorySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedwith('spec\Sylius\Component\Resource\FakeResource');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Resource\Factory\ResourceFactory');
    }

    function it_is_a_resource_factory()
    {
        $this->shouldImplement('Sylius\Component\Resource\Factory\ResourceFactoryInterface');
    }

    function it_creates_new_instance()
    {
        $this->createNew()->shouldReturnAnInstanceOf('spec\Sylius\Component\Resource\FakeResource');
    }
}
