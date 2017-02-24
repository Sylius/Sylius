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

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\OrderBundle\Remover\ExpiredCartsRemover;
use Sylius\Bundle\OrderBundle\SyliusExpiredCartsEvents;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Remover\ExpiredCartsRemoverInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ExpiredCartsRemoverSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository, ObjectManager $orderManager, EventDispatcher $eventDispatcher)
    {
        $this->beConstructedWith($orderRepository, $orderManager, $eventDispatcher, '2 months');
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
        ObjectManager $orderManager,
        EventDispatcher $eventDispatcher,
        OrderInterface $firstCart,
        OrderInterface $secondCart
    ) {
        $orderRepository->findCartsNotModifiedSince(Argument::type('\DateTime'))->willReturn([
            $firstCart,
            $secondCart
        ]);

        $eventDispatcher
            ->dispatch(SyliusExpiredCartsEvents::PRE_REMOVE, Argument::any())
            ->shouldBeCalled()
        ;

        $orderManager->remove($firstCart);
        $orderManager->remove($secondCart);
        $orderManager->flush();

        $eventDispatcher
            ->dispatch(SyliusExpiredCartsEvents::POST_REMOVE, Argument::any())
            ->shouldBeCalled()
        ;

        $this->remove();
    }
}
