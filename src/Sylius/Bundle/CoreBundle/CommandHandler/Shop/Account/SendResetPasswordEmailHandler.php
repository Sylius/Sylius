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

use Sylius\Bundle\CoreBundle\Command\Shop\Account\SendResetPasswordEmail;
use Sylius\Bundle\CoreBundle\Mailer\ResetPasswordEmailManagerInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Webmozart\Assert\Assert;

#[AsMessageHandler]
final readonly class SendResetPasswordEmailHandler
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

    public function __invoke(SendResetPasswordEmail $sendResetPasswordEmail): void
    {
        $shopUser = $this->userRepository->findOneByEmail($sendResetPasswordEmail->email);
        Assert::notNull($shopUser);

        $channel = $this->channelRepository->findOneByCode($sendResetPasswordEmail->channelCode);
        Assert::notNull($channel);

        $this->resetPasswordEmailManager->sendResetPasswordEmail($shopUser, $channel, $sendResetPasswordEmail->localeCode);
    }
}
