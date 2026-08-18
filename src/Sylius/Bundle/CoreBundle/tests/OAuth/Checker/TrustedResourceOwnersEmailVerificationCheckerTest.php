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

namespace Tests\Sylius\Bundle\CoreBundle\OAuth\Checker;

use HWI\Bundle\OAuthBundle\OAuth\ResourceOwnerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\OAuth\Checker\EmailVerificationCheckerInterface;
use Sylius\Bundle\CoreBundle\OAuth\Checker\TrustedResourceOwnersEmailVerificationChecker;

final class TrustedResourceOwnersEmailVerificationCheckerTest extends TestCase
{
    private EmailVerificationCheckerInterface&MockObject $decoratedChecker;

    protected function setUp(): void
    {
        $this->decoratedChecker = $this->createMock(EmailVerificationCheckerInterface::class);
    }

    public function testImplementsEmailVerificationCheckerInterface(): void
    {
        $this->assertInstanceOf(
            EmailVerificationCheckerInterface::class,
            new TrustedResourceOwnersEmailVerificationChecker($this->decoratedChecker),
        );
    }

    public function testTrustsAConfiguredResourceOwnerWithoutAskingTheDecoratedChecker(): void
    {
        $checker = new TrustedResourceOwnersEmailVerificationChecker($this->decoratedChecker, ['facebook']);

        $this->decoratedChecker->expects($this->never())->method('isEmailVerified');

        $this->assertTrue($checker->isEmailVerified($this->createResponse('facebook')));
    }

    public function testDelegatesToTheDecoratedCheckerForOtherResourceOwners(): void
    {
        $checker = new TrustedResourceOwnersEmailVerificationChecker($this->decoratedChecker, ['facebook']);
        $response = $this->createResponse('google');

        $this->decoratedChecker
            ->expects($this->once())
            ->method('isEmailVerified')
            ->with($response)
            ->willReturn(false)
        ;

        $this->assertFalse($checker->isEmailVerified($response));
    }

    public function testDelegatesEverythingWhenNoResourceOwnerIsTrusted(): void
    {
        $checker = new TrustedResourceOwnersEmailVerificationChecker($this->decoratedChecker);

        $this->decoratedChecker->expects($this->once())->method('isEmailVerified')->willReturn(true);

        $this->assertTrue($checker->isEmailVerified($this->createResponse('facebook')));
    }

    private function createResponse(string $resourceOwnerName): UserResponseInterface
    {
        $resourceOwner = $this->createMock(ResourceOwnerInterface::class);
        $resourceOwner->method('getName')->willReturn($resourceOwnerName);

        $response = $this->createMock(UserResponseInterface::class);
        $response->method('getResourceOwner')->willReturn($resourceOwner);

        return $response;
    }
}
