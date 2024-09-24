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

use InvalidArgumentException;
use Sylius\Bundle\ApiBundle\Command\Account\SendAccountRegistrationEmail;
use Sylius\Bundle\ApiBundle\Command\Account\VerifyShopUser;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

#[AsMessageHandler]
final readonly class VerifyShopUserHandler
{
    /** @param RepositoryInterface<ShopUserInterface> $shopUserRepository */
    public function __construct(
        private RepositoryInterface $shopUserRepository,
        private ClockInterface $clock,
        private MessageBusInterface $commandBus,
    ) {
    }

    public function __invoke(VerifyShopUser $command): void
    {
        /** @var ShopUserInterface|null $user */
        $user = $this->shopUserRepository->findOneBy(['emailVerificationToken' => $command->token]);
        if (null === $user) {
            throw new InvalidArgumentException(
                sprintf('There is no shop user with %s email verification token', $command->token),
            );
        }

        $user->setVerifiedAt($this->clock->now());
        $user->setEmailVerificationToken(null);
        $user->enable();

        $this->commandBus->dispatch(
            new SendAccountRegistrationEmail($user->getEmail(), $command->localeCode, $command->channelCode),
            [new DispatchAfterCurrentBusStamp()],
        );
    }
}
