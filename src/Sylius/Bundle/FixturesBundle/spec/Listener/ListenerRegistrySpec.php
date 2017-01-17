<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\FixturesBundle\Listener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\FixturesBundle\Listener\ListenerInterface;
use Sylius\Bundle\FixturesBundle\Listener\ListenerNotFoundException;
use Sylius\Bundle\FixturesBundle\Listener\ListenerRegistry;
use Sylius\Bundle\FixturesBundle\Listener\ListenerRegistryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ListenerRegistrySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\FixturesBundle\Listener\ListenerRegistry');
    }

    function it_implements_listener_registry_interface()
    {
        $this->shouldImplement(ListenerRegistryInterface::class);
    }

    function it_has_a_listener(ListenerInterface $listener)
    {
        $listener->getName()->willReturn('listener_name');

        $this->addListener($listener);

        $this->getListener('listener_name')->shouldReturn($listener);
        $this->getListeners()->shouldReturn(['listener_name' => $listener]);
    }

    function it_throws_an_exception_if_trying_to_another_listener_with_the_same_name(
        ListenerInterface $listener,
        ListenerInterface $anotherListener
    ) {
        $listener->getName()->willReturn('listener_name');
        $anotherListener->getName()->willReturn('listener_name');

        $this->addListener($listener);
        $this->shouldThrow(\InvalidArgumentException::class)->during('addListener', [$listener]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('addListener', [$anotherListener]);
    }

    function it_returns_an_empty_listeners_list_if_it_does_not_have_any_listeners()
    {
        $this->getListeners()->shouldReturn([]);
    }

    function it_throws_an_exception_if_trying_to_get_unexisting_listener_by_name()
    {
        $this->shouldThrow(ListenerNotFoundException::class)->during('getListener', ['listener_name']);
    }
}
