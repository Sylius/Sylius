<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class CustomerContextSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $customerRepository,
        FactoryInterface $customerFactory
    ) {
        $this->beConstructedWith($customerRepository, $customerFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Transform\CustomerContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_creates_new_customer_if_it_does_not_exist(
        CustomerInterface $customer,
        FactoryInterface $customerFactory,
        RepositoryInterface $customerRepository
    ) {
        $customerRepository->findOneBy(['email' => 'oliver.queen@star.com'])->willReturn(null);

        $customerFactory->createNew()->willReturn($customer);
        $customer->setEmail('oliver.queen@star.com')->shouldBeCalled();

        $customerRepository->add($customer)->shouldBeCalled();

        $this->getOrCreateCustomerByEmail('oliver.queen@star.com')->shouldReturn($customer);
    }

    function it_provides_new_customer_from_repository_if_it_exists(
        CustomerInterface $customer,
        RepositoryInterface $customerRepository
    ) {
        $customerRepository->findOneBy(['email' => 'oliver.queen@star.com'])->willReturn($customer);

        $this->getOrCreateCustomerByEmail('oliver.queen@star.com')->shouldReturn($customer);
    }
}
