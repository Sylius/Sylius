<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\OrderBundle\Remover;

use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\OrderBundle\SyliusExpiredCartsEvents;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Remover\ExpiredCartsRemoverInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class ExpiredCartsRemoverSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository, ObjectManager $orderManager, EventDispatcher $eventDispatcher): void
    {
        $this->beConstructedWith($orderRepository, $orderManager, $eventDispatcher, '2 months');
    }

    function it_implements_an_expired_carts_remover_interface(): void
    {
        $this->shouldImplement(ExpiredCartsRemoverInterface::class);
    }

    function it_removes_a_cart_which_has_been_updated_before_configured_date(
        OrderRepositoryInterface $orderRepository,
        ObjectManager $orderManager,
        EventDispatcher $eventDispatcher,
        OrderInterface $firstCart,
        OrderInterface $secondCart,
    ): void {
        $orderRepository->findCartsNotModifiedSince(Argument::type('\DateTimeInterface'), 100)->willReturn(
            [$firstCart, $secondCart],
            [],
        );

        $eventDispatcher
            ->dispatch(Argument::any(), SyliusExpiredCartsEvents::PRE_REMOVE)
            ->shouldBeCalled()
        ;

        $orderManager->remove($firstCart)->shouldBeCalledOnce();
        $orderManager->remove($secondCart)->shouldBeCalledOnce();
        $orderManager->flush()->shouldBeCalledOnce();
        $orderManager->clear()->shouldBeCalledOnce();

        $eventDispatcher
            ->dispatch(Argument::any(), SyliusExpiredCartsEvents::POST_REMOVE)
            ->shouldBeCalled()
        ;

        $this->remove();
    }

    function it_removes_carts_in_batches(
        OrderRepositoryInterface $orderRepository,
        ObjectManager $orderManager,
        EventDispatcher $eventDispatcher,
        OrderInterface $cart,
    ): void {
        $orderRepository
            ->findCartsNotModifiedSince(Argument::type('\DateTimeInterface'), 100)
            ->willReturn(
                array_fill(0, 100, $cart),
                array_fill(0, 100, $cart),
                [],
            )
        ;

        $eventDispatcher
            ->dispatch(Argument::any(), SyliusExpiredCartsEvents::PRE_REMOVE)
            ->shouldBeCalledTimes(2)
        ;

        $orderManager->remove(Argument::type(OrderInterface::class))->shouldBeCalledTimes(200);
        $orderManager->flush()->shouldBeCalledTimes(2);
        $orderManager->clear()->shouldBeCalledTimes(2);

        $eventDispatcher
            ->dispatch(Argument::any(), SyliusExpiredCartsEvents::POST_REMOVE)
            ->shouldBeCalledTimes(2)
        ;

        $this->remove();
    }
}
