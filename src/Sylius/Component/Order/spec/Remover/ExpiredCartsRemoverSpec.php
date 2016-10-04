<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Order\Remover;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Remover\ExpiredCartsRemover;
use Sylius\Component\Order\Remover\ExpiredCartsRemoverInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 *
 * @mixin ExpiredCartsRemover
 */
final class ExpiredCartsRemoverSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository)
    {
        $this->beConstructedWith($orderRepository, '2 months');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ExpiredCartsRemover::class);
    }

    function it_implements_expired_carts_remover_interface()
    {
        $this->shouldImplement(ExpiredCartsRemoverInterface::class);
    }

    function it_removes_cart_which_has_been_updated_before_configured_date(
        OrderInterface $firstCart,
        OrderInterface $secondCart,
        OrderRepositoryInterface $orderRepository
    ) {
        $orderRepository->findExpiredCarts(new \DateTime('-2 months'))->willReturn([
            $firstCart,
            $secondCart
        ]);

        $orderRepository->remove($firstCart)->shouldBeCalled();
        $orderRepository->remove($secondCart)->shouldBeCalled();

        $this->remove();
    }
}
