<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Service;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Behat\Service\SecurityServiceInterface;
use Sylius\Behat\Service\SharedSecurityService;
use Sylius\Behat\Service\SharedSecurityServiceInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class SharedSecurityServiceSpec extends ObjectBehavior
{
    function let(SecurityServiceInterface $adminSecurityService)
    {
        $this->beConstructedWith($adminSecurityService);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SharedSecurityService::class);
    }

    function it_implements_shared_security_service()
    {
        $this->shouldImplement(SharedSecurityServiceInterface::class);
    }

    function it_performs_action_as_given_admin_user_and_restore_previous_token(
        SecurityServiceInterface $adminSecurityService,
        TokenInterface $token,
        OrderInterface $order,
        AdminUserInterface $adminUser
    ) {
        $adminSecurityService->getCurrentToken()->willReturn($token);
        $adminSecurityService->logIn($adminUser)->shouldBeCalled();
        $order->completeCheckout()->shouldBeCalled();
        $adminSecurityService->restoreToken($token)->shouldBeCalled();
        $adminSecurityService->logOut()->shouldNotBeCalled();

        $wrappedOrder = $order->getWrappedObject();
        $this->performActionAsAdminUser(
            $adminUser,
            function () use ($wrappedOrder) {
                $wrappedOrder->completeCheckout();
            }
        );
    }

    function it_performs_action_as_given_admin_user_and_logout(
        SecurityServiceInterface $adminSecurityService,
        OrderInterface $order,
        AdminUserInterface $adminUser
    ) {
        $adminSecurityService->getCurrentToken()->willThrow(TokenNotFoundException::class);
        $adminSecurityService->logIn($adminUser)->shouldBeCalled();
        $order->completeCheckout()->shouldBeCalled();
        $adminSecurityService->restoreToken(Argument::any())->shouldNotBeCalled();
        $adminSecurityService->logOut()->shouldBeCalled();

        $wrappedOrder = $order->getWrappedObject();
        $this->performActionAsAdminUser(
            $adminUser,
            function () use ($wrappedOrder) {
                $wrappedOrder->completeCheckout();
            }
        );
    }
}
