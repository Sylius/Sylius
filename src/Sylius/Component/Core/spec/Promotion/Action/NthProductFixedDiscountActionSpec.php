<?php

namespace spec\Sylius\Component\Core\Promotion\Action;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Originator\Originator\OriginatorInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class NthProductFixedDiscountActionSpec extends ObjectBehavior
{
    function let(RepositoryInterface $adjustmentRepository, OriginatorInterface $originator)
    {
        $this->beConstructedWith($adjustmentRepository, $originator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Action\NthProductFixedDiscountAction');
    }

    function it_implements_Sylius_promotion_action_interface()
    {
        $this->shouldImplement('Sylius\Component\Promotion\Action\PromotionActionInterface');
    }

    function it_applies_fixed_discount_as_promotion_adjustment(
        $adjustmentRepository,
        $originator,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        AdjustmentInterface $adjustment,
        PromotionInterface $promotion,
        Product $product
    ) {

        $order->getItems()->willReturn([$orderItem]);
        $orderItem->getQuantity()->willReturn(2);
        $orderItem->getProduct()->willReturn($product);
        $product->getId()->willReturn(2);

        $adjustmentRepository->createNew()->willReturn($adjustment);
        $promotion->getDescription()->willReturn('promotion description');

        $adjustment->setAmount(-500)->shouldBeCalled();
        $adjustment->setLabel(AdjustmentInterface::PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $adjustment->setDescription('promotion description')->shouldBeCalled();

        $originator->setOrigin($adjustment, $promotion)->shouldBeCalled();

        $order->addAdjustment($adjustment)->shouldBeCalled();

        $configuration = array('amount' => 500, 'nth' => 2);

        $this->execute($order, $configuration, $promotion);
    }

    function it_doesnt_apply_fixed_discount_if_quantity_is_insufficiant(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        PromotionInterface $promotion,
        Product $product
    ) {
        $order->getItems()->willReturn([$orderItem]);
        $orderItem->getQuantity()->willReturn(1);
        $orderItem->getProduct()->willReturn($product);
        $product->getId()->willReturn(2);

        $order->addAdjustment(Argument::any())->shouldNotBeCalled();

        $configuration = array('amount' => 500, 'nth' => 2);

        $this->execute($order, $configuration, $promotion);
    }

    function it_applies_fixed_discount_on_product_variations(
        $adjustmentRepository,
        $originator,
        OrderInterface $order,
        OrderItemInterface $firstItem,
        OrderItemInterface $secondItem,
        AdjustmentInterface $adjustment,
        PromotionInterface $promotion,
        Product $product
    ) {
        $order->getItems()->willReturn([$firstItem, $secondItem]);
        $firstItem->getQuantity()->willReturn(1);
        $firstItem->getProduct()->willReturn($product);
        $secondItem->getQuantity()->willReturn(1);
        $secondItem->getProduct()->willReturn($product);
        $product->getId()->willReturn(2);

        $adjustmentRepository->createNew()->willReturn($adjustment);
        $promotion->getDescription()->willReturn('promotion description');

        $adjustment->setAmount(-500)->shouldBeCalled();
        $adjustment->setLabel(AdjustmentInterface::PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $adjustment->setDescription('promotion description')->shouldBeCalled();

        $originator->setOrigin($adjustment, $promotion)->shouldBeCalled();

        $order->addAdjustment($adjustment)->shouldBeCalled();

        $configuration = array('amount' => 500, 'nth' => 2);

        $this->execute($order, $configuration, $promotion);
    }

    function it_doesnt_apply_fixed_discount_on_different_products(
        OrderInterface $order,
        OrderItemInterface $firstItem,
        OrderItemInterface $secondItem,
        PromotionInterface $promotion,
        Product $firstProduct,
        Product $secondProduct
    ) {
        $order->getItems()->willReturn([$firstItem, $secondItem]);
        $firstItem->getQuantity()->willReturn(1);
        $firstItem->getProduct()->willReturn($firstProduct);
        $secondItem->getQuantity()->willReturn(1);
        $secondItem->getProduct()->willReturn($secondProduct);
        $firstProduct->getId()->willReturn(2);
        $secondProduct->getId()->willReturn(3);

        $order->addAdjustment(Argument::any())->shouldNotBeCalled();

        $configuration = array('amount' => 500, 'nth' => 2);

        $this->execute($order, $configuration, $promotion);
    }

    function it_should_apply_fixed_discount_multiple_times_on_multiple_quantities(
        $adjustmentRepository,
        $originator,
        OrderInterface $order,
        OrderItemInterface $firstItem,
        OrderItemInterface $secondItem,
        AdjustmentInterface $adjustment,
        PromotionInterface $promotion,
        Product $product
    ) {
        $order->getItems()->willReturn([$firstItem, $secondItem]);
        $firstItem->getQuantity()->willReturn(3);
        $firstItem->getProduct()->willReturn($product);
        $secondItem->getQuantity()->willReturn(2);
        $secondItem->getProduct()->willReturn($product);
        $product->getId()->willReturn(2);

        $adjustmentRepository->createNew()->willReturn($adjustment);
        $promotion->getDescription()->willReturn('promotion description');

        $adjustment->setAmount(-1000)->shouldBeCalled();
        $adjustment->setLabel(AdjustmentInterface::PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $adjustment->setDescription('promotion description')->shouldBeCalled();

        $originator->setOrigin($adjustment, $promotion)->shouldBeCalled();

        $order->addAdjustment($adjustment)->shouldBeCalled();

        $configuration = array('amount' => 500, 'nth' => 2);

        $this->execute($order, $configuration, $promotion);
    }
}
