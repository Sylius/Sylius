<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Promotion\Action;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Promotion\Action\AddProductPromotionActionCommand;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Promotion\Action\PromotionActionCommandInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @mixin AddProductPromotionActionCommand
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class AddProductPromotionActionCommandSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $itemFactory,
        RepositoryInterface $variantRepository,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier
    ) {
        $this->beConstructedWith($itemFactory, $variantRepository, $orderItemQuantityModifier);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AddProductPromotionActionCommand::class);
    }

    function it_implements_a_promotion_action_interface()
    {
        $this->shouldImplement(PromotionActionCommandInterface::class);
    }

    function it_adds_product_as_promotion(
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        RepositoryInterface $variantRepository,
        FactoryInterface $itemFactory,
        OrderInterface $order,
        OrderItemInterface $item,
        ProductVariantInterface $variant,
        PromotionInterface $promotion
    ) {
        $variantRepository->find(500)->willReturn($variant);

        $itemFactory->createNew()->willReturn($item);
        $item->setUnitPrice(0)->willReturn($item);
        $item->setVariant($variant)->willReturn($item);
        $orderItemQuantityModifier->modify($item, 2)->shouldBeCalled();

        $item->setImmutable(true)->shouldBeCalled();

        $order->getItems()->willReturn([]);

        $order->addItem($item)->shouldBeCalled();

        $this->execute($order, ['variant' => 500, 'quantity' => 2, 'price' => 0], $promotion);
    }

    function it_does_not_add_product_if_exists(
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        RepositoryInterface $variantRepository,
        FactoryInterface $itemFactory,
        OrderInterface $order,
        OrderItemInterface $item,
        ProductVariantInterface $variant,
        PromotionInterface $promotion
    ) {
        $variantRepository->find(500)->willReturn($variant);

        $itemFactory->createNew()->willReturn($item);
        $item->setUnitPrice(1)->willReturn($item);
        $item->setVariant($variant)->willReturn($item);
        $orderItemQuantityModifier->modify($item, 2)->shouldBeCalled();
        $item->equals($item)->willReturn(true);

        $order->getItems()->willReturn([$item]);

        $order->addItem($item)->shouldNotBeCalled();

        $this->execute($order, ['variant' => 500, 'quantity' => 2, 'price' => 1], $promotion);
    }

    function it_reverts_a_product(
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        RepositoryInterface $variantRepository,
        FactoryInterface $itemFactory,
        OrderInterface $order,
        OrderItemInterface $item,
        ProductVariantInterface $variant,
        PromotionInterface $promotion
    ) {
        $variantRepository->find(500)->willReturn($variant);

        $itemFactory->createNew()->willReturn($item);
        $item->setUnitPrice(2)->willReturn($item);
        $item->setVariant($variant)->willReturn($item);
        $orderItemQuantityModifier->modify($item, 3)->shouldBeCalled();
        $item->equals($item)->willReturn(true);
        $item->setImmutable(true)->shouldBeCalled();

        $order->getItems()->willReturn([$item]);

        $order->removeItem($item)->shouldBeCalled();

        $this->revert($order, ['variant' => 500, 'quantity' => 3, 'price' => 2], $promotion);
    }
}
