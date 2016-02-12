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
use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Test\Factory\TestUserFactory;
use Sylius\Bundle\CoreBundle\Test\Factory\TestUserFactoryInterface;
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
        TestUserFactory $userFactory,
        FactoryInterface $addressFactory,
        ObjectManager $userManager
    ) {
        $this->beConstructedWith(
            $userRepository,
            $sharedStorage,
            $userFactory,
            $addressFactory,
            $userManager
        );
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
        $sharedStorage,
        $userFactory,
        $userRepository,
        UserInterface $user
    ) {
        $userFactory->create('test@example.com', 'pa$$word')->willReturn($user);

        $sharedStorage->setCurrentResource('user', $user)->shouldBeCalled();
        $userRepository->add($user)->shouldBeCalled();

        $this->thereIsUserIdentifiedBy('test@example.com', 'pa$$word');
    }

    function it_creates_user_with_given_credentials_default_data_and_shipping_address(
        $addressFactory,
        $sharedStorage,
        $userFactory,
        $userRepository,
        AddressInterface $address,
        CustomerInterface $customer,
        UserInterface $user
    ) {
        $userFactory->create('test@example.com', 'pa$$word')->willReturn($user);
        $user->getCustomer()->willReturn($customer);

        $customer->getFirstName()->willReturn('John');
        $customer->getLastName()->willReturn('Doe');

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

    function it_sets_current_user_shipping_address(
        $addressFactory,
        $sharedStorage,
        $userManager,
        AddressInterface $address,
        CustomerInterface $customer,
        UserInterface $user
    ) {
        $sharedStorage->getCurrentResource('user')->willReturn($user);
        $user->getCustomer()->willReturn($customer);

        $customer->getFirstName()->willReturn('John');
        $customer->getLastName()->willReturn('Doe');

        $addressFactory->createNew()->willReturn($address);
        $address->setFirstName('John')->shouldBeCalled();
        $address->setLastName('Doe')->shouldBeCalled();
        $address->setStreet('Jones St. 114')->shouldBeCalled();
        $address->setCity('Paradise City')->shouldBeCalled();
        $address->setPostcode('99999')->shouldBeCalled();
        $address->setCountryCode('GB')->shouldBeCalled();

        $customer->setShippingAddress($address)->shouldBeCalled();

        $userManager->flush()->shouldBeCalled();

        $this->myDefaultShippingAddressIs('United Kingdom');
    }
}
