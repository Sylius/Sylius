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

namespace Sylius\Bundle\CoreBundle\CommandHandler\Shop\Account;

use Sylius\Bundle\CoreBundle\Command\Shop\Account\SendShopUserVerificationEmail;
use Sylius\Bundle\CoreBundle\Mailer\AccountVerificationEmailManagerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SendShopUserVerificationEmailHandler
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

    public function __invoke(SendShopUserVerificationEmail $command): void
    {
        $shopUser = $this->shopUserRepository->findOneByEmail($command->shopUserEmail);
        if (null === $shopUser) {
            throw new \InvalidArgumentException(
                sprintf('There is no shop user with %s email', $command->shopUserEmail),
            );
        }

        $channel = $this->channelRepository->findOneByCode($command->channelCode);
        if (null === $channel) {
            throw new \InvalidArgumentException(
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
