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

namespace spec\Sylius\Bundle\ApiBundle\DataProvider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

final class CustomerItemDataProviderSpec extends ObjectBehavior
{
    function let(UserContextInterface $userContext, CustomerRepositoryInterface $customerRepository): void
    {
        $this->beConstructedWith($userContext, $customerRepository);
    }

    function it_supports_only_customer(): void
    {
        $this->supports(CustomerInterface::class, Request::METHOD_PUT)->shouldReturn(true);
        $this->supports(OrderInterface::class, Request::METHOD_PUT)->shouldReturn(false);
    }

    function it_provides_customer_by_id_for_logged_in_admin_user(
        UserContextInterface $userContext,
        AdminUserInterface $user,
        CustomerRepositoryInterface $customerRepository,
        CustomerInterface $customer,
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getRoles()->willReturn(['ROLE_API_ACCESS']);

        $customerRepository->find('1')->willReturn($customer);

        $this
            ->getItem(
                CustomerInterface::class,
                '1',
                Request::METHOD_PUT,
                [],
            )
            ->shouldReturn($customer)
        ;
    }

    function it_provides_customer_by_id_for_logged_in_same_customer(
        UserContextInterface $userContext,
        ShopUserInterface $user,
        CustomerInterface $customer,
        CustomerRepositoryInterface $customerRepository,
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getCustomer()->willReturn($customer);
        $customer->getId()->willReturn('1');

        $customerRepository->find('1')->willReturn($customer);

        $this
            ->getItem(
                CustomerInterface::class,
                '1',
                Request::METHOD_PUT,
                [],
            )
            ->shouldReturn($customer)
        ;
    }

    function it_provides_null_when_logged_in_customer_try_to_get_another_customer(
        UserContextInterface $userContext,
        ShopUserInterface $user,
        CustomerInterface $customer,
        CustomerRepositoryInterface $customerRepository,
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getCustomer()->willReturn($customer);
        $customer->getId()->willReturn('1');

        $customerRepository->find('2')->shouldNotBeCalled();

        $this
            ->getItem(
                CustomerInterface::class,
                '2',
                Request::METHOD_PUT,
                [],
            )
            ->shouldReturn(null)
        ;
    }

    function it_provides_customer_by_id_for_email_verification_purpose(
        UserContextInterface $userContext,
        ShopUserInterface $user,
        CustomerInterface $customer,
        CustomerRepositoryInterface $customerRepository,
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getCustomer()->willReturn($customer);
        $customer->getId()->willReturn('1');

        $customerRepository->find('1')->willReturn($customer);

        $this
            ->getItem(
                CustomerInterface::class,
                '1',
                'shop_verify_customer_account',
                [],
            )
            ->shouldReturn($customer)
        ;
    }
}
