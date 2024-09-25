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

namespace Sylius\Component\User\Security\Checker;

use Sylius\Component\User\Model\AdvancedUserInterface;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Copied from Symfony 4.4.
 *
 * @link https://github.com/symfony/symfony/blob/v4.4.18/src/Symfony/Component/Security/Core/User/UserChecker.php
 */
final class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof AdvancedUserInterface) {
            return;
        }

        if (!$user->isAccountNonLocked()) {
            $exception = new LockedException('User account is locked.');
            $exception->setUser($user);

            throw $exception;
        }

        if (!$user->isEnabled()) {
            $exception = new DisabledException('User account is disabled.');
            $exception->setUser($user);

            throw $exception;
        }

        if (!$user->isAccountNonExpired()) {
            $exception = new AccountExpiredException('User account has expired.');
            $exception->setUser($user);

            throw $exception;
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof AdvancedUserInterface) {
            return;
        }

        if (!$user->isCredentialsNonExpired()) {
            $exception = new CredentialsExpiredException('User credentials have expired.');
            $exception->setUser($user);

            throw $exception;
        }
    }
}
