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

namespace spec\Sylius\Bundle\ApiBundle\DataProvider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;

final class AddressCollectionDataProviderSpec extends ObjectBehavior
{
    function let(
        AddressRepositoryInterface $addressRepository,
        UserContextInterface $userContext
    ): void {
        $this->beConstructedWith($addressRepository, $userContext);
    }

    function it_supports_only_address(): void
    {
        $this->supports(AddressInterface::class, 'get')->shouldReturn(true);
        $this->supports(ProductInterface::class, 'get')->shouldReturn(false);
    }

    function it_provides_all_shop_users_addresses(
        AddressRepositoryInterface $addressRepository,
        UserContextInterface $userContext,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        AddressInterface $firstAddress,
        AddressInterface $secondAddress
    ) {
        $userContext->getUser()->willReturn($shopUser);

        $shopUser->getCustomer()->willReturn($customer);
        $shopUser->getRoles()->willReturn(['ROLE_USER']);

        $addressRepository
            ->findByCustomer($customer->getWrappedObject())
            ->willReturn([$firstAddress, $secondAddress])
        ;

        $this->getCollection(AddressInterface::class)->shouldReturn([$firstAddress, $secondAddress]);
    }

    function it_provides_all_addresses_for_admin_user(
        AddressRepositoryInterface $addressRepository,
        UserContextInterface $userContext,
        AdminUserInterface $adminUser,
        AddressInterface $firstAddress,
        AddressInterface $secondAddress
    ) {
        $userContext->getUser()->willReturn($adminUser);

        $adminUser->getRoles()->willReturn(['ROLE_API_ACCESS']);

        $addressRepository->findAll()->willReturn([$firstAddress, $secondAddress]);

        $this->getCollection(AddressInterface::class)->shouldReturn([$firstAddress, $secondAddress]);
    }

    function it_provides_empty_array_for_shop_user_with_null_customer(
        AddressRepositoryInterface $addressRepository,
        UserContextInterface $userContext,
        ShopUserInterface $shopUser
    ) {
        $userContext->getUser()->willReturn($shopUser);

        $shopUser->getCustomer()->willReturn(null);
        $shopUser->getRoles()->willReturn(['ROLE_USER']);

        $addressRepository->findByCustomer(new Customer())->shouldNotBeCalled();

        $this->getCollection(AddressInterface::class)->shouldReturn([]);
    }

    function it_provides_empty_array_for_shop_user_without_properly_roles(
        AddressRepositoryInterface $addressRepository,
        UserContextInterface $userContext,
        CustomerInterface $customer,
        ShopUserInterface $shopUser
    ) {
        $userContext->getUser()->willReturn($shopUser);

        $shopUser->getCustomer()->willReturn($customer);
        $shopUser->getRoles()->willReturn(['']);

        $addressRepository->findByCustomer($customer->getWrappedObject())->shouldNotBeCalled();

        $this->getCollection(AddressInterface::class)->shouldReturn([]);
    }
}
