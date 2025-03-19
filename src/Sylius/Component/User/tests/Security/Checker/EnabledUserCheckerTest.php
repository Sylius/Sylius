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

namespace Sylius\Component\User\tests\Security\Checker;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Security\Checker\EnabledUserChecker;
use Symfony\Component\Security\Core\Exception\DisabledException;

final class EnabledUserCheckerTest extends TestCase
{
    use ProphecyTrait;

    private EnabledUserChecker $userChecker;

    protected function setUp(): void
    {
        $this->userChecker = new EnabledUserChecker();
    }

    /** @test */
    public function it_throws_a_disabled_exception_if_account_is_disabled(): void
    {
        $this->expectException(DisabledException::class);

        $user = $this->createMock(UserInterface::class);
        $user->method('isEnabled')->willReturn(false);

        $this->userChecker->checkPreAuth($user);
        $this->userChecker->checkPostAuth($user);
    }

    /** @test */
    public function it_does_nothing_if_user_is_enabled(): void
    {
        /** @var UserInterface|ObjectProphecy $user */
        $user = $this->prophesize(UserInterface::class);

        $user->isEnabled()->shouldBeCalled()->willReturn(true);

        $this->userChecker->checkPreAuth($user->reveal());
    }
}
