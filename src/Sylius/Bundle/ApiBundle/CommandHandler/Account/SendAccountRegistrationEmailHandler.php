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

use Sylius\Bundle\ApiBundle\Command\Account\SendAccountRegistrationEmail;
use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/** @experimental  */
final class SendAccountRegistrationEmailHandler implements MessageHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $shopUserRepository,
        private ChannelRepositoryInterface $channelRepository,
        private SenderInterface $emailSender,
    ) {
    }

    public function __invoke(SendAccountRegistrationEmail $command): void
    {
        /** @var ShopUserInterface $shopUser */
        $shopUser = $this->shopUserRepository->findOneByEmail($command->shopUserEmail);

        $channel = $this->channelRepository->findOneByCode($command->channelCode);

        $this->emailSender->send(
            Emails::USER_REGISTRATION,
            [$command->shopUserEmail],
            ['user' => $shopUser, 'localeCode' => $command->localeCode, 'channel' => $channel],
        );
    }
}
