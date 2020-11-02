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

namespace spec\Sylius\Bundle\ApiBundle\Assigner;

use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Assigner\CartToUserAssignerInterface;
use Sylius\Bundle\CoreBundle\Context\CustomerContext;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class CartToUserAssignerSpec extends ObjectBehavior
{
    function let(
        ObjectManager $orderManager,
        SessionInterface $session,
        OrderRepositoryInterface $cartRepository,
        CustomerInterface $customer
    ): void{
        $this->beConstructedWith($orderManager, $session, $cartRepository, $customer);
    }

    function it_implements_cart_to_user_assigner_interface(): void
    {
        $this->shouldImplement(CartToUserAssignerInterface::class);
    }

    function it_should_assign_cart_to_user_if_there_is_a_cart(
        SessionInterface $session,
        OrderRepositoryInterface $cartRepository,
        OrderInterface $cart,
        CustomerInterface $customer,
        CustomerContextInterface $customerContext
    ): void{
        $session->has('cart_token')->willReturn(true);
        $session->get('cart_token')->willReturn('urisafestr');
        $cartRepository->findCartByTokenValue('urisafestr')->willReturn($cart);

        $customerContext->getCustomer()->willReturn($customer);
        $cart->setCustomer($customer)->shouldBeCalled();

        $this->assignByCustomer($customer);
    }
}
