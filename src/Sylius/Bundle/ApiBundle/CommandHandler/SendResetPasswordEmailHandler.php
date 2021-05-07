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

use Sylius\Bundle\ApiBundle\Command\SendResetPasswordEmail;
use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/** @experimental */
final class SendResetPasswordEmailHandler implements MessageHandlerInterface
{
    /** @var SenderInterface */
    private $emailSender;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var UserRepositoryInterface */
    private $userRepository;

    public function __construct(
        SenderInterface $emailSender,
        ChannelRepositoryInterface $channelRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->emailSender = $emailSender;
        $this->channelRepository = $channelRepository;
        $this->userRepository = $userRepository;
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
            ]
        );
    }
}
