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

namespace spec\Sylius\Bundle\ApiBundle\StateProvider\Shop\Shipment;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;

final class ItemProviderSpec extends ObjectBehavior
{
    public function let(
        SectionProviderInterface $sectionProvider,
        UserContextInterface $userContext,
        ShipmentRepositoryInterface $shipmentRepository,
    ): void {
        $this->beConstructedWith($sectionProvider, $userContext, $shipmentRepository);
    }

    function it_is_a_state_provider(): void
    {
        $this->shouldImplement(ProviderInterface::class);
    }

    function it_throws_an_exception_if_operation_class_is_not_shipment(
        Operation $operation,
    ): void {
        $operation->getClass()->willReturn(\stdClass::class);
        $this->shouldThrow(\InvalidArgumentException::class)->during('provide', [$operation]);
    }

    function it_throws_an_exception_if_operation_is_not_get(
        Operation $operation,
    ): void {
        $operation->getClass()->willReturn(\stdClass::class);

        $this->shouldThrow(\InvalidArgumentException::class)->during('provide', [$operation]);
    }

    function it_throws_an_exception_if_section_is_not_shop_api_section(
        SectionProviderInterface $sectionProvider,
    ): void {
        $operation = new Get(class: ShipmentInterface::class, name: 'get');
        $sectionProvider->getSection()->willReturn(new AdminApiSection());

        $this->shouldThrow(\InvalidArgumentException::class)->during('provide', [$operation, [], []]);
    }

    function it_returns_nothing_if_user_is_not_shop_user(
        SectionProviderInterface $sectionProvider,
        UserContextInterface $userContext,
        ShipmentRepositoryInterface $shipmentRepository,
        AdminUserInterface $adminUser,
    ): void {
        $operation = new Get(class: ShipmentInterface::class, name: 'get');
        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $userContext->getUser()->willReturn($adminUser);
        $shipmentRepository->findOneByCustomerAndOrderToken(Argument::cetera())->shouldNotBeCalled();

        $this->provide($operation, [], [])->shouldReturn(null);
    }

    function it_returns_nothing_if_customer_is_null(
        SectionProviderInterface $sectionProvider,
        UserContextInterface $userContext,
        ShipmentRepositoryInterface $shipmentRepository,
        ShopUserInterface $shopUser,
    ): void {
        $operation = new Get(class: ShipmentInterface::class, name: 'get');
        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $userContext->getUser()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn(null);
        $shipmentRepository->findOneByCustomerAndOrderToken(Argument::cetera())->shouldNotBeCalled();

        $this->provide($operation, [], [])->shouldReturn(null);
    }

    function it_returns_shipment_by_customer_and_order_token(
        SectionProviderInterface $sectionProvider,
        UserContextInterface $userContext,
        ShipmentRepositoryInterface $shipmentRepository,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        ShipmentInterface $shipment,
    ): void {
        $operation = new Get(class: ShipmentInterface::class, name: 'get');
        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $userContext->getUser()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn($customer);
        $shipmentId = 1;
        $shipmentRepository->findOneByCustomerAndOrderToken($shipmentId, $customer, 'token')->willReturn($shipment);

        $this->provide($operation, ['shipmentId' => $shipmentId, 'tokenValue' => 'token'], [])->shouldReturn($shipment);
    }

    function it_returns_nothing_if_shipment_by_customer_and_order_token_is_not_found(
        SectionProviderInterface $sectionProvider,
        UserContextInterface $userContext,
        ShipmentRepositoryInterface $shipmentRepository,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
    ): void {
        $operation = new Get(class: ShipmentInterface::class, name: 'get');
        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $userContext->getUser()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn($customer);
        $shipmentId = 1;
        $shipmentRepository->findOneByCustomerAndOrderToken($shipmentId, $customer, 'token')->willReturn(null);

        $this->provide($operation, ['shipmentId' => $shipmentId, 'tokenValue' => 'token'], [])->shouldReturn(null);
    }
}
