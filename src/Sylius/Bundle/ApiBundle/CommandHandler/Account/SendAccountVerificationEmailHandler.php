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

use Sylius\Bundle\ApiBundle\Command\Account\SendAccountVerificationEmail;
use Sylius\Bundle\ApiBundle\Exception\ChannelNotFoundException;
use Sylius\Bundle\ApiBundle\Exception\UserNotFoundException;
use Sylius\Bundle\CoreBundle\Mailer\AccountVerificationEmailManagerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendAccountVerificationEmailHandler implements MessageHandlerInterface
{
    /**
     * @param UserRepositoryInterface<ShopUserInterface> $shopUserRepository
     * @param ChannelRepositoryInterface<ChannelInterface> $channelRepository
     */
    public function __construct(
        private UserRepositoryInterface $shopUserRepository,
        private ChannelRepositoryInterface $channelRepository,
        private AccountVerificationEmailManagerInterface $accountVerificationEmailManager,
    ) {
    }

    public function __invoke(SendAccountVerificationEmail $command): void
    {
        $shopUser = $this->shopUserRepository->findOneByEmail($command->shopUserEmail);

        if ($shopUser === null) {
            throw new UserNotFoundException(sprintf('User with email %s has not been found.', $command->shopUserEmail));
        }

        $channel = $this->channelRepository->findOneByCode($command->channelCode);

        if ($channel === null) {
            throw new ChannelNotFoundException(
                sprintf('Channel with code %s has not been found.', $command->channelCode),
            );
        }

        $this->accountVerificationEmailManager->sendAccountVerificationEmail(
            $shopUser,
            $channel,
            $command->localeCode,
        );
    }
}
