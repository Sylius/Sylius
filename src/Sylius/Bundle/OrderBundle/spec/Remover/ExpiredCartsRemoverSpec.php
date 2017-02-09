<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\OrderBundle\Remover;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\OrderBundle\Remover\ExpiredCartsRemover;
use Sylius\Bundle\OrderBundle\SyliusCartsRemoveEvents;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Remover\ExpiredCartsRemoverInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ExpiredCartsRemoverSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository, EventDispatcher $eventDispatcher)
    {
        $this->beConstructedWith($orderRepository, $eventDispatcher, '2 months');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ExpiredCartsRemover::class);
    }

    function it_implements_an_expired_carts_remover_interface()
    {
        $this->shouldImplement(ExpiredCartsRemoverInterface::class);
    }

    function it_removes_a_cart_which_has_been_updated_before_configured_date(
        OrderRepositoryInterface $orderRepository,
        EventDispatcher $eventDispatcher,
        OrderInterface $firstCart,
        OrderInterface $secondCart
    ) {
        $orderRepository->findCartsNotModifiedSince(Argument::type('\DateTime'))->willReturn([
            $firstCart,
            $secondCart
        ]);

        $eventDispatcher
            ->dispatch(SyliusCartsRemoveEvents::CARTS_PRE_REMOVE, Argument::any())
            ->shouldBeCalled()
        ;

        $orderRepository->remove($firstCart)->shouldBeCalled();
        $orderRepository->remove($secondCart)->shouldBeCalled();

        $eventDispatcher
            ->dispatch(SyliusCartsRemoveEvents::CARTS_POST_REMOVE, Argument::any())
            ->shouldBeCalled()
        ;

        $this->remove();
    }
}
