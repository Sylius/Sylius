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
use Sylius\Bundle\CoreBundle\Security\Checker\VerificationAwareEnabledUserChecker;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

#[CoversClass(VerificationAwareEnabledUserChecker::class)]
final class VerificationAwareEnabledUserCheckerTest extends TestCase
{
    /** @var UserCheckerInterface&MockObject */
    private MockObject $enabledUserChecker;

    /** @var ChannelContextInterface&MockObject */
    private MockObject $channelContext;

    private VerificationAwareEnabledUserChecker $userChecker;

    protected function setUp(): void
    {
        $this->enabledUserChecker = $this->createMock(UserCheckerInterface::class);
        $this->channelContext = $this->createMock(ChannelContextInterface::class);
        $this->userChecker = new VerificationAwareEnabledUserChecker($this->enabledUserChecker, $this->channelContext);
    }

    public function testItSkipsTheEnabledUserCheckerPreAuthCheckForAnUnverifiedUserWhenVerificationIsRequired(): void
    {
        $user = $this->createMock(UserInterface::class);
        $channel = $this->createMock(ChannelInterface::class);

        $this->channelContext->method('getChannel')->willReturn($channel);
        $channel->method('isAccountVerificationRequired')->willReturn(true);
        $user->method('getVerifiedAt')->willReturn(null);

        $this->enabledUserChecker->expects($this->never())->method('checkPreAuth');

        $this->userChecker->checkPreAuth($user);
    }

    public function testItDelegatesThePreAuthCheckForAVerifiedUserWhenVerificationIsRequired(): void
    {
        $user = $this->createMock(UserInterface::class);
        $channel = $this->createMock(ChannelInterface::class);

        $this->channelContext->method('getChannel')->willReturn($channel);
        $channel->method('isAccountVerificationRequired')->willReturn(true);
        $user->method('getVerifiedAt')->willReturn(new \DateTimeImmutable());

        $this->enabledUserChecker->expects($this->once())->method('checkPreAuth')->with($user);

        $this->userChecker->checkPreAuth($user);
    }

    public function testItDelegatesThePreAuthCheckWhenVerificationIsNotRequired(): void
    {
        $user = $this->createMock(UserInterface::class);
        $channel = $this->createMock(ChannelInterface::class);

        $this->channelContext->method('getChannel')->willReturn($channel);
        $channel->method('isAccountVerificationRequired')->willReturn(false);

        $this->enabledUserChecker->expects($this->once())->method('checkPreAuth')->with($user);

        $this->userChecker->checkPreAuth($user);
    }

    public function testItDelegatesThePreAuthCheckWhenChannelIsNotACoreChannel(): void
    {
        $user = $this->createMock(UserInterface::class);

        $this->channelContext->method('getChannel')->willReturn($this->createMock(BaseChannelInterface::class));

        $this->enabledUserChecker->expects($this->once())->method('checkPreAuth')->with($user);

        $this->userChecker->checkPreAuth($user);
    }

    public function testItDelegatesThePreAuthCheckForANonSyliusUser(): void
    {
        $user = $this->createMock(SymfonyUserInterface::class);

        $this->channelContext->expects($this->never())->method('getChannel');
        $this->enabledUserChecker->expects($this->once())->method('checkPreAuth')->with($user);

        $this->userChecker->checkPreAuth($user);
    }

    public function testItAlwaysDelegatesThePostAuthCheck(): void
    {
        $user = $this->createMock(UserInterface::class);

        $this->enabledUserChecker->expects($this->once())->method('checkPostAuth')->with($user);

        $this->userChecker->checkPostAuth($user);
    }
}
