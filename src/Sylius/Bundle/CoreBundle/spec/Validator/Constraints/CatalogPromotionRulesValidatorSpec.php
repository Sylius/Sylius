<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Validator\Constraints\CatalogPromotionRules;
use Sylius\Component\Core\Model\CatalogPromotionRuleInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class CatalogPromotionRulesValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $executionContext, ProductVariantRepositoryInterface $variantRepository): void
    {
        $this->beConstructedWith($variantRepository);

        $this->initialize($executionContext);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    function it_adds_violation_if_catalog_promotion_has_invalid_type(
        ExecutionContextInterface $executionContext,
        CatalogPromotionRuleInterface $rule
    ): void {
        $rule->getType()->willReturn('wrong_type');

        $executionContext->addViolation('sylius.catalog_promotion.rules.invalid_type')->shouldBeCalled();

        $this->validate([$rule], new CatalogPromotionRules());
    }

    function it_adds_violation_if_catalog_promotion_rule_is_invalid(
        ExecutionContextInterface $executionContext,
        CatalogPromotionRuleInterface $rule
    ): void {
        $rule->getType()->willReturn(CatalogPromotionRuleInterface::TYPE_FOR_VARIANTS);
        $rule->getConfiguration()->willReturn(['wrong_config' => 'wrong_value']);

        $executionContext->addViolation('sylius.catalog_promotion.rules.invalid_configuration')->shouldBeCalled();

        $this->validate([$rule], new CatalogPromotionRules());
    }

    function it_adds_violation_if_catalog_promotion_rule_is_without_variants_key(
        ExecutionContextInterface $executionContext,
        CatalogPromotionRuleInterface $rule
    ): void {
        $rule->getType()->willReturn(CatalogPromotionRuleInterface::TYPE_FOR_VARIANTS);
        $rule->getConfiguration()->willReturn(['first_variant', 'second_variant']);

        $executionContext->addViolation('sylius.catalog_promotion.rules.invalid_configuration')->shouldBeCalled();

        $this->validate([$rule], new CatalogPromotionRules());
    }

    function it_does_nothing_if_catalog_promotion_rule_is_valid(
        ExecutionContextInterface $executionContext,
        CatalogPromotionRuleInterface $rule,
        ProductVariantRepositoryInterface $variantRepository,
        ProductVariantInterface $firstVariant,
        ProductVariantInterface $secondVariant
    ): void {
        $rule->getType()->willReturn(CatalogPromotionRuleInterface::TYPE_FOR_VARIANTS);
        $rule->getConfiguration()->willReturn(['variants' => ['first_variant', 'second_variant']]);

        $executionContext->addViolation('sylius.catalog_promotion.rules.invalid_type')->shouldNotBeCalled();

        $variantRepository->findOneBy(['code' => 'first_variant'])->willReturn($firstVariant);
        $executionContext->addViolation('sylius.catalog_promotion.rules.invalid_configuration')->shouldNotBeCalled();

        $variantRepository->findOneBy(['code' => 'second_variant'])->willReturn($secondVariant);
        $executionContext->addViolation('sylius.catalog_promotion.rules.invalid_configuration')->shouldNotBeCalled();

        $this->validate([$rule], new CatalogPromotionRules());
    }
}
