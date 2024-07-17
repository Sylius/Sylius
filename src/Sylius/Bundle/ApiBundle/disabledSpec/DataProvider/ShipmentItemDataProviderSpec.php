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
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

final class ShipmentItemDataProviderSpec extends ObjectBehavior
{
    function let(ShipmentRepositoryInterface $shipmentRepository, UserContextInterface $userContext): void
    {
        $this->beConstructedWith($shipmentRepository, $userContext);
    }

    function it_supports_only_shipment(): void
    {
        $this->supports(ShipmentInterface::class, 'get')->shouldReturn(true);
        $this->supports(ResourceInterface::class, 'get')->shouldReturn(false);
    }

    function it_provides_shipment_for_shop_user(
        ShipmentRepositoryInterface $shipmentRepository,
        UserContextInterface $userContext,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        ShipmentInterface $shipment,
    ) {
        $userContext->getUser()->willReturn($shopUser);

        $shopUser->getCustomer()->willReturn($customer);
        $shopUser->getRoles()->willReturn(['ROLE_USER']);

        $shipmentRepository
            ->findOneByCustomer('123', $customer->getWrappedObject())
            ->willReturn($shipment)
        ;

        $this->getItem(ShipmentInterface::class, '123')->shouldReturn($shipment);
    }

    function it_provides_shipment_for_admin_user(
        ShipmentRepositoryInterface $shipmentRepository,
        UserContextInterface $userContext,
        AdminUserInterface $adminUser,
        ShipmentInterface $shipment,
    ) {
        $userContext->getUser()->willReturn($adminUser);

        $adminUser->getRoles()->willReturn(['ROLE_API_ACCESS']);

        $shipmentRepository->find('123')->willReturn($shipment);

        $this->getItem(ShipmentInterface::class, '123')->shouldReturn($shipment);
    }

    function it_returns_null_if_shop_user_has_no_customer(
        ShipmentRepositoryInterface $shipmentRepository,
        UserContextInterface $userContext,
        ShopUserInterface $shopUser,
    ) {
        $userContext->getUser()->willReturn($shopUser);

        $shopUser->getCustomer()->willReturn(null);
        $shopUser->getRoles()->willReturn(['ROLE_USER']);

        $shipmentRepository->findOneByCustomer('123', new Customer())->shouldNotBeCalled();

        $this->getItem(ShipmentInterface::class, '123')->shouldReturn(null);
    }

    function it_returns_null_if_shop_user_does_have_proper_roles(
        ShipmentRepositoryInterface $shipmentRepository,
        UserContextInterface $userContext,
        CustomerInterface $customer,
        ShopUserInterface $shopUser,
    ) {
        $userContext->getUser()->willReturn($shopUser);

        $shopUser->getCustomer()->willReturn($customer);
        $shopUser->getRoles()->willReturn(['']);

        $shipmentRepository->findOneByCustomer('123', new Customer())->shouldNotBeCalled();

        $this->getItem(ShipmentInterface::class, '123')->shouldReturn(null);
    }
}
