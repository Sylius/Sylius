<?php

namespace spec\Sylius\Component\Core\Promotion\Checker;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductVariantInterface;

class NthProductRuleCheckerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Checker\NthProductRuleChecker');
    }

    function it_should_be_a_rule_checker()
    {
        $this->shouldHaveType('Sylius\Component\Promotion\Checker\RuleCheckerInterface');
    }

    function it_should_recognize_less_than_count_as_not_eligible(
        OrderInterface $subject,
        OrderItemInterface $item,
        ProductVariantInterface $variant,
        Product $product
    ) {
        $subject->getItems()->willReturn([$item]);
        $item->getQuantity()->willReturn(1);
        $item->getVariant()->willReturn($variant);
        $variant->getObject()->willReturn($product);
        $product->getId()->willReturn(2);

        $this->isEligible($subject, ['nth' => 2])->shouldReturn(false);
    }

    function it_should_recognize_more_than_count_as_eligible(
        OrderInterface $subject,
        OrderItemInterface $item,
        ProductVariantInterface $variant,
        Product $product
    ) {
        $subject->getItems()->willReturn([$item]);
        $item->getQuantity()->willReturn(3);
        $item->getVariant()->willReturn($variant);
        $variant->getObject()->willReturn($product);
        $product->getId()->willReturn(2);

        $this->isEligible($subject, ['nth' => 2])->shouldReturn(true);
    }

    function it_should_recognize_exact_count_as_eligible(
        OrderInterface $subject,
        OrderItemInterface $item,
        ProductVariantInterface $variant,
        Product $product
    ) {
        $subject->getItems()->willReturn([$item]);
        $item->getQuantity()->willReturn(2);
        $item->getVariant()->willReturn($variant);
        $variant->getObject()->willReturn($product);
        $product->getId()->willReturn(2);

        $this->isEligible($subject, ['nth' => 2])->shouldReturn(true);
    }

    function it_should_recognize_variations_of_same_product_as_eligible(
        OrderInterface $subject,
        OrderItemInterface $firstItemVariation,
        OrderItemInterface $secondItemVariation,
        ProductVariantInterface $firstVariant,
        ProductVariantInterface $secondVariant,
        Product $product
    ) {
        $subject->getItems()->willReturn([$firstItemVariation, $secondItemVariation]);

        $firstItemVariation->getQuantity()->willReturn(1);
        $firstItemVariation->getVariant()->willReturn($firstVariant);
        $secondItemVariation->getQuantity()->willReturn(1);
        $secondItemVariation->getVariant()->willReturn($secondVariant);

        $firstVariant->getObject()->willReturn($product);
        $secondVariant->getObject()->willReturn($product);
        $product->getId()->willReturn(2);


        $this->isEligible($subject, ['nth' => 2])->shouldReturn(true);
    }

    function it_should_recognize_two_different_products_as_not_eligible(
        OrderInterface $subject,
        OrderItemInterface $firstItemVariation,
        OrderItemInterface $secondItemVariation,
        ProductVariantInterface $firstVariant,
        ProductVariantInterface $secondVariant,
        Product $firstProduct,
        Product $secondProduct
    ) {
        $subject->getItems()->willReturn([$firstItemVariation, $secondItemVariation]);

        $firstItemVariation->getQuantity()->willReturn(1);
        $firstItemVariation->getVariant()->willReturn($firstVariant);
        $secondItemVariation->getQuantity()->willReturn(1);
        $secondItemVariation->getVariant()->willReturn($secondVariant);

        $firstVariant->getObject()->willReturn($firstProduct);
        $secondVariant->getObject()->willReturn($secondProduct);
        $firstProduct->getId()->willReturn(2);
        $secondProduct->getId()->willReturn(3);

        $this->isEligible($subject, ['nth' => 2])->shouldReturn(false);
    }
}
