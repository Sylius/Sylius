<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Test\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Test\Factory\UserFactoryInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class UserFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $customerFactory, FactoryInterface $userFactory)
    {
        $this->beConstructedWith($customerFactory, $userFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Test\Factory\UserFactory');
    }

    function it_implements_user_provider_interface()
    {
        $this->shouldImplement(UserFactoryInterface::class);
    }

    function it_creates_user_with_customer(
        $customerFactory,
        $userFactory,
        CustomerInterface $customer,
        UserInterface $user
    ) {
        $customerFactory->createNew()->willReturn($customer);
        $customer->setFirstName('John')->shouldBeCalled();
        $customer->setLastName('Doe')->shouldBeCalled();

        $userFactory->createNew()->willReturn($user);

        $user->setCustomer($customer)->shouldBeCalled();
        $user->setUsername('john.doe@example.com')->shouldBeCalled();
        $user->setEmail('john.doe@example.com')->shouldBeCalled();
        $user->setPlainPassword('password123')->shouldBeCalled();
        $user->enable()->shouldBeCalled();

        $this->create('John', 'Doe', 'john.doe@example.com', 'password123')->shouldReturn($user);
    }
}
