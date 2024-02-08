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

namespace Sylius\Component\User\Security;

use Sylius\Component\User\Model\CredentialsHolderInterface;

final class PasswordUpdater implements PasswordUpdaterInterface
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function updatePassword(CredentialsHolderInterface $user): void
    {
        if (in_array($user->getPlainPassword(), ['', null], true)) {
            return;
        }

        $user->setPassword($this->userPasswordHasher->hash($user));
        $user->eraseCredentials();
    }
}
