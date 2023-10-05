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

namespace spec\Sylius\Bundle\ApiBundle\Validator\CatalogPromotion;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionAction\ActionValidatorInterface;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionAction;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class FixedDiscountActionValidatorSpec extends ObjectBehavior
{
    function let(
        ActionValidatorInterface $baseActionValidator,
        SectionProviderInterface $sectionProvider,
    ): void {
        $this->beConstructedWith($baseActionValidator, $sectionProvider);
    }

    function it_is_an_action_validator(): void
    {
        $this->shouldHaveType(ActionValidatorInterface::class);
    }

    function it_just_fallbacks_to_base_validator_if_it_is_not_admin_api_section_it(
        ActionValidatorInterface $baseActionValidator,
        SectionProviderInterface $sectionProvider,
        ExecutionContextInterface $executionContext,
    ): void {
        $constraint = new CatalogPromotionAction();

        $sectionProvider->getSection()->willReturn(new ShopApiSection());

        $baseActionValidator->validate([], $constraint, $executionContext)->shouldBeCalled();

        $executionContext->buildViolation(Argument::any())->shouldNotBeCalled();

        $this->validate([], $constraint, $executionContext);
    }

    function it_adds_violation_if_catalog_promotion_action_has_an_empty_configuration(
        ActionValidatorInterface $baseActionValidator,
        SectionProviderInterface $sectionProvider,
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $sectionProvider->getSection()->willReturn(new AdminApiSection());

        $executionContext->buildViolation('sylius.catalog_promotion_action.fixed_discount.not_valid')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('configuration')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $baseActionValidator->validate(Argument::any())->shouldNotBeCalled();

        $this->validate([], new CatalogPromotionAction(), $executionContext);
    }

    function it_adds_violation_if_catalog_promotion_action_has_not_configured_amount(
        ActionValidatorInterface $baseActionValidator,
        SectionProviderInterface $sectionProvider,
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $sectionProvider->getSection()->willReturn(new AdminApiSection());

        $executionContext->buildViolation('sylius.catalog_promotion_action.fixed_discount.not_valid')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('configuration')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate(['channel' => []], new CatalogPromotionAction(), $executionContext);
    }

    function it_adds_violation_if_catalog_promotion_action_has_not_provided_amount(
        ActionValidatorInterface $baseActionValidator,
        SectionProviderInterface $sectionProvider,
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $sectionProvider->getSection()->willReturn(new AdminApiSection());

        $executionContext->buildViolation('sylius.catalog_promotion_action.fixed_discount.not_valid')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('configuration')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $baseActionValidator->validate(Argument::any())->shouldNotBeCalled();

        $this->validate(['channel' => ['amount' => null]], new CatalogPromotionAction(), $executionContext);
    }

    function it_adds_violation_if_catalog_promotion_action_has_invalid_amount_configured(
        ActionValidatorInterface $baseActionValidator,
        SectionProviderInterface $sectionProvider,
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $constraint = new CatalogPromotionAction();

        $sectionProvider->getSection()->willReturn(new AdminApiSection());

        $executionContext->buildViolation('sylius.catalog_promotion_action.fixed_discount.not_valid')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('configuration')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $baseActionValidator->validate(Argument::any())->shouldNotBeCalled();

        $this->validate(['channel' => ['amount' => 'wrong_value']], $constraint, $executionContext);
    }

    function it_adds_violation_if_catalog_promotion_action_has_invalid_amount_configured_for_one_of_channels(
        ActionValidatorInterface $baseActionValidator,
        SectionProviderInterface $sectionProvider,
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $constraint = new CatalogPromotionAction();

        $sectionProvider->getSection()->willReturn(new AdminApiSection());

        $executionContext->buildViolation('sylius.catalog_promotion_action.fixed_discount.not_valid')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('configuration')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalledOnce();

        $baseActionValidator->validate(Argument::any())->shouldNotBeCalled();

        $this->validate(
            ['channel' => ['amount' => 'wrong_value'], 'second_channel' => ['amount' => 'wrong_value']],
            $constraint,
            $executionContext,
        );
    }

    function it_does_nothing_if_the_provided_configuration_is_valid(
        ActionValidatorInterface $baseActionValidator,
        SectionProviderInterface $sectionProvider,
        ExecutionContextInterface $executionContext,
    ): void {
        $constraint = new CatalogPromotionAction();

        $sectionProvider->getSection()->willReturn(new AdminApiSection());

        $executionContext->buildViolation('sylius.catalog_promotion_action.fixed_discount.not_valid')->shouldNotBeCalled();

        $baseActionValidator
            ->validate(['channel' => ['amount' => 1000]], $constraint, $executionContext)
            ->shouldBeCalled()
        ;

        $this->validate(['channel' => ['amount' => 1000]], new CatalogPromotionAction(), $executionContext);
    }
}
