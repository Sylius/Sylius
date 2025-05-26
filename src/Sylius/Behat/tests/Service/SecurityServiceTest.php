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

namespace Tests\Sylius\Behat\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Behat\Service\SecurityService;
use Sylius\Behat\Service\SecurityServiceInterface;
use Sylius\Behat\Service\Setter\CookieSetterInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionFactoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

final class SecurityServiceTest extends TestCase
{
    private MockObject&RequestStack $requestStack;

    private CookieSetterInterface&MockObject $cookieSetter;

    private MockObject&SessionFactoryInterface $sessionFactory;

    private MockObject&SessionInterface $session;

    private SecurityService $securityService;

    protected function setUp(): void
    {
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->cookieSetter = $this->createMock(CookieSetterInterface::class);
        $this->sessionFactory = $this->createMock(SessionFactoryInterface::class);
        $this->session = $this->createMock(SessionInterface::class);

        $this->securityService = new SecurityService(
            $this->requestStack,
            $this->cookieSetter,
            'shop',
            $this->sessionFactory,
        );
    }

    public function testImplementsSecurityServiceInterface(): void
    {
        $this->assertInstanceOf(SecurityServiceInterface::class, $this->securityService);
    }

    public function testLogsUserInWhenSessionFactoryIsNotAvailable(): void
    {
        /** @var ShopUserInterface&MockObject $shopUser */
        $shopUser = $this->createMock(ShopUserInterface::class);

        $shopUser->expects($this->once())->method('getRoles')->willReturn(['ROLE_USER']);
        $shopUser->expects($this->once())->method('__serialize')->willReturn(['serialized_user']);

        $this->requestStack->expects($this->once())->method('getSession')->willReturn($this->session);

        $this->session->expects($this->once())->method('set');
        $this->session->expects($this->once())->method('save');
        $this->session->expects($this->once())->method('getName')->willReturn('MOCKEDSID');
        $this->session->expects($this->once())->method('getId')->willReturn('xyzc123');

        $this->cookieSetter->expects($this->once())->method('setCookie')->with('MOCKEDSID', 'xyzc123');

        $this->securityService->logIn($shopUser);
    }

    public function testLogsUserIn(): void
    {
        /** @var ShopUserInterface&MockObject $shopUser */
        $shopUser = $this->createMock(ShopUserInterface::class);

        $this->sessionFactory->expects($this->once())->method('createSession')->willReturn($this->session);

        $shopUser->expects($this->once())->method('getRoles')->willReturn(['ROLE_USER']);
        $shopUser->expects($this->once())->method('__serialize')->willReturn(['serialized_user']);

        $this->requestStack->expects($this->once())->method('push')->with($this->isInstanceOf(Request::class));
        $this->requestStack->expects($this->once())->method('getSession')->willReturn($this->session);

        $this->session->expects($this->once())->method('set');
        $this->session->expects($this->once())->method('save');
        $this->session->expects($this->once())->method('getName')->willReturn('MOCKEDSID');
        $this->session->expects($this->once())->method('getId')->willReturn('xyzc123');

        $this->cookieSetter->expects($this->once())->method('setCookie')->with('MOCKEDSID', 'xyzc123');

        $this->securityService->logIn($shopUser);
    }

    public function testDoesNothingWhenThereIsNoSessionDuringLogOut(): void
    {
        $this->requestStack->expects($this->once())->method('getSession')->willThrowException(new SessionNotFoundException());
        $this->cookieSetter->expects($this->never())->method('setCookie')->with($this->any());

        $this->securityService->logOut();
    }

    public function testLogsUserOut(): void
    {
        $this->requestStack->expects($this->once())->method('getSession')->willReturn($this->session);

        $this->session->expects($this->once())->method('set')->with('_security_shop', null);
        $this->session->expects($this->once())->method('save');
        $this->session->expects($this->once())->method('getName')->willReturn('MOCKEDSID');
        $this->session->expects($this->once())->method('getId')->willReturn('xyzc123');

        $this->cookieSetter->expects($this->once())->method('setCookie')->with('MOCKEDSID', 'xyzc123');

        $this->securityService->logOut();
    }

    public function testThrowsTokenNotFoundException(): void
    {
        $this->requestStack->expects($this->once())->method('getSession')->willReturn($this->session);
        $this->session->expects($this->once())->method('get')->with('_security_shop')->willReturn(null);
        $this->expectException(TokenNotFoundException::class);

        $this->securityService->getCurrentToken();
    }
}
