<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\CommandHandler;

use Sylius\Bundle\ApiBundle\Command\ResetPassword;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class ResetPasswordHandler implements MessageHandlerInterface
{
    private UserRepositoryInterface $userRepository;

    private MetadataInterface $metadata;

    private PasswordUpdaterInterface $passwordUpdater;

    public function __construct(
        UserRepositoryInterface $userRepository,
        MetadataInterface $metadata,
        PasswordUpdaterInterface $passwordUpdater
    ) {
        $this->userRepository = $userRepository;
        $this->metadata = $metadata;
        $this->passwordUpdater = $passwordUpdater;
    }

    public function __invoke(ResetPassword $command): void
    {
        /** @var ShopUserInterface|null $user */
        $user = $this->userRepository->findOneBy(['passwordResetToken' => $command->resetPasswordToken]);

        Assert::notNull($user);

        $resetting = $this->metadata->getParameter('resetting');
        $lifetime = new \DateInterval($resetting['token']['ttl']);

        if (!$user->isPasswordRequestNonExpired($lifetime)) {
            throw new \InvalidArgumentException('Password reset token has expired');
        }

        if ($command->resetPasswordToken !== $user->getPasswordResetToken()) {
            throw new \InvalidArgumentException('Password reset token does not match.');
        }

        $user->setPlainPassword($command->newPassword);

        $this->passwordUpdater->updatePassword($user);
        $user->setPasswordResetToken(null);
    }
}
