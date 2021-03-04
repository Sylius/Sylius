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

use Sylius\Bundle\ApiBundle\Command\SendAccountVerificationEmail;
use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/** @experimental  */
final class SendAccountVerificationEmailHandler implements MessageHandlerInterface
{
    /** @var UserRepositoryInterface */
    private $shopUserRepository;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var SenderInterface */
    private $emailSender;

    public function __construct(
        UserRepositoryInterface $shopUserRepository,
        ChannelRepositoryInterface $channelRepository,
        SenderInterface $emailSender
    ) {
        $this->shopUserRepository = $shopUserRepository;
        $this->channelRepository = $channelRepository;
        $this->emailSender = $emailSender;
    }

    public function __invoke(SendAccountVerificationEmail $command): void
    {
        /** @var ShopUserInterface $shopUser */
        $shopUser = $this->shopUserRepository->findOneByEmail($command->shopUserEmail);

        $channel = $this->channelRepository->findOneByCode($command->channelCode);

        $this->emailSender->send(
            Emails::ACCOUNT_VERIFICATION_TOKEN,
            [$shopUser->getEmail()],
            ['user' => $shopUser, 'localeCode' => $command->localeCode, 'channel' => $channel]
        );
    }
}
