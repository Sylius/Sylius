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
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\OrderItemRepositoryInterface;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Symfony\Component\HttpFoundation\Request;

final class OrderItemAdjustmentsSubresourceDataProviderSpec extends ObjectBehavior
{
    function let(OrderItemRepositoryInterface $orderItemRepository): void
    {
        $this->beConstructedWith($orderItemRepository);
    }

    function it_supports_only_order_item_adjustments_subresource_data_provider(): void
    {
        $this
            ->supports(ProductInterface::class, Request::METHOD_GET)
            ->shouldReturn(false)
        ;

        $context['subresource_identifiers'] = ['tokenValue' => 'TOKEN', 'items' => 11];
        $this
            ->supports(AdjustmentInterface::class, Request::METHOD_GET, $context)
            ->shouldReturn(true)
        ;
    }

    function it_providers_empty_array_if_order_item_does_not_exist(
        OrderItemRepositoryInterface $orderItemRepository,
    ): void {
        $context['subresource_identifiers'] = ['tokenValue' => 'TOKEN', 'items' => '11'];
        $orderItemRepository->findOneByIdAndOrderTokenValue(11, 'TOKEN')->willReturn(null);

        $this
            ->getSubresource(AdjustmentInterface::class, [], $context, Request::METHOD_GET)
            ->shouldReturn([]);
    }

    function it_returns_order_adjustments(
        OrderItemRepositoryInterface $orderItemRepository,
        OrderItemInterface $orderItem,
        AdjustmentInterface $adjustment,
    ): void {
        $context['subresource_identifiers'] = ['tokenValue' => 'TOKEN', 'items' => '11'];
        $orderItemRepository->findOneByIdAndOrderTokenValue(11, 'TOKEN')->willReturn($orderItem);

        $orderItem->getAdjustmentsRecursively()->willReturn(new ArrayCollection([$adjustment->getWrappedObject()]));

        $this
            ->getSubresource(
                AdjustmentInterface::class,
                [],
                $context,
                Request::METHOD_GET,
            )
            ->shouldBeLike(new ArrayCollection([$adjustment->getWrappedObject()]))
        ;
    }
}
