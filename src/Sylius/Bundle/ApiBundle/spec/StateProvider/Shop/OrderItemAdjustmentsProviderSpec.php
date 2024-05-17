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

namespace spec\Sylius\Bundle\ApiBundle\StateProvider\Shop;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Core\Repository\OrderItemRepositoryInterface;

final class OrderItemAdjustmentsProviderSpec extends ObjectBehavior
{
    public function let(OrderItemRepositoryInterface $orderItemRepository): void
    {
        $this->beConstructedWith($orderItemRepository);
    }

    function it_is_a_state_provider(): void
    {
        $this->shouldImplement(ProviderInterface::class);
    }

    function it_returns_an_empty_array_when_uri_variables_have_no_id(
        OrderItemRepositoryInterface $orderItemRepository,
        Operation $operation,
    ): void {
        $orderItemRepository->findOneByIdAndOrderTokenValue(Argument::cetera())->shouldNotBeCalled();

        $this->provide($operation, ['tokenValue' => '42'])->shouldReturn([]);
    }

    function it_returns_an_empty_array_when_uri_variables_have_no_token_value(
        OrderItemRepositoryInterface $orderItemRepository,
        Operation $operation,
    ): void {
        $orderItemRepository->findOneByIdAndOrderTokenValue(Argument::cetera())->shouldNotBeCalled();

        $this->provide($operation, ['id' => 42])->shouldReturn([]);
    }

    function it_returns_an_empty_array_when_no_order_item_can_be_found(
        OrderItemRepositoryInterface $orderItemRepository,
        Operation $operation,
    ): void {
        $orderItemRepository->findOneByIdAndOrderTokenValue(42, 'token')->willReturn(null);

        $this->provide($operation, ['id' => '42', 'tokenValue' => 'token'])->shouldReturn([]);
    }


    function it_returns_adjustments_recursively(
        OrderItemRepositoryInterface $orderItemRepository,
        Operation $operation,
        OrderItem $orderItem,
        AdjustmentInterface $firstAdjustment,
        AdjustmentInterface $secondAdjustment,
    ): void {
        $adjustments = new ArrayCollection([
            $firstAdjustment->getWrappedObject(),
            $secondAdjustment->getWrappedObject(),
        ]);

        $orderItem->getAdjustmentsRecursively()->willReturn($adjustments);
        $orderItemRepository->findOneByIdAndOrderTokenValue(42, 'token')->willReturn($orderItem);

        $this->provide($operation, ['id' => '42', 'tokenValue' => 'token'])->shouldReturn($adjustments);
    }
}
