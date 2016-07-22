<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Cart\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Cart\Context\CartContextInterface;
use Sylius\Component\Cart\Context\CartNotFoundException;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Registry\PrioritizedServiceRegistryInterface;
use Zend\Stdlib\PriorityQueue;
use Zend\Stdlib\SplPriorityQueue;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class CompositeCartContextSpec extends ObjectBehavior
{
    function let(PrioritizedServiceRegistryInterface $prioritizedServiceRegistry)
    {
        $this->beConstructedWith($prioritizedServiceRegistry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Cart\Context\CompositeCartContext');
    }

    function it_implements_cart_context_interface()
    {
        $this->shouldImplement(CartContextInterface::class);
    }

    function it_throws_cart_not_found_exception_if_there_are_no_nested_cart_contexts_defined(
        PrioritizedServiceRegistryInterface $prioritizedServiceRegistry,
        PriorityQueue $priorityQueue,
        SplPriorityQueue $splPriorityQueue
    ) {
        $prioritizedServiceRegistry->all()->willReturn($priorityQueue);

        $priorityQueue->getIterator()->willReturn($splPriorityQueue);
        $splPriorityQueue->valid(false);

        $this->shouldThrow(CartNotFoundException::class)->during('getCart');
    }

    function it_returns_cart_from_first_available_context(
        PrioritizedServiceRegistryInterface $prioritizedServiceRegistry,
        CartContextInterface $firstCartContext,
        CartContextInterface $secondCartContext,
        CartInterface $cart,
        PriorityQueue $priorityQueue,
        SplPriorityQueue $splPriorityQueue
    ) {
        $prioritizedServiceRegistry->all()->willReturn($priorityQueue);
        $priorityQueue->getIterator()->willReturn($splPriorityQueue);

        $splPriorityQueue->valid()->willReturn(true, true, false);
        $splPriorityQueue->current()->willReturn($firstCartContext, $secondCartContext);
        $splPriorityQueue->next()->shouldBeCalled();
        $splPriorityQueue->rewind()->shouldBeCalled();

        $firstCartContext->getCart()->willThrow(CartNotFoundException::class);
        $secondCartContext->getCart()->willReturn($cart);

        $this->getCart()->shouldReturn($cart);
    }
}
