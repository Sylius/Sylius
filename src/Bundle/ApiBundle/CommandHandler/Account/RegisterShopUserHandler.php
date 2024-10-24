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

use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\ApiBundle\Command\Account\RegisterShopUser;
use Sylius\Bundle\ApiBundle\Command\Account\SendAccountRegistrationEmail;
use Sylius\Bundle\ApiBundle\Command\Account\SendShopUserVerificationEmail;
use Sylius\Bundle\CoreBundle\Resolver\CustomerResolverInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

#[AsMessageHandler]
final readonly class RegisterShopUserHandler
{
    /** @param FactoryInterface<ShopUserInterface> $shopUserFactory */
    public function __construct(
        private FactoryInterface $shopUserFactory,
        private ObjectManager $shopUserManager,
        private CustomerResolverInterface $customerResolver,
        private ChannelRepositoryInterface $channelRepository,
        private GeneratorInterface $tokenGenerator,
        private MessageBusInterface $commandBus,
    ) {
    }

    public function __invoke(RegisterShopUser $command): ShopUserInterface
    {
        /** @var ShopUserInterface $user */
        $user = $this->shopUserFactory->createNew();
        $user->setPlainPassword($command->password);

        $customer = $this->customerResolver->resolve($command->email);

        if ($customer->getUser() !== null) {
            throw new \DomainException(sprintf('User with email "%s" is already registered.', $command->email));
        }

        $customer->setFirstName($command->firstName);
        $customer->setLastName($command->lastName);
        $customer->setSubscribedToNewsletter($command->subscribedToNewsletter);
        $customer->setUser($user);

        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($command->channelCode);

        $this->shopUserManager->persist($user);

        $this->commandBus->dispatch(new SendAccountRegistrationEmail(
            $command->email,
            $command->localeCode,
            $command->channelCode,
        ), [new DispatchAfterCurrentBusStamp()]);

        if (!$channel->isAccountVerificationRequired()) {
            $user->setEnabled(true);

            return $user;
        }

        $token = $this->tokenGenerator->generate();
        $user->setEmailVerificationToken($token);

        $this->commandBus->dispatch(new SendShopUserVerificationEmail(
            $command->email,
            $command->localeCode,
            $command->channelCode,
        ), [new DispatchAfterCurrentBusStamp()]);

        return $user;
    }
}
