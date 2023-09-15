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

namespace spec\Sylius\Component\Core\Promotion\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Promotion\Checker\ProductInPromotionRuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ProductInPromotionRuleCheckerSpec extends ObjectBehavior
{
    function let(RepositoryInterface $promotionRuleRepository)
    {
        $this->beConstructedWith($promotionRuleRepository);
    }

    function it_implements_a_contains_product_promotion_rule_applied_checker_interface(): void
    {
        $this->shouldImplement(ProductInPromotionRuleCheckerInterface::class);
    }

    function it_checks_if_promotion_rule_is_applied_with_product(
        RepositoryInterface $promotionRuleRepository,
        PromotionRuleInterface $promotionRule,
        ProductInterface $product,
    ) {
        $promotionRuleRepository->findBy(['type' => 'contains_product'])->willReturn([$promotionRule]);

        $promotionRule->getConfiguration()->willReturn(['product_code' => 'sample_product_code']);

        $product->getCode()->willReturn('sample_product_code');

        $this->isInUse($product)->shouldReturn(true);
    }

    function it_returns_false_when_promotion_rule_is_not_applied_with_product(
        RepositoryInterface $promotionRuleRepository,
        PromotionRuleInterface $promotionRule,
        ProductInterface $product,
    ) {
        $promotionRuleRepository->findBy(['type' => 'contains_product'])->willReturn([$promotionRule]);

        $promotionRule->getConfiguration()->willReturn(['product_code' => 'sample_product_code']);

        $product->getCode()->willReturn('different_product_code');

        $this->isInUse($product)->shouldReturn(false);
    }

    function it_returns_false_when_no_promotion_rules_are_found(
        RepositoryInterface $promotionRuleRepository,
        ProductInterface $product,
    ) {
        $promotionRuleRepository->findBy(['type' => 'contains_product'])->willReturn([]);

        $this->isInUse($product)->shouldReturn(false);
    }
}
