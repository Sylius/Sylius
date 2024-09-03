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
use Sylius\Component\User\Model\AdvancedUserInterface;
use Sylius\Component\User\Security\Checker\UserChecker;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Exception\LockedException;

final class UserCheckerTest extends TestCase
{
    private UserChecker $userChecker;

    protected function setUp(): void
    {
        $this->userChecker = new UserChecker();
    }

    /** @test */
    public function it_throws_locked_exception_if_account_is_locked(): void
    {
        $this->expectException(LockedException::class);

        $user = $this->createMock(AdvancedUserInterface::class);
        $user->method('isAccountNonLocked')->willReturn(false);
        $user->method('isEnabled')->willReturn(true);
        $user->method('isAccountNonExpired')->willReturn(true);
        $user->method('isCredentialsNonExpired')->willReturn(true);

        $this->userChecker->checkPreAuth($user);
        $this->userChecker->checkPostAuth($user);
    }

    /** @test */
    public function it_throws_disabled_exception_if_account_is_disabled(): void
    {
        $this->expectException(DisabledException::class);

        $user = $this->createMock(AdvancedUserInterface::class);
        $user->method('isAccountNonLocked')->willReturn(true);
        $user->method('isEnabled')->willReturn(false);
        $user->method('isAccountNonExpired')->willReturn(true);
        $user->method('isCredentialsNonExpired')->willReturn(true);

        $this->userChecker->checkPreAuth($user);
        $this->userChecker->checkPostAuth($user);
    }

    /** @test */
    public function it_throws_account_expired_exception_if_account_is_expired(): void
    {
        $this->expectException(AccountExpiredException::class);

        $user = $this->createMock(AdvancedUserInterface::class);
        $user->method('isAccountNonLocked')->willReturn(true);
        $user->method('isEnabled')->willReturn(true);
        $user->method('isAccountNonExpired')->willReturn(false);
        $user->method('isCredentialsNonExpired')->willReturn(true);

        $this->userChecker->checkPreAuth($user);
        $this->userChecker->checkPostAuth($user);
    }

    /** @test */
    public function it_throws_credentials_expired_exception_if_credentials_are_expired(): void
    {
        $this->expectException(CredentialsExpiredException::class);

        $user = $this->createMock(AdvancedUserInterface::class);
        $user->method('isAccountNonLocked')->willReturn(true);
        $user->method('isEnabled')->willReturn(true);
        $user->method('isAccountNonExpired')->willReturn(true);
        $user->method('isCredentialsNonExpired')->willReturn(false);

        $this->userChecker->checkPreAuth($user);
        $this->userChecker->checkPostAuth($user);
    }
}
