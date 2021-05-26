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

use Lexik\Bundle\JWTAuthenticationBundle\Exception\MissingTokenException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;

final class AddressItemDataProviderSpec extends ObjectBehavior
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

    function it_provides_address_for_shop_user(
        AddressRepositoryInterface $addressRepository,
        UserContextInterface $userContext,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        AddressInterface $address
    ) {
        $userContext->getUser()->willReturn($shopUser);

        $shopUser->getCustomer()->willReturn($customer);
        $shopUser->getRoles()->willReturn(['ROLE_USER']);

        $addressRepository
            ->findOneByCustomer('123', $customer->getWrappedObject())
            ->willReturn($address)
        ;

        $this->getItem(AddressInterface::class, '123')->shouldReturn($address);
    }

    function it_provides_address_for_admin_user(
        AddressRepositoryInterface $addressRepository,
        UserContextInterface $userContext,
        AdminUserInterface $adminUser,
        AddressInterface $address
    ) {
        $userContext->getUser()->willReturn($adminUser);

        $adminUser->getRoles()->willReturn(['ROLE_API_ACCESS']);

        $addressRepository->findOneBy(['id' => '123'])->willReturn($address);

        $this->getItem(AddressInterface::class, '123')->shouldReturn($address);
    }

    function it_return_null_for_shop_user_with_null_customer(
        AddressRepositoryInterface $addressRepository,
        UserContextInterface $userContext,
        ShopUserInterface $shopUser
    ) {
        $userContext->getUser()->willReturn($shopUser);

        $shopUser->getCustomer()->willReturn(null);
        $shopUser->getRoles()->willReturn(['ROLE_USER']);

        $addressRepository->findOneByCustomer('123', new Customer())->shouldNotBeCalled();

        $this->getItem(AddressInterface::class, '123')->shouldReturn(null);
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

        $addressRepository->findOneByCustomer('123', new Customer())->shouldNotBeCalled();

        $this->getItem(AddressInterface::class, '123')->shouldReturn(null);
    }

    function it_throws_an_exception_if_there_is_not_logged_in_user(
        AddressRepositoryInterface $addressRepository,
        UserContextInterface $userContext
    ): void {
        $userContext->getUser()->willReturn(null);

        $addressRepository->findOneByCustomer('123', Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(MissingTokenException::class)
            ->during('getItem', [AddressInterface::class, '123'])
        ;
    }
}
