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

namespace Tests\Sylius\Bundle\CoreBundle\Security\Checker;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Security\Checker\EmailVerificationUserChecker;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

#[CoversClass(EmailVerificationUserChecker::class)]
final class EmailVerificationUserCheckerTest extends TestCase
{
    /** @var ChannelContextInterface&MockObject */
    private MockObject $channelContext;

    private EmailVerificationUserChecker $userChecker;

    protected function setUp(): void
    {
        $this->channelContext = $this->createMock(ChannelContextInterface::class);
        $this->userChecker = new EmailVerificationUserChecker($this->channelContext);
    }

    public function testItThrowsAnExceptionForAnUnverifiedUserWhenVerificationIsRequired(): void
    {
        $user = $this->createMock(UserInterface::class);
        $channel = $this->createMock(ChannelInterface::class);

        $this->channelContext->method('getChannel')->willReturn($channel);
        $channel->method('isAccountVerificationRequired')->willReturn(true);
        $user->method('getVerifiedAt')->willReturn(null);

        $this->expectException(CustomUserMessageAccountStatusException::class);
        $this->expectExceptionMessage('sylius.user.email_not_verified');

        $this->userChecker->checkPostAuth($user);
    }

    public function testItDoesNothingForAVerifiedUserWhenVerificationIsRequired(): void
    {
        $user = $this->createMock(UserInterface::class);
        $channel = $this->createMock(ChannelInterface::class);

        $this->channelContext->method('getChannel')->willReturn($channel);
        $channel->method('isAccountVerificationRequired')->willReturn(true);
        $user->method('getVerifiedAt')->willReturn(new \DateTimeImmutable());

        $this->userChecker->checkPostAuth($user);

        $this->expectNotToPerformAssertions();
    }

    public function testItDoesNothingWhenVerificationIsNotRequired(): void
    {
        $user = $this->createMock(UserInterface::class);
        $channel = $this->createMock(ChannelInterface::class);

        $this->channelContext->method('getChannel')->willReturn($channel);
        $channel->method('isAccountVerificationRequired')->willReturn(false);

        $this->userChecker->checkPostAuth($user);

        $this->expectNotToPerformAssertions();
    }

    public function testItDoesNothingWhenChannelIsNotACoreChannel(): void
    {
        $user = $this->createMock(UserInterface::class);

        $this->channelContext->method('getChannel')->willReturn($this->createMock(BaseChannelInterface::class));

        $this->userChecker->checkPostAuth($user);

        $this->expectNotToPerformAssertions();
    }

    public function testItDoesNothingForANonSyliusUser(): void
    {
        $user = $this->createMock(SymfonyUserInterface::class);

        $this->channelContext->expects($this->never())->method('getChannel');

        $this->userChecker->checkPostAuth($user);
    }

    public function testItDoesNothingOnPreAuth(): void
    {
        $user = $this->createMock(UserInterface::class);

        $this->channelContext->expects($this->never())->method('getChannel');

        $this->userChecker->checkPreAuth($user);
    }
}
