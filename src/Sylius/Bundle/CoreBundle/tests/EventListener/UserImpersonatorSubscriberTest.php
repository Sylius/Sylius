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

namespace Tests\Sylius\Bundle\CoreBundle\EventListener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\EventListener\UserImpersonatorSubscriber;
use Symfony\Bundle\SecurityBundle\Security\FirewallConfig;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

final class UserImpersonatorSubscriberTest extends TestCase
{
    private const FIREWALL_NAME = 'test';

    private UserImpersonatorSubscriber $subscriber;

    private FirewallMap&MockObject $firewallMap;

    protected function setUp(): void
    {
        $this->firewallMap = $this->createMock(FirewallMap::class);

        $this->subscriber = new UserImpersonatorSubscriber($this->firewallMap);
    }

    public function testSubscribeToEvents(): void
    {
        $this->assertSame(
            [LogoutEvent::class => 'unimpersonate'],
            UserImpersonatorSubscriber::getSubscribedEvents(),
        );
    }

    public function testRemovesImpersonationSessionVariable(): void
    {
        $request = $this->createMock(Request::class);
        $session = $this->createMock(SessionInterface::class);
        $event = $this->createMock(LogoutEvent::class);

        $event->method('getRequest')->willReturn($request);
        $firewallConfig = new FirewallConfig(self::FIREWALL_NAME, 'mock');
        $this->firewallMap->method('getFirewallConfig')->with($request)->willReturn($firewallConfig);
        $request->method('getSession')->willReturn($session);

        $session->expects($this->once())
            ->method('remove')
            ->with(sprintf('_security_impersonate_sylius_%s', self::FIREWALL_NAME))
        ;

        $this->subscriber->unimpersonate($event);
    }

    public function testDoesNotThrowExceptionWhenSessionIsNotSet(): void
    {
        $request = $this->createMock(Request::class);
        $event = $this->createMock(LogoutEvent::class);

        $event->method('getRequest')->willReturn($request);

        $firewallConfig = new FirewallConfig(self::FIREWALL_NAME, 'mock');
        $this->firewallMap
            ->method('getFirewallConfig')
            ->with($request)
            ->willReturn($firewallConfig)
        ;
        $request->method('getSession')->will($this->throwException(new SessionNotFoundException()));

        $this->subscriber->unimpersonate($event);

        $this->addToAssertionCount(1);
    }

    public function testDoesNothingWhenFirewallIsNotSet(): void
    {
        $request = $this->createMock(Request::class);
        $session = $this->createMock(SessionInterface::class);
        $event = $this->createMock(LogoutEvent::class);

        $event->method('getRequest')->willReturn($request);
        $this->firewallMap
            ->method('getFirewallConfig')
            ->with($request)
            ->willReturn(null)
        ;
        $request->method('getSession')->willReturn($session);

        $session->expects($this->never())->method('remove');

        $this->subscriber->unimpersonate($event);
    }
}
