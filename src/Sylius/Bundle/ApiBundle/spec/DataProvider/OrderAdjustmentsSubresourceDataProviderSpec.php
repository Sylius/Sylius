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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class OrderAdjustmentsSubresourceDataProviderSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository): void
    {
        $this->beConstructedWith($orderRepository);
    }

    function it_supports_only_order_adjustments_subresource_data_provider(): void
    {
        $this
            ->supports(ProductInterface::class, Request::METHOD_GET)
            ->shouldReturn(false)
        ;

        $context['subresource_identifiers'] = ['tokenValue' => 'TOKEN', 'items' => 11];
        $this
            ->supports(AdjustmentInterface::class, Request::METHOD_GET, $context)
            ->shouldReturn(false)
        ;

        $context['subresource_identifiers'] = ['tokenValue' => 'TOKEN'];
        $this
            ->supports(AdjustmentInterface::class, Request::METHOD_GET, $context)
            ->shouldReturn(true)
        ;
    }

    function it_throws_an_exception_if_order_with_given_token_does_not_exist(OrderRepositoryInterface $orderRepository): void
    {
        $context['subresource_identifiers'] = ['tokenValue' => 'TOKEN'];
        $orderRepository->findOneBy(['tokenValue' => 'TOKEN'])->willReturn(null);

        $this
            ->shouldThrow(NotFoundHttpException::class)
            ->during('getSubresource', [
                AdjustmentInterface::class,
                [],
                $context,
                Request::METHOD_GET,
            ])
        ;
    }

    function it_returns_order_adjustments(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        AdjustmentInterface $adjustment,
    ): void {
        $context['subresource_identifiers'] = ['tokenValue' => 'TOKEN'];
        $orderRepository->findOneBy(['tokenValue' => 'TOKEN'])->willReturn($order);

        $order->getAdjustmentsRecursively()->willReturn(new ArrayCollection([$adjustment->getWrappedObject()]));

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
