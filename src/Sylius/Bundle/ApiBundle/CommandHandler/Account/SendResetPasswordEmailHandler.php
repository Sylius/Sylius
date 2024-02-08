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
use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/** @experimental */
final class SendResetPasswordEmailHandler implements MessageHandlerInterface
{
    public function __construct(
        private SenderInterface $emailSender,
        private ChannelRepositoryInterface $channelRepository,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(SendResetPasswordEmail $command)
    {
        $user = $this->userRepository->findOneByEmail($command->email);
        $channel = $this->channelRepository->findOneByCode($command->channelCode());

        $this->emailSender->send(
            Emails::PASSWORD_RESET,
            [$command->email],
            [
                'user' => $user,
                'localeCode' => $command->localeCode(),
                'channel' => $channel,
            ],
        );
    }
}
