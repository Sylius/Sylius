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

namespace spec\Sylius\Bundle\ApiBundle\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Response;

final class AuthenticationSuccessListenerSpec extends ObjectBehavior
{
    function it_adds_customers_id_to_shop_authentication_token_response(
        ShopUserInterface $shopUser,
        CustomerInterface $customer
    ): void {
        $event = new AuthenticationSuccessEvent([], $shopUser->getWrappedObject(), new Response());

        $shopUser->getCustomer()->willReturn($customer->getWrappedObject());

        $customer->getId()->shouldBeCalled()->willReturn(1);

        $this->onAuthenticationSuccessResponse($event);
    }

    function it_does_not_add_anything_to_admin_authentication_token_response(
        AdminUserInterface $adminUser,
        CustomerInterface $customer
    ): void {
        $event = new AuthenticationSuccessEvent([], $adminUser->getWrappedObject(), new Response());

        $customer->getId()->shouldNotBeCalled();

        $this->onAuthenticationSuccessResponse($event);
    }
}
