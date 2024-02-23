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

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\OrderItemRepositoryInterface;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Symfony\Component\HttpFoundation\Request;

final class AdminOrderItemAdjustmentsSubresourceDataProviderSpec extends ObjectBehavior
{
    function let(
        OrderItemRepositoryInterface $orderItemRepository,
        SectionProviderInterface $sectionProvider,
    ): void {
        $this->beConstructedWith($orderItemRepository, $sectionProvider);
    }

    function it_does_not_support_not_adjustment_resource(): void
    {
        $this
            ->supports(ProductInterface::class, Request::METHOD_GET, [])
            ->shouldReturn(false)
        ;
    }

    function it_does_not_support_in_shop_api_section(
        SectionProviderInterface $sectionProvider,
        ShopApiSection $shopApiSection,
    ): void {
        $sectionProvider->getSection()->willReturn($shopApiSection);

        $this
            ->supports(AdjustmentInterface::class, Request::METHOD_GET, [])
            ->shouldReturn(false)
        ;
    }

    function it_does_not_support_not_order_item_subresource(
        SectionProviderInterface $sectionProvider,
        AdminApiSection $adminApiSection,
    ): void {
        $sectionProvider->getSection()->willReturn($adminApiSection);

        $context['subresource_resources'] = [
            ProductInterface::class => ['id' => 2],
        ];

        $this
            ->supports(AdjustmentInterface::class, Request::METHOD_GET, $context)
            ->shouldReturn(false)
        ;
    }

    function it_supports_order_item_adjustments_subresource_in_admin_api_section(
        SectionProviderInterface $sectionProvider,
        AdminApiSection $adminApiSection,
    ): void {
        $sectionProvider->getSection()->willReturn($adminApiSection);
        $context['subresource_resources'] = [
            OrderItemInterface::class => ['id' => 2],
        ];

        $this
            ->supports(AdjustmentInterface::class, Request::METHOD_GET, $context)
            ->shouldReturn(true)
        ;
    }

    function it_providers_empty_array_if_order_item_does_not_exist(
        OrderItemRepositoryInterface $orderItemRepository,
    ): void {
        $context['subresource_identifiers'] = ['id' => '11'];
        $orderItemRepository->find('11')->willReturn(null);

        $this
            ->getSubresource(AdjustmentInterface::class, [], $context, Request::METHOD_GET)
            ->shouldReturn([]);
    }

    function it_returns_order_item_adjustments(
        OrderItemRepositoryInterface $orderItemRepository,
        OrderItemInterface $orderItem,
        AdjustmentInterface $adjustment1,
        AdjustmentInterface $adjustment2,
    ): void {
        $context['subresource_identifiers'] = ['id' => '11'];
        $orderItemRepository->find('11')->willReturn($orderItem);
        $orderItem->getAdjustmentsRecursively()->willReturn(
            new ArrayCollection([$adjustment1->getWrappedObject(), $adjustment2->getWrappedObject()]),
        );

        $this
            ->getSubresource(
                AdjustmentInterface::class,
                [],
                $context,
                Request::METHOD_GET,
            )
            ->shouldBeLike(new ArrayCollection([$adjustment1->getWrappedObject(), $adjustment2->getWrappedObject()]));
    }
}
