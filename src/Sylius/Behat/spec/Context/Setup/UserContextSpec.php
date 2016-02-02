<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class UserContextSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $userRepository,
        SharedStorageInterface $sharedStorage,
        FactoryInterface $userFactory,
        FactoryInterface $customerFactory,
        FactoryInterface $addressFactory
    ) {
        $this->beConstructedWith($userRepository, $sharedStorage, $userFactory, $customerFactory, $addressFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Setup\UserContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_creates_user_with_given_credentials_and_default_data(
        $customerFactory,
        $sharedStorage,
        $userFactory,
        $userRepository,
        CustomerInterface $customer,
        UserInterface $user
    ) {
        $customerFactory->createNew()->willReturn($customer);
        $customer->setFirstName('John')->shouldBeCalled();
        $customer->setLastName('Doe')->shouldBeCalled();

        $customer->getFirstName()->willReturn('John');
        $customer->getLastName()->willReturn('Doe');

        $userFactory->createNew()->willReturn($user);
        $user->setCustomer($customer)->shouldBeCalled();
        $user->setUsername('John Doe')->shouldBeCalled();
        $user->setEmail('test@example.com')->shouldBeCalled();
        $user->setPlainPassword('pa$$word')->shouldBeCalled();
        $user->enable()->shouldBeCalled();

        $sharedStorage->setCurrentResource('user', $user)->shouldBeCalled();
        $userRepository->add($user)->shouldBeCalled();

        $this->thereIsUserIdentifiedBy('test@example.com', 'pa$$word');
    }

    function it_creates_user_with_given_credentials_default_data_and_shipping_address(
        $addressFactory,
        $customerFactory,
        $sharedStorage,
        $userFactory,
        $userRepository,
        AddressInterface $address,
        CustomerInterface $customer,
        UserInterface $user
    ) {
        $customerFactory->createNew()->willReturn($customer);
        $customer->setFirstName('John')->shouldBeCalled();
        $customer->setLastName('Doe')->shouldBeCalled();

        $customer->getFirstName()->willReturn('John');
        $customer->getLastName()->willReturn('Doe');

        $userFactory->createNew()->willReturn($user);
        $user->setCustomer($customer)->shouldBeCalled();
        $user->setUsername('John Doe')->shouldBeCalled();
        $user->setEmail('test@example.com')->shouldBeCalled();
        $user->setPlainPassword('pa$$word')->shouldBeCalled();
        $user->enable()->shouldBeCalled();

        $addressFactory->createNew()->willReturn($address);
        $address->setFirstName('John')->shouldBeCalled();
        $address->setLastName('Doe')->shouldBeCalled();
        $address->setStreet('Jones St. 114')->shouldBeCalled();
        $address->setCity('Paradise City')->shouldBeCalled();
        $address->setPostcode('99999')->shouldBeCalled();
        $address->setCountryCode('GB')->shouldBeCalled();

        $customer->setShippingAddress($address)->shouldBeCalled();

        $sharedStorage->setCurrentResource('user', $user)->shouldBeCalled();
        $userRepository->add($user)->shouldBeCalled();

        $this->thereIsUserWithShippingCountry('test@example.com', 'pa$$word', 'United Kingdom');
    }
}
