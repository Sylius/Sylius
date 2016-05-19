<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PromotionBundle\Validator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\CouponPossibleGenerationAmount;
use Sylius\Bundle\PromotionBundle\Validator\CouponGenerationAmountValidator;
use Sylius\Component\Promotion\Generator\GenerationPolicyInterface;
use Sylius\Component\Promotion\Generator\InstructionInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @mixin CouponGenerationAmountValidator
 *
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class CouponGenerationAmountValidatorSpec extends ObjectBehavior
{
    function let(GenerationPolicyInterface $generationPolicy, ExecutionContextInterface $context)
    {
        $this->beConstructedWith($generationPolicy);
        $this->initialize($context);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionBundle\Validator\CouponGenerationAmountValidator');
    }

    function it_is_constraint_validator()
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    function it_adds_violation(
        ExecutionContextInterface $context,
        CouponPossibleGenerationAmount $constraint,
        InstructionInterface $instruction,
        GenerationPolicyInterface $generationPolicy
    ) {
        $instruction->getAmount()->willReturn(17);
        $instruction->getCodeLength()->willReturn(1);
        $generationPolicy->isGenerationPossible($instruction)->willReturn(false);
        $generationPolicy->getPossibleGenerationAmount($instruction)->shouldBeCalled();
        $context->addViolation($constraint->message, Argument::any())->shouldBeCalled();

        $this->validate($instruction, $constraint);
    }

    function it_not_adds_violation(
        ExecutionContextInterface $context,
        CouponPossibleGenerationAmount $constraint,
        InstructionInterface $instruction,
        GenerationPolicyInterface $generationPolicy
    ) {
        $instruction->getAmount()->willReturn(5);
        $instruction->getCodeLength()->willReturn(1);
        $generationPolicy->isGenerationPossible($instruction)->willReturn(true);
        $generationPolicy->getPossibleGenerationAmount($instruction)->shouldNotBeCalled();
        $context->addViolation($constraint->message)->shouldNotBeCalled();

        $this->validate($instruction, $constraint);
    }
}
