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

namespace spec\Sylius\Bundle\PromotionBundle\Validator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionActionType;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class CatalogPromotionActionTypeValidatorSpec extends ObjectBehavior
{
    private const ACTION_TYPES = [
        'test',
        'another_test',
    ];

    function let(ExecutionContextInterface $context): void
    {
        $this->beConstructedWith(self::ACTION_TYPES);
        $this->initialize($context);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    function it_throws_exception_when_constraint_is_not_catalog_promotion_action_type(
        Constraint $constraint,
        CatalogPromotionActionInterface $action,
    ): void {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('validate', [$action, $constraint])
        ;
    }

    function it_throws_exception_when_value_is_not_catalog_promotion_action(): void
    {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('validate', [new \stdClass(), new CatalogPromotionActionType()])
        ;
    }

    function it_does_nothing_when_passed_action_has_null_as_type(
        ExecutionContextInterface $context,
        CatalogPromotionActionInterface $action,
    ): void {
        $action->getType()->willReturn(null);

        $context->buildViolation(Argument::any())->shouldNotBeCalled();

        $this->validate($action, new CatalogPromotionActionType());
    }

    function it_does_nothing_when_passed_action_has_an_empty_string_as_type(
        ExecutionContextInterface $context,
        CatalogPromotionActionInterface $action,
    ): void {
        $action->getType()->willReturn('');

        $context->buildViolation(Argument::any())->shouldNotBeCalled();

        $this->validate($action, new CatalogPromotionActionType());
    }

    function it_does_nothing_when_catalog_promotion_action_has_valid_type(
        ExecutionContextInterface $context,
        CatalogPromotionActionInterface $action,
    ): void {
        $action->getType()->willReturn('test');

        $context->buildViolation(Argument::any())->shouldNotBeCalled();

        $this->validate($action, new CatalogPromotionActionType());
    }

    function it_adds_violation_when_type_is_unknown(
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $violationBuilder,
        CatalogPromotionActionInterface $action,
    ): void {
        $constraint = new CatalogPromotionActionType();
        $action->getType()->willReturn('not_existing_type');

        $context->buildViolation($constraint->invalidType)->willReturn($violationBuilder);
        $violationBuilder
            ->setParameter('{{ available_action_types }}', implode(', ', self::ACTION_TYPES))
            ->willReturn($violationBuilder)
        ;
        $violationBuilder->atPath('type')->willReturn($violationBuilder);
        $violationBuilder->addViolation()->shouldBeCalled();

        $this->validate($action, $constraint);
    }
}
