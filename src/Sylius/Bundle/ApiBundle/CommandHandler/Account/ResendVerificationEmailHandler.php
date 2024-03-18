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

use Sylius\Bundle\ApiBundle\Command\Account\ResendVerificationEmail;
use Sylius\Bundle\ApiBundle\Command\Account\SendAccountVerificationEmail;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use Webmozart\Assert\Assert;

final class ResendVerificationEmailHandler implements MessageHandlerInterface
{
    /**
     * @param UserRepositoryInterface<ShopUserInterface> $shopUserRepository
     */
    public function __construct(
        private UserRepositoryInterface $shopUserRepository,
        private GeneratorInterface $tokenGenerator,
        private MessageBusInterface $commandBus,
    ) {
    }

    public function __invoke(ResendVerificationEmail $command): void
    {
        /** @var ShopUserInterface|null $user */
        $user = $this->shopUserRepository->find($command->getShopUserId());
        Assert::notNull($user);

        $token = $this->tokenGenerator->generate();
        $user->setEmailVerificationToken($token);
        /** @var CustomerInterface $customer */
        $customer = $user->getCustomer();

        $this->commandBus->dispatch(new SendAccountVerificationEmail(
            $customer->getEmail(),
            $command->getLocaleCode(),
            $command->getChannelCode(),
        ), [new DispatchAfterCurrentBusStamp()]);
    }
}
