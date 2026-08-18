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

namespace Tests\Sylius\Bundle\CoreBundle\OAuth\EventListener;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\OAuth\EventListener\UnverifiedEmailLoginFailureListener;
use Sylius\Bundle\CoreBundle\OAuth\Exception\UnverifiedEmailException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\SecurityRequestAttributes;

final class UnverifiedEmailLoginFailureListenerTest extends TestCase
{
    private UnverifiedEmailLoginFailureListener $listener;

    protected function setUp(): void
    {
        $this->listener = new UnverifiedEmailLoginFailureListener();
    }

    public function testStoresTheErrorInTheSessionSoTheLoginPageCanRenderIt(): void
    {
        $exception = new UnverifiedEmailException('facebook');
        $request = $this->createRequestWithSession();

        ($this->listener)($this->createEvent($exception, $request));

        $this->assertSame(
            $exception,
            $request->getSession()->get(SecurityRequestAttributes::AUTHENTICATION_ERROR),
        );
    }

    public function testDoesNothingForOtherAuthenticationFailures(): void
    {
        $request = $this->createRequestWithSession();

        ($this->listener)($this->createEvent(new AuthenticationException(), $request));

        $this->assertFalse($request->getSession()->has(SecurityRequestAttributes::AUTHENTICATION_ERROR));
    }

    public function testDoesNothingWhenThereIsNoSession(): void
    {
        $request = new Request();

        ($this->listener)($this->createEvent(new UnverifiedEmailException('facebook'), $request));

        $this->assertFalse($request->hasSession());
    }

    private function createRequestWithSession(): Request
    {
        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage()));

        return $request;
    }

    private function createEvent(AuthenticationException $exception, Request $request): LoginFailureEvent
    {
        return new LoginFailureEvent(
            $exception,
            $this->createMock(AuthenticatorInterface::class),
            $request,
            null,
            'shop',
        );
    }
}
