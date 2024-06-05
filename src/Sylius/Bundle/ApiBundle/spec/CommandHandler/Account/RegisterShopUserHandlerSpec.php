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

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Account;

use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Account\RegisterShopUser;
use Sylius\Bundle\ApiBundle\Command\Account\SendAccountRegistrationEmail;
use Sylius\Bundle\ApiBundle\Command\Account\SendShopUserVerificationEmail;
use Sylius\Bundle\CoreBundle\Resolver\CustomerResolverInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final class RegisterShopUserHandlerSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $shopUserFactory,
        ObjectManager $shopUserManager,
        CustomerResolverInterface $customerResolver,
        ChannelRepositoryInterface $channelRepository,
        GeneratorInterface $generator,
        MessageBusInterface $commandBus,
    ): void {
        $this->beConstructedWith(
            $shopUserFactory,
            $shopUserManager,
            $customerResolver,
            $channelRepository,
            $generator,
            $commandBus,
        );
    }

    function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    function it_creates_a_shop_user_with_given_data(
        FactoryInterface $shopUserFactory,
        ObjectManager $shopUserManager,
        CustomerResolverInterface $customerResolver,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel,
        GeneratorInterface $generator,
        MessageBusInterface $commandBus,
    ): void {
        $command = new RegisterShopUser('Will', 'Smith', 'WILL.SMITH@example.com', 'iamrobot', 'CHANNEL_CODE', 'en_US', true);

        $shopUserFactory->createNew()->willReturn($shopUser);
        $customerResolver->resolve('WILL.SMITH@example.com')->willReturn($customer);

        $customer->getUser()->willReturn(null);

        $shopUser->setPlainPassword('iamrobot')->shouldBeCalled();

        $customer->setFirstName('Will')->shouldBeCalled();
        $customer->setLastName('Smith')->shouldBeCalled();
        $customer->setSubscribedToNewsletter(true)->shouldBeCalled();
        $customer->setUser($shopUser)->shouldBeCalled();

        $channelRepository->findOneByCode('CHANNEL_CODE')->willReturn($channel);
        $channel->isAccountVerificationRequired()->willReturn(true);
        $generator->generate()->willReturn('TOKEN');
        $shopUser->setEmailVerificationToken('TOKEN')->shouldBeCalled();

        $shopUserManager->persist($shopUser)->shouldBeCalled();

        $sendRegistrationEmailCommand = new SendAccountRegistrationEmail('WILL.SMITH@example.com', 'en_US', 'CHANNEL_CODE');
        $commandBus
            ->dispatch($sendRegistrationEmailCommand, [new DispatchAfterCurrentBusStamp()])
            ->shouldBeCalled()
            ->willReturn(new Envelope($sendRegistrationEmailCommand))
        ;

        $sendVerificationEmailCommand = new SendShopUserVerificationEmail('WILL.SMITH@example.com', 'en_US', 'CHANNEL_CODE');
        $commandBus
            ->dispatch($sendVerificationEmailCommand, [new DispatchAfterCurrentBusStamp()])
            ->shouldBeCalled()
            ->willReturn(new Envelope($sendVerificationEmailCommand))
        ;

        $this($command)->shouldReturn($shopUser);
    }

    function it_creates_a_shop_user_with_given_data_and_verifies_it(
        FactoryInterface $shopUserFactory,
        ObjectManager $shopUserManager,
        CustomerResolverInterface $customerResolver,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel,
        MessageBusInterface $commandBus,
    ): void {
        $command = new RegisterShopUser('Will', 'Smith', 'WILL.SMITH@example.com', 'iamrobot', 'CHANNEL_CODE', 'en_US', true);

        $shopUserFactory->createNew()->willReturn($shopUser);
        $customerResolver->resolve('WILL.SMITH@example.com')->willReturn($customer);

        $customer->getUser()->willReturn(null);

        $shopUser->setPlainPassword('iamrobot')->shouldBeCalled();

        $customer->setFirstName('Will')->shouldBeCalled();
        $customer->setLastName('Smith')->shouldBeCalled();
        $customer->setSubscribedToNewsletter(true)->shouldBeCalled();
        $customer->setUser($shopUser)->shouldBeCalled();

        $shopUserManager->persist($shopUser)->shouldBeCalled();

        $sendRegistrationEmailCommand = new SendAccountRegistrationEmail('WILL.SMITH@example.com', 'en_US', 'CHANNEL_CODE');
        $commandBus
            ->dispatch($sendRegistrationEmailCommand, [new DispatchAfterCurrentBusStamp()])
            ->shouldBeCalled()
            ->willReturn(new Envelope($sendRegistrationEmailCommand))
        ;

        $channelRepository->findOneByCode('CHANNEL_CODE')->willReturn($channel);
        $channel->isAccountVerificationRequired()->willReturn(false);
        $shopUser->setEnabled(true);

        $this($command)->shouldReturn($shopUser);
    }

    function it_throws_an_exception_if_customer_with_user_already_exists(
        FactoryInterface $shopUserFactory,
        ObjectManager $shopUserManager,
        CustomerResolverInterface $customerResolver,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        ShopUserInterface $existingShopUser,
        MessageBusInterface $commandBus,
    ): void {
        $shopUserFactory->createNew()->willReturn($shopUser);
        $customerResolver->resolve('WILL.SMITH@example.com')->willReturn($customer);

        $customer->getUser()->willReturn($existingShopUser);

        $shopUserManager->persist($shopUser)->shouldNotBeCalled();

        $sendRegistrationEmailCommand = new SendAccountRegistrationEmail('WILL.SMITH@example.com', 'en_US', 'CHANNEL_CODE');
        $commandBus->dispatch($sendRegistrationEmailCommand)->shouldNotBeCalled()->willReturn(new Envelope($sendRegistrationEmailCommand));

        $this
            ->shouldThrow(\DomainException::class)
            ->during('__invoke', [new RegisterShopUser('Will', 'Smith', 'WILL.SMITH@example.com', 'iamrobot', 'CHANNEL_CODE', 'en_US', true)])
        ;
    }
}
