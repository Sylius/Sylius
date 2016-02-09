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
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\Core\Test\Factory\TestUserFactoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class TestUserFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $customerFactory, FactoryInterface $userFactory)
    {
        $this->beConstructedWith($customerFactory, $userFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Test\Factory\TestUserFactory');
    }

    function it_implements_user_provider_interface()
    {
        $this->shouldImplement(TestUserFactoryInterface::class);
    }

    function it_creates_user_with_customer(
        $customerFactory,
        $userFactory,
        CustomerInterface $customer,
        UserInterface $user
    ) {
        $customerFactory->createNew()->willReturn($customer);
        $customer->setFirstName('Oliver')->shouldBeCalled();
        $customer->setLastName('Queen')->shouldBeCalled();

        $userFactory->createNew()->willReturn($user);

        $user->setCustomer($customer)->shouldBeCalled();
        $user->setUsername('oliver.queen@star.com')->shouldBeCalled();
        $user->setEmail('oliver.queen@star.com')->shouldBeCalled();
        $user->setPlainPassword('a££ow')->shouldBeCalled();
        $user->enable()->shouldBeCalled();
        $user->addRole('ROLE_USER')->shouldBeCalled();

        $this->create('oliver.queen@star.com', 'a££ow', 'Oliver', 'Queen')->shouldReturn($user);
    }

    function it_creates_default_user_with_customer(
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
        $user->addRole('ROLE_USER')->shouldBeCalled();

        $this->createDefault()->shouldReturn($user);
    }

    function it_creates_default_admin(
        $customerFactory,
        $userFactory,
        CustomerInterface $customer,
        UserInterface $admin
    ) {
        $customerFactory->createNew()->willReturn($customer);
        $customer->setFirstName('John')->shouldBeCalled();
        $customer->setLastName('Doe')->shouldBeCalled();

        $userFactory->createNew()->willReturn($admin);

        $admin->setCustomer($customer)->shouldBeCalled();
        $admin->setUsername('admin@example.com')->shouldBeCalled();
        $admin->setEmail('admin@example.com')->shouldBeCalled();
        $admin->setPlainPassword('password123')->shouldBeCalled();
        $admin->enable()->shouldBeCalled();
        $admin->addRole('ROLE_ADMINISTRATION_ACCESS')->shouldBeCalled();

        $this->createDefaultAdmin()->shouldReturn($admin);
    }
}
