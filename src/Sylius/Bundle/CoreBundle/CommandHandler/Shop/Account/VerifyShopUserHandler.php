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
use Sylius\Bundle\CoreBundle\Command\Shop\Account\VerifyShopUser;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use Webmozart\Assert\Assert;

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
        Assert::notNull($user, sprintf('There is no shop user with %s email verification token', $command->token));

        $user->setVerifiedAt($this->clock->now());
        $user->setEmailVerificationToken(null);
        $user->enable();

        $this->commandBus->dispatch(
            new SendAccountRegistrationEmail($user->getEmail(), $command->channelCode, $command->localeCode),
            [new DispatchAfterCurrentBusStamp()],
        );
    }
}
