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

namespace spec\Sylius\Bundle\ApiBundle\StateProvider\Shop\OrderItem;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Operation;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderItemRepositoryInterface;

final class ItemProviderSpec extends ObjectBehavior
{
    function let(
        SectionProviderInterface $sectionProvider,
        UserContextInterface $userContext,
        OrderItemRepositoryInterface $orderItemRepository,
    ): void {
        $this->beConstructedWith($sectionProvider, $userContext, $orderItemRepository);
    }

    function it_provides_order_item(
        SectionProviderInterface $sectionProvider,
        UserContextInterface $userContext,
        OrderItemRepositoryInterface $orderItemRepository,
        ShopUserInterface $user,
        CustomerInterface $customer,
        OrderItemInterface $item,
    ): void {
        $operation = new Get(class: OrderItem::class);
        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $userContext->getUser()->willReturn($user);
        $user->getCustomer()->willReturn($customer);

        $orderItemRepository->findOneByIdAndCustomer(1, $customer)->willReturn($item);

        $this->provide($operation, ['id' => 1])->shouldReturn($item);
    }

    function it_returns_null_if_there_is_no_logged_in_shop_user(
        SectionProviderInterface $sectionProvider,
        OrderItemRepositoryInterface $orderItemRepository,
        ShopUserInterface $user,
        CustomerInterface $customer,
    ): void {
        $operation = new Get(class: OrderItem::class);
        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $user->getCustomer()->willReturn($customer);

        $orderItemRepository->findOneByIdAndCustomer(1, $customer)->willReturn(null);

        $this->provide($operation, ['id' => 1])->shouldReturn(null);
    }

    function it_returns_null_if_there_is_no_order_item_unit_with_given_id_for_logged_in_shop_user(
        SectionProviderInterface $sectionProvider,
        UserContextInterface $userContext,
        OrderItemRepositoryInterface $orderItemRepository,
    ): void {
        $operation = new Get(class: OrderItem::class);
        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $userContext->getUser()->willReturn(null);

        $orderItemRepository->findOneByIdAndCustomer(1, Argument::any())->shouldNotBeCalled();

        $this->provide($operation, ['id' => 1])->shouldReturn(null);
    }

    function it_throws_an_exception_when_resource_is_not_an_order_item_interface(): void
    {
        $operation = new Get(class: \stdClass::class);

        $this->shouldThrow(\InvalidArgumentException::class)->during('provide', [$operation]);
    }

    function it_throws_an_exception_when_operation_is_not_get(Operation $operation): void
    {
        $operation->getClass()->willReturn(OrderItem::class);

        $this->shouldThrow(\InvalidArgumentException::class)->during('provide', [$operation]);
    }

    function it_throws_an_exception_when_operation_is_not_in_shop_api_section(
        SectionProviderInterface $sectionProvider,
    ): void {
        $operation = new Get(class: OrderItem::class);
        $sectionProvider->getSection()->willReturn(new AdminApiSection());

        $this->shouldThrow(\InvalidArgumentException::class)->during('provide', [$operation]);
    }
}
