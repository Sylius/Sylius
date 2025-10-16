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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\HttpUtils;

final class AuthenticationFailureHandlerTest extends TestCase
{
    private HttpKernelInterface&MockObject $httpKernel;

    private HttpUtils&MockObject $httpUtils;

    private AuthenticationFailureHandler $authenticationFailureHandler;

    protected function setUp(): void
    {
        $this->httpKernel = $this->createMock(HttpKernelInterface::class);
        $this->httpUtils = $this->createMock(HttpUtils::class);

        $this->authenticationFailureHandler = new AuthenticationFailureHandler($this->httpKernel, $this->httpUtils);
    }

    public function testAuthenticationFailureHandler(): void
    {
        $this->assertInstanceOf(AuthenticationFailureHandlerInterface::class, $this->authenticationFailureHandler);
    }

    public function testReturnsJsonResponseIfRequestIsXmlBased(): void
    {
        /** @var Request&MockObject $request */
        $request = $this->createMock(Request::class);
        /** @var AuthenticationException&MockObject $authenticationException */
        $authenticationException = $this->createMock(AuthenticationException::class);
        $request->expects($this->once())->method('isXmlHttpRequest')->willReturn(true);
        $authenticationException->expects($this->once())->method('getMessageKey')->willReturn('Invalid credentials.');

        $this->assertInstanceOf(
            JsonResponse::class,
            $this->authenticationFailureHandler->onAuthenticationFailure($request, $authenticationException),
        );
    }
}
