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
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class RegisterShopUserHandlerSpec extends ObjectBehavior
{
    function let(
        CanonicalizerInterface $canonicalizer,
        FactoryInterface $shopUserFactory,
        FactoryInterface $customerFactory,
        CustomerRepositoryInterface $customerRepository,
        ObjectManager $shopUserManager
    ): void {
        $this->beConstructedWith(
            $canonicalizer,
            $shopUserFactory,
            $customerFactory,
            $customerRepository,
            $shopUserManager
        );
    }

    function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    function it_creates_a_customer_and_user_with_given_data(
        CanonicalizerInterface $canonicalizer,
        FactoryInterface $shopUserFactory,
        FactoryInterface $customerFactory,
        CustomerRepositoryInterface $customerRepository,
        ObjectManager $shopUserManager,
        ShopUserInterface $shopUser,
        CustomerInterface $customer
    ): void {
        $canonicalizer->canonicalize('WILL.SMITH@example.com')->willReturn('will.smith@example.com');
        $customerRepository->findOneBy(['emailCanonical' => 'will.smith@example.com'])->willReturn(null);

        $shopUserFactory->createNew()->willReturn($shopUser);
        $customerFactory->createNew()->willReturn($customer);

        $customer->getUser()->willReturn(null);

        $shopUser->setPlainPassword('iamrobot')->shouldBeCalled();

        $customer->setFirstName('Will')->shouldBeCalled();
        $customer->setLastName('Smith')->shouldBeCalled();
        $customer->setEmail('WILL.SMITH@example.com')->shouldBeCalled();
        $customer->setPhoneNumber('+13104322400')->shouldBeCalled();
        $customer->setUser($shopUser)->shouldBeCalled();

        $shopUserManager->persist($shopUser)->shouldBeCalled();

        $this(new RegisterShopUser('Will', 'Smith', 'WILL.SMITH@example.com', 'iamrobot', '+13104322400'));
    }

    function it_creates_only_a_user_if_customer_without_user_already_exists(
        CanonicalizerInterface $canonicalizer,
        FactoryInterface $shopUserFactory,
        FactoryInterface $customerFactory,
        CustomerRepositoryInterface $customerRepository,
        ObjectManager $shopUserManager,
        ShopUserInterface $shopUser,
        CustomerInterface $customer
    ): void {
        $canonicalizer->canonicalize('WILL.SMITH@example.com')->willReturn('will.smith@example.com');
        $customerRepository->findOneBy(['emailCanonical' => 'will.smith@example.com'])->willReturn($customer);

        $shopUserFactory->createNew()->willReturn($shopUser);
        $customerFactory->createNew()->shouldNotBeCalled();

        $customer->getUser()->willReturn(null);

        $shopUser->setPlainPassword('iamrobot')->shouldBeCalled();

        $customer->setFirstName('Will')->shouldBeCalled();
        $customer->setLastName('Smith')->shouldBeCalled();
        $customer->setEmail('WILL.SMITH@example.com')->shouldBeCalled();
        $customer->setPhoneNumber('+13104322400')->shouldBeCalled();
        $customer->setUser($shopUser)->shouldBeCalled();

        $shopUserManager->persist($shopUser)->shouldBeCalled();

        $this(new RegisterShopUser('Will', 'Smith', 'WILL.SMITH@example.com', 'iamrobot', '+13104322400'));
    }

    function it_throws_an_exception_if_customer_with_user_already_exists(
        CanonicalizerInterface $canonicalizer,
        FactoryInterface $shopUserFactory,
        FactoryInterface $customerFactory,
        CustomerRepositoryInterface $customerRepository,
        ObjectManager $shopUserManager,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        ShopUserInterface $existingShopUser
    ): void {
        $canonicalizer->canonicalize('WILL.SMITH@example.com')->willReturn('will.smith@example.com');
        $customerRepository->findOneBy(['emailCanonical' => 'will.smith@example.com'])->willReturn($customer);

        $customer->getUser()->willReturn($existingShopUser);

        $shopUserFactory->createNew()->willReturn($shopUser);
        $customerFactory->createNew()->shouldNotBeCalled();

        $shopUserManager->persist($shopUser)->shouldNotBeCalled();

        $this->shouldThrow(\DomainException::class)->during('__invoke', [new RegisterShopUser('Will', 'Smith', 'WILL.SMITH@example.com', 'iamrobot', '+13104322400')]);
    }
}
