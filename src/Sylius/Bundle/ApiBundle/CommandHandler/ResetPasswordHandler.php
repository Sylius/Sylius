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


use Sylius\Bundle\ApiBundle\Command\ChangeShopUserPassword;
use Sylius\Bundle\ApiBundle\Command\ResetPassword;
use Sylius\Component\Core\Model\ShopUser;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use Webmozart\Assert\Assert;

/** @experimental */
class ResetPasswordHandler
{
    /** @var UserRepositoryInterface */
    private $userRepository;

    /** @var MetadataInterface */
    private $metadata;

    /** @var MessageBusInterface */
    private $commandBus;

    public function __construct(
        UserRepositoryInterface $userRepository,
        MetadataInterface $metadata,
        MessageBusInterface $commandBus
    ) {
        $this->userRepository = $userRepository;
        $this->metadata = $metadata;
        $this->commandBus = $commandBus;
    }

    public function __invoke(ResetPassword $command): void
    {
        /** @var ShopUserInterface $user */
        $user = $this->userRepository->findOneBy(['passwordResetToken' => $command->getResetPasswordToken()]);

        $resetting = $this->metadata->getParameter('resetting');
        $lifetime = new \DateInterval($resetting['token']['ttl']);
        if (!$user->isPasswordRequestNonExpired($lifetime)) {
            throw new \InvalidArgumentException('Password reset token has expired');
        }

        if ($command->getResetPasswordToken() !== $user->getPasswordResetToken()) {
            throw new \InvalidArgumentException('Password reset token do not match.');
        }

        $changeShopUserPassword = new ChangeShopUserPassword(
            $command->newPassword,
            $command->confirmNewPassword,
            ''
        );

        $changeShopUserPassword->setShopUserId($user->getId());

        $this->commandBus->dispatch(
            $changeShopUserPassword,
            [new DispatchAfterCurrentBusStamp()]
        );
    }
}
