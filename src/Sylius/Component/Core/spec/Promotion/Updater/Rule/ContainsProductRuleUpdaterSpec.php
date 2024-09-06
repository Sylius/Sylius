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

namespace spec\Sylius\Component\Core\Promotion\Updater\Rule;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Promotion\Checker\Rule\ContainsProductRuleChecker;
use Sylius\Component\Core\Promotion\Updater\Rule\ProductAwareRuleUpdaterInterface;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

final class ContainsProductRuleUpdaterSpec extends ObjectBehavior
{
    function let(RepositoryInterface $repository): void
    {
        $this->beConstructedWith($repository);
    }

    function it_implements_product_aware_rule_updater_interface(): void
    {
        $this->shouldImplement(ProductAwareRuleUpdaterInterface::class);
    }

    function it_does_nothing_when_no_promotion_rules_of_contains_product_type_were_found(
        RepositoryInterface $repository,
        ProductInterface $product,
    ): void {
        $repository->findBy(['type' => ContainsProductRuleChecker::TYPE])->willReturn([]);
        $repository->remove(Argument::any())->shouldNotBeCalled();

        $this->updateAfterProductDeletion($product)->shouldReturn([]);
    }

    function it_does_nothing_when_product_was_not_used_in_any_contains_product_rules(
        RepositoryInterface $repository,
        ProductInterface $product,
        PromotionRuleInterface $promotionRule,
    ): void {
        $product->getCode()->willReturn('code');

        $repository->findBy(['type' => ContainsProductRuleChecker::TYPE])->willReturn([$promotionRule]);

        $promotionRule->getConfiguration()->willReturn([
            'product_code' => 'different_code',
        ]);

        $repository->remove(Argument::any())->shouldNotBeCalled();

        $this->updateAfterProductDeletion($product)->shouldReturn([]);
    }

    function it_removes_promotion_rule_and_returns_its_promotion_code_when_product_was_used_in_contains_product_rule(
        RepositoryInterface $repository,
        ProductInterface $product,
        PromotionRuleInterface $promotionRule,
        PromotionInterface $promotion,
    ): void {
        $product->getCode()->willReturn('code');

        $repository->findBy(['type' => ContainsProductRuleChecker::TYPE])->willReturn([$promotionRule]);

        $promotionRule->getConfiguration()->willReturn([
            'product_code' => 'code',
        ]);

        $promotionRule->getPromotion()->willReturn($promotion);
        $promotion->getCode()->willReturn('promotion_code');

        $repository->remove($promotionRule)->shouldBeCalled();

        $this->updateAfterProductDeletion($product)->shouldReturn(['promotion_code']);
    }
}
