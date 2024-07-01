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

namespace spec\Sylius\Bundle\ApiBundle\StateProvider\Shop\OrderItemUnit;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Operation;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderItemUnit;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderItemUnitRepositoryInterface;

final class ItemProviderSpec extends ObjectBehavior
{
    function let(
        SectionProviderInterface $sectionProvider,
        UserContextInterface $userContext,
        OrderItemUnitRepositoryInterface $orderItemUnitRepository,
    ): void {
        $this->beConstructedWith($sectionProvider, $userContext, $orderItemUnitRepository);
    }

    function it_provides_order_item_unit(
        SectionProviderInterface $sectionProvider,
        UserContextInterface $userContext,
        OrderItemUnitRepositoryInterface $orderItemUnitRepository,
        ShopUserInterface $user,
        CustomerInterface $customer,
        OrderItemUnitInterface $unit,
    ): void {
        $operation = new Get(class: OrderItemUnit::class);
        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $userContext->getUser()->willReturn($user);
        $user->getCustomer()->willReturn($customer);

        $orderItemUnitRepository->findOneByCustomer(1, $customer)->willReturn($unit);

        $this->provide($operation, ['id' => 1])->shouldReturn($unit);
    }

    function it_returns_null_if_there_is_no_logged_in_shop_user(
        SectionProviderInterface $sectionProvider,
        OrderItemUnitRepositoryInterface $orderItemUnitRepository,
        ShopUserInterface $user,
        CustomerInterface $customer,
    ): void {
        $operation = new Get(class: OrderItemUnit::class);
        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $user->getCustomer()->willReturn($customer);

        $orderItemUnitRepository->findOneByCustomer(1, $customer)->willReturn(null);

        $this->provide($operation, ['id' => 1])->shouldReturn(null);
    }

    function it_returns_null_if_there_is_no_order_item_unit_with_given_id_for_logged_in_shop_user(
        SectionProviderInterface $sectionProvider,
        UserContextInterface $userContext,
        OrderItemUnitRepositoryInterface $orderItemUnitRepository,
    ): void {
        $operation = new Get(class: OrderItemUnit::class);
        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $userContext->getUser()->willReturn(null);

        $orderItemUnitRepository->findOneByCustomer(1, Argument::any())->shouldNotBeCalled();

        $this->provide($operation, ['id' => 1])->shouldReturn(null);
    }

    function it_throws_an_exception_when_resource_is_not_an_order_item_unit_interface(): void
    {
        $operation = new Get(class: \stdClass::class);

        $this->shouldThrow(\InvalidArgumentException::class)->during('provide', [$operation]);
    }

    function it_throws_an_exception_when_operation_is_not_get(Operation $operation): void
    {
        $operation->getClass()->willReturn(OrderItemUnit::class);

        $this->shouldThrow(\InvalidArgumentException::class)->during('provide', [$operation]);
    }

    function it_throws_an_exception_when_operation_is_not_in_shop_api_section(
        SectionProviderInterface $sectionProvider,
    ): void {
        $operation = new Get(class: OrderItemUnit::class);
        $sectionProvider->getSection()->willReturn(new AdminApiSection());

        $this->shouldThrow(\InvalidArgumentException::class)->during('provide', [$operation]);
    }
}
