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

use Sylius\Bundle\CoreBundle\Command\Shop\Account\SendAccountRegistrationEmail;
use Sylius\Bundle\CoreBundle\Mailer\AccountRegistrationEmailManagerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SendAccountRegistrationEmailHandler
{
    public function __construct(
        private UserRepositoryInterface $shopUserRepository,
        private ChannelRepositoryInterface $channelRepository,
        private AccountRegistrationEmailManagerInterface $accountRegistrationEmailManager,
    ) {
    }

    public function __invoke(SendAccountRegistrationEmail $command): void
    {
        /** @var ShopUserInterface|null $shopUser */
        $shopUser = $this->shopUserRepository->findOneByEmail($command->shopUserEmail);
        if (null === $shopUser) {
            throw new \InvalidArgumentException(
                sprintf('There is no shop user with %s email', $command->shopUserEmail),
            );
        }

        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->findOneByCode($command->channelCode);
        if (null === $channel) {
            throw new \InvalidArgumentException(
                sprintf('There is no channel with %s code', $command->channelCode),
            );
        }

        if ($channel->isAccountVerificationRequired() && !$shopUser->isEnabled()) {
            return;
        }

        $this->accountRegistrationEmailManager->sendAccountRegistrationEmail($shopUser, $channel, $command->localeCode);
    }
}
