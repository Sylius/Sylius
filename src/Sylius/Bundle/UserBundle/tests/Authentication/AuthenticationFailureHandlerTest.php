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

namespace Tests\Sylius\Bundle\UserBundle\Authentication;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\UserBundle\Authentication\AuthenticationFailureHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AuthenticationFailureHandlerTest extends TestCase
{
    /** @var HttpKernelInterface&MockObject */
    private $httpKernel;

    /** @var HttpUtils&MockObject */
    private $httpUtils;

    /** @var AuthenticationException&MockObject */
    private AuthenticationException $authException;

    /** @var Request&MockObject */
    private Request $request;

    protected function setUp(): void
    {
        $this->httpKernel = $this->createMock(HttpKernelInterface::class);
        $this->httpUtils = $this->createMock(HttpUtils::class);
        $this->authException = $this->createMock(AuthenticationException::class);
        $this->request = $this->createMock(Request::class);
    }

    public function testItImplementsTheInterface(): void
    {
        $handler = new AuthenticationFailureHandler($this->httpKernel, $this->httpUtils);

        self::assertInstanceOf(AuthenticationFailureHandlerInterface::class, $handler);
    }

    public function testReturnsJsonResponseForAjaxRequestWithoutTranslator(): void
    {
        $handler = new AuthenticationFailureHandler($this->httpKernel, $this->httpUtils);
        $this->request->expects(self::once())->method('isXmlHttpRequest')->willReturn(true);
        $this->authException->expects(self::once())->method('getMessageKey')->willReturn('Invalid credentials.');

        $response = $handler->onAuthenticationFailure($this->request, $this->authException);

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        self::assertSame(
            ['success' => false, 'message' => 'Invalid credentials.'],
            json_decode($response->getContent(), true, 512, \JSON_THROW_ON_ERROR),
        );
    }

    public function testReturnsTranslatedJsonResponseForAjaxRequestWithTranslator(): void
    {
        /** @var TranslatorInterface&MockObject $translator */
        $translator = $this->createMock(TranslatorInterface::class);

        $handler = new AuthenticationFailureHandler(
            $this->httpKernel,
            $this->httpUtils,
            [],
            null,
            $translator,
        );

        $this->request->expects(self::once())->method('isXmlHttpRequest')->willReturn(true);
        $this->request->expects(self::once())->method('getLocale')->willReturn('pl_PL');

        $this->authException->expects(self::once())->method('getMessageKey')->willReturn('Invalid credentials.');

        $translator
            ->expects(self::once())
            ->method('trans')
            ->with('Invalid credentials.', [], 'security', 'pl_PL')
            ->willReturn('Nieprawidłowe dane logowania.');

        $response = $handler->onAuthenticationFailure($this->request, $this->authException);

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        self::assertSame(
            ['success' => false, 'message' => 'Nieprawidłowe dane logowania.'],
            json_decode($response->getContent(), true, 512, \JSON_THROW_ON_ERROR),
        );
    }

    public function testFallsBackToParentHandlerWhenRequestIsNotXmlHttpRequest(): void
    {
        $options = ['failure_path' => '/login'];

        $this->httpUtils
            ->expects(self::once())
            ->method('createRedirectResponse')
            ->with(self::isInstanceOf(Request::class), '/login')
            ->willReturn(new RedirectResponse('/login'));

        $handler = new AuthenticationFailureHandler($this->httpKernel, $this->httpUtils, $options);

        $request = Request::create('/login_check');
        $session = new Session(new MockArraySessionStorage());
        $session->start();
        $request->setSession($session);

        $response = $handler->onAuthenticationFailure($request, $this->authException);

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame(302, $response->getStatusCode());
    }
}
