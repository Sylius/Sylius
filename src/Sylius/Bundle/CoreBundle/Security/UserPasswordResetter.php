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

namespace Sylius\Bundle\CoreBundle\Security;

use Sylius\Bundle\UserBundle\Exception\UserNotFoundException;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;

final class UserPasswordResetter implements UserPasswordResetterInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordUpdaterInterface $passwordUpdater,
        private string $tokenTtl,
    ) {
    }

    /**
     * @throws UserNotFoundException
     */
    public function reset(string $token, string $password): void
    {
        /** @var UserInterface|null $user */
        $user = $this->userRepository->findOneBy(['passwordResetToken' => $token]);

        if (null === $user) {
            throw new UserNotFoundException(message: sprintf('No user found with reset token: %s', $token));
        }

        $lifetime = new \DateInterval($this->tokenTtl);

        if (!$user->isPasswordRequestNonExpired($lifetime)) {
            throw new \InvalidArgumentException('Password reset token has expired');
        }

        $user->setPlainPassword($password);

        $this->passwordUpdater->updatePassword($user);
        $user->setPasswordResetToken(null);
        $user->setPasswordRequestedAt(null);
    }
}
