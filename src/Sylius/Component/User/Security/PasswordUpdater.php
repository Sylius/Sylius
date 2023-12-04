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
    public function __construct(private UserPasswordEncoderInterface|UserPasswordHasherInterface $userPasswordEncoderOrHasher)
    {
        if ($this->userPasswordEncoderOrHasher instanceof UserPasswordEncoderInterface) {
            trigger_deprecation(
                'sylius/user',
                '1.12',
                'The "%s" class is deprecated, use "%s" instead.',
                UserPasswordEncoderInterface::class,
                UserPasswordHasherInterface::class,
            );
        }
    }

    public function updatePassword(CredentialsHolderInterface $user): void
    {
        if (in_array($user->getPlainPassword(), ['', null], true)) {
            return;
        }

        if ($this->userPasswordEncoderOrHasher instanceof UserPasswordEncoderInterface) {
            $user->setPassword($this->userPasswordEncoderOrHasher->encode($user));
            $user->eraseCredentials();

            return;
        }

        $user->setPassword($this->userPasswordEncoderOrHasher->hash($user));
        $user->eraseCredentials();
    }
}
