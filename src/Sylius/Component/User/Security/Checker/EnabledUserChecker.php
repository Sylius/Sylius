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

use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

final class EnabledUserChecker implements UserCheckerInterface
{
    public function checkPreAuth(SymfonyUserInterface $user): void
    {
    }

    public function checkPostAuth(SymfonyUserInterface $user): void
    {
        if (!$user instanceof UserInterface) {
            return;
        }

        if (!$user->isEnabled()) {
            $exception = new CustomUserMessageAccountStatusException('User account is disabled.');
            $exception->setUser($user);

            throw $exception;
        }
    }
}
