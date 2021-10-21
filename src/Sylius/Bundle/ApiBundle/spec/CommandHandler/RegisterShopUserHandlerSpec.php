<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler;

use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\RegisterShopUser;
use Sylius\Bundle\ApiBundle\Provider\CustomerProviderInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class RegisterShopUserHandlerSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $shopUserFactory,
        ObjectManager $shopUserManager,
        CustomerProviderInterface $customerProvider,
        ChannelContextInterface $channelContext
    ): void {
        $this->beConstructedWith($shopUserFactory, $shopUserManager, $customerProvider, $channelContext);
    }

    function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    function it_creates_a_shop_user_with_given_data(
        FactoryInterface $shopUserFactory,
        ObjectManager $shopUserManager,
        CustomerProviderInterface $customerProvider,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        ChannelContextInterface $channelContext,
        Channel $channel
    ): void {
        $shopUserFactory->createNew()->willReturn($shopUser);
        $customerProvider->provide('WILL.SMITH@example.com')->willReturn($customer);

        $customer->getUser()->willReturn(null);

        $shopUser->setPlainPassword('iamrobot')->shouldBeCalled();

        $customer->setFirstName('Will')->shouldBeCalled();
        $customer->setLastName('Smith')->shouldBeCalled();
        $customer->setPhoneNumber('+13104322400')->shouldBeCalled();
        $customer->setUser($shopUser)->shouldBeCalled();

        $channelContext->getChannel()->willReturn($channel);
        $channel->isAccountVerificationRequired()->willReturn(true);

        $shopUserManager->persist($shopUser)->shouldBeCalled();

        $this(
            new RegisterShopUser('Will', 'Smith', 'WILL.SMITH@example.com', 'iamrobot', '+13104322400')
        )->shouldReturn($shopUser);
    }

    function it_creates_a_shop_user_with_given_data_and_verifies_it(
        FactoryInterface $shopUserFactory,
        ObjectManager $shopUserManager,
        CustomerProviderInterface $customerProvider,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        ChannelContextInterface $channelContext,
        Channel $channel
    ): void {
        $shopUserFactory->createNew()->willReturn($shopUser);
        $customerProvider->provide('WILL.SMITH@example.com')->willReturn($customer);

        $customer->getUser()->willReturn(null);

        $shopUser->setPlainPassword('iamrobot')->shouldBeCalled();

        $customer->setFirstName('Will')->shouldBeCalled();
        $customer->setLastName('Smith')->shouldBeCalled();
        $customer->setPhoneNumber('+13104322400')->shouldBeCalled();
        $customer->setUser($shopUser)->shouldBeCalled();

        $channelContext->getChannel()->willReturn($channel);
        $channel->isAccountVerificationRequired()->willReturn(false);
        $shopUser->setEnabled(true)->shouldBeCalled();

        $shopUserManager->persist($shopUser)->shouldBeCalled();

        $this(
            new RegisterShopUser('Will', 'Smith', 'WILL.SMITH@example.com', 'iamrobot', '+13104322400')
        )->shouldReturn($shopUser);
    }

    function it_throws_an_exception_if_customer_with_user_already_exists(
        FactoryInterface $shopUserFactory,
        ObjectManager $shopUserManager,
        CustomerProviderInterface $customerProvider,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        ShopUserInterface $existingShopUser
    ): void {
        $shopUserFactory->createNew()->willReturn($shopUser);
        $customerProvider->provide('WILL.SMITH@example.com')->willReturn($customer);

        $customer->getUser()->willReturn($existingShopUser);

        $shopUserManager->persist($shopUser)->shouldNotBeCalled();

        $this
            ->shouldThrow(\DomainException::class)
            ->during('__invoke', [new RegisterShopUser('Will', 'Smith', 'WILL.SMITH@example.com', 'iamrobot', '+13104322400')])
        ;
    }
}
