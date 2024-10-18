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

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Symfony\Bundle\SecurityBundle\Security\FirewallConfig;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

final class UserImpersonatorSubscriberSpec extends ObjectBehavior
{
    private const FIREWALL_NAME = 'test';

    function let(FirewallMap $firewallMap): void
    {
        $this->beConstructedWith($firewallMap);
    }

    function it_removes_impersonation_session_variable(
        LogoutEvent $event,
        Request $request,
        SessionInterface $session,
        FirewallMap $firewallMap,
    ): void {
        $event->getRequest()->willReturn($request);
        $firewallConfig = new FirewallConfig(self::FIREWALL_NAME, 'mock');
        $firewallMap->getFirewallConfig($request)->willReturn($firewallConfig);
        $request->getSession()->willReturn($session);

        $session->remove(sprintf('_security_impersonate_sylius_%s', self::FIREWALL_NAME))->shouldBeCalled()->willReturn(null);

        $this->unimpersonate($event);
    }
}
