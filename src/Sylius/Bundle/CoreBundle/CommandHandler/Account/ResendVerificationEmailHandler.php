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

namespace Sylius\Bundle\CoreBundle\CommandHandler\Account;

use Sylius\Bundle\CoreBundle\Command\Account\ResendVerificationEmail;
use Sylius\Bundle\CoreBundle\Mailer\Emails as CoreEmails;
use Sylius\Bundle\UserBundle\Mailer\Emails as UserEmails;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ResendVerificationEmailHandler
{
    /**
     * @param UserRepositoryInterface<ShopUserInterface> $shopUserRepository
     * @param ChannelRepositoryInterface<ChannelInterface> $channelRepository
     */
    public function __construct(
        private UserRepositoryInterface $shopUserRepository,
        private ChannelRepositoryInterface $channelRepository,
        private GeneratorInterface $tokenGenerator,
        private SenderInterface $emailSender,
        private ObjectManager $shopUserManager,
    ) {
    }

    public function __invoke(ResendVerificationEmail $command): void
    {
        /** @var ShopUserInterface|null $user */
        $user = $this->shopUserRepository->findOneByEmail($command->email);

        if (null === $user || $user->isVerified()) {
            return;
        }

        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->findOneByCode($command->channelCode);

        if (null === $channel || !$channel->isAccountVerificationRequired()) {
            return;
        }

        $user->setEmailVerificationToken($this->tokenGenerator->generate());
        $this->shopUserManager->flush();

        $this->emailSender->send(
            $command->sendVerificationLink ? UserEmails::EMAIL_VERIFICATION_TOKEN : CoreEmails::ACCOUNT_VERIFICATION,
            [$user->getEmail()],
            [
                'user' => $user,
                'channel' => $channel,
                'localeCode' => $command->localeCode,
            ],
        );
    }
}
