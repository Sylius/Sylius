<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Cart;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Cart\BlameCart;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class BlameCartHandlerSpec extends ObjectBehavior
{
    function let(
        UserRepositoryInterface $shopUserRepository,
        OrderRepositoryInterface $orderRepository,
        OrderProcessorInterface $orderProcessor,
    ): void {
        $this->beConstructedWith(
            $shopUserRepository,
            $orderRepository,
            $orderProcessor,
        );
    }

    function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    function it_blames_cart_with_given_data(
        UserRepositoryInterface $shopUserRepository,
        OrderRepositoryInterface $orderRepository,
        OrderInterface $cart,
        ShopUserInterface $user,
        CustomerInterface $customer,
        OrderProcessorInterface $orderProcessor,
    ): void {
        $shopUserRepository->findOneByEmail('sylius@example.com')->willReturn($user);
        $orderRepository->findCartByTokenValue('TOKEN')->willReturn($cart);

        $cart->getCustomer()->willReturn(null);

        $user->getCustomer()->willReturn($customer);

        $cart->setCustomerWithAuthorization($customer)->shouldBeCalled();

        $orderProcessor->process($cart)->shouldBeCalled();

        $this(new BlameCart('sylius@example.com', 'TOKEN'));
    }

    function it_throws_an_exception_if_cart_is_occupied(
        UserRepositoryInterface $shopUserRepository,
        OrderRepositoryInterface $orderRepository,
        OrderInterface $cart,
        ShopUserInterface $user,
        CustomerInterface $customer,
    ): void {
        $shopUserRepository->findOneByEmail('sylius@example.com')->willReturn($user);
        $orderRepository->findCartByTokenValue('TOKEN')->willReturn($cart);

        $cart->getCustomer()->willReturn($customer);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [
                new BlameCart('sylius@example.com', 'TOKEN'),
            ])
        ;
    }

    function it_throws_an_exception_if_cart_has_not_been_found(
        UserRepositoryInterface $shopUserRepository,
        ShopUserInterface $user,
    ): void {
        $shopUserRepository->findOneByEmail('sylius@example.com')->willReturn($user);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [new BlameCart('sylius@example.com', 'TOKEN')])
        ;
    }

    function it_throws_an_exception_if_user_has_not_been_found(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [new BlameCart('sylius@example.com', 'TOKEN')])
        ;
    }
}
