<?php

/*
 *  This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\MessageHandler\Admin\Account;

use Sylius\Bundle\CoreBundle\Message\Admin\Account\ResetPassword;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class ResetPasswordHandler implements MessageHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordUpdaterInterface $passwordUpdater,
        private string $tokenTtl
    ) {
    }

    public function __invoke(ResetPassword $command): void
    {
        /** @var AdminUserInterface|null $user */
        $user = $this->userRepository->findOneBy(['passwordResetToken' => $command->resetPasswordToken]);

        Assert::notNull($user, 'No user found with reset token: ' . $command->resetPasswordToken);

        $lifetime = new \DateInterval($this->tokenTtl);

        if (!$user->isPasswordRequestNonExpired($lifetime)) {
            throw new \InvalidArgumentException('Password reset token has expired');
        }

        $user->setPlainPassword($command->newPassword);

        $this->passwordUpdater->updatePassword($user);
        $user->setPasswordResetToken(null);
    }
}
