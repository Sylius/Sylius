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

namespace Sylius\Bundle\ApiBundle\CommandHandler\Account;

use Sylius\Bundle\ApiBundle\Command\Account\SendResetPasswordEmail;
use Sylius\Bundle\CoreBundle\Mailer\ResetPasswordEmailManagerInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendResetPasswordEmailHandler implements MessageHandlerInterface
{
    /**
     * @param ChannelRepositoryInterface<ChannelInterface> $channelRepository
     * @param UserRepositoryInterface<UserInterface> $userRepository
     */
    public function __construct(
        private ChannelRepositoryInterface $channelRepository,
        private UserRepositoryInterface $userRepository,
        private ResetPasswordEmailManagerInterface $resetPasswordEmailManager,
    ) {
    }

    public function __invoke(SendResetPasswordEmail $command): void
    {
        $user = $this->userRepository->findOneByEmail($command->email);
        $channel = $this->channelRepository->findOneByCode($command->channelCode());

        $this->resetPasswordEmailManager->sendResetPasswordEmail($user, $channel, $command->localeCode());
    }
}
