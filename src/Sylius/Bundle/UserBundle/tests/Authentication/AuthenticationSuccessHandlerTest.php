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
use Sylius\Bundle\UserBundle\Authentication\AuthenticationSuccessHandler;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\HttpUtils;

final class AuthenticationSuccessHandlerTest extends TestCase
{
    private HttpUtils&MockObject $httpUtils;

    private AuthenticationSuccessHandler $authenticationSuccessHandler;

    protected function setUp(): void
    {
        $this->httpUtils = $this->createMock(HttpUtils::class);

        $this->authenticationSuccessHandler = new AuthenticationSuccessHandler($this->httpUtils);
    }

    public function testAuthenticationSuccessHandler(): void
    {
        $this->assertInstanceOf(AuthenticationSuccessHandlerInterface::class, $this->authenticationSuccessHandler);
    }

    public function testReturnsJsonResponseIfRequestIsXmlBased(): void
    {
        /** @var Request&MockObject $request */
        $request = $this->createMock(Request::class);
        /** @var TokenInterface&MockObject $token */
        $token = $this->createMock(TokenInterface::class);
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);

        $request->expects($this->once())->method('isXmlHttpRequest')->willReturn(true);
        $token->expects($this->once())->method('getUser')->willReturn($user);

        $this->authenticationSuccessHandler->onAuthenticationSuccess($request, $token);
    }
}
