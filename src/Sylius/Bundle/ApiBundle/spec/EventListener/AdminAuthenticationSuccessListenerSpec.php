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

namespace spec\Sylius\Bundle\ApiBundle\EventListener;

use ApiPlatform\Api\IriConverterInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Response;

final class AdminAuthenticationSuccessListenerSpec extends ObjectBehavior
{
    function let(IriConverterInterface $iriConverter): void
    {
        $this->beConstructedWith($iriConverter);
    }

    function it_adds_admins_to_admin_authentication_token_response(
        IriConverterInterface $iriConverter,
        AdminUserInterface $adminUser,
    ): void {
        $event = new AuthenticationSuccessEvent([], $adminUser->getWrappedObject(), new Response());
        $iriConverter->getIriFromResource($adminUser->getWrappedObject())->shouldBeCalled();

        $this->onAuthenticationSuccessResponse($event);
    }

    function it_does_not_add_anything_to_shop_authentication_token_response(
        IriConverterInterface $iriConverter,
        AdminUserInterface $adminUser,
        ShopUserInterface $shopUser,
    ): void {
        $event = new AuthenticationSuccessEvent([], $shopUser->getWrappedObject(), new Response());

        $iriConverter->getIriFromResource($adminUser->getWrappedObject())->shouldNotBeCalled();

        $this->onAuthenticationSuccessResponse($event);
    }
}
