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
use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionActionGroup;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Validator\ContextualValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CatalogPromotionActionGroupValidatorSpec extends ObjectBehavior
{
    private const VALIDATION_GROUPS = [
        'test' => [
            'test_group',
        ],
        'another_test' => [
            'another_test_group',
        ],
    ];

    function let(ExecutionContextInterface $context): void
    {
        $this->beConstructedWith(self::VALIDATION_GROUPS);
        $this->initialize($context);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    function it_throws_exception_when_constraint_is_not_catalog_promotion_action_group(
        CatalogPromotionActionInterface $action,
        Constraint $constraint,
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
            ->during('validate', [new \stdClass(), new CatalogPromotionActionGroup()])
        ;
    }

    function it_does_nothing_when_type_is_null(
        ExecutionContextInterface $context,
        CatalogPromotionActionInterface $action,
    ): void {
        $action->getType()->willReturn(null);

        $context->getValidator()->shouldNotBeCalled();

        $this->validate($action, new CatalogPromotionActionGroup());
    }

    function it_does_nothing_when_type_is_an_empty_string(
        ExecutionContextInterface $context,
        CatalogPromotionActionInterface $action,
    ): void {
        $action->getType()->willReturn('');

        $context->getValidator()->shouldNotBeCalled();

        $this->validate($action, new CatalogPromotionActionGroup());
    }

    function it_passes_configured_validation_groups_for_further_validation(
        ExecutionContextInterface $context,
        ValidatorInterface $validator,
        ContextualValidatorInterface $contextualValidator,
        ConstraintViolationListInterface $violationList,
        CatalogPromotionActionInterface $action,
    ): void {
        $constraint = new CatalogPromotionActionGroup();

        $action->getType()->willReturn('test');

        $context->getValidator()->willReturn($validator);
        $validator->inContext($context)->willReturn($contextualValidator);

        $contextualValidator
            ->validate($action, null, ['test_group'])
            ->shouldBeCalled()
            ->willReturn($contextualValidator)
        ;

        $context->getViolations()->willReturn($violationList);
        $violationList->count()->willReturn(1);

        $this->validate($action, $constraint);
    }
}
