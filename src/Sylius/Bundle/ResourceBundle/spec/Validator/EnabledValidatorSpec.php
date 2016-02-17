<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Validator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Validator\Constraints;
use Sylius\Component\Resource\Model\ToggleableInterface;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @mixin \Sylius\Bundle\ResourceBundle\Validator\EnabledValidator
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class EnabledValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $context)
    {
        $this->initialize($context);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Validator\EnabledValidator');
    }

    function it_is_constraint_validator()
    {
        $this->shouldHaveType(ConstraintValidatorInterface::class);
    }

    function it_does_not_apply_to_null_values(ExecutionContextInterface $context, Constraints\Enabled $constraint)
    {
        $context->addViolation(Argument::cetera())->shouldNotBeCalled();

        $this->validate(null, $constraint);
    }

    function it_throws_an_exception_if_subject_does_not_implement_toggleable_interface(
        ExecutionContextInterface $context,
        Constraints\Enabled $constraint,
        \stdClass $subject
    ) {
        $context->addViolation(Argument::cetera())->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->duringValidate($subject, $constraint);
    }

    function it_adds_violation_if_subject_is_disabled(
        ExecutionContextInterface $context,
        Constraints\Enabled $constraint,
        ToggleableInterface $subject
    ) {
        $constraint->message = 'Violation message';

        $subject->isEnabled()->shouldBeCalled()->willReturn(false);

        $context->addViolation($constraint->message, Argument::cetera())->shouldBeCalled();

        $this->validate($subject, $constraint);
    }

    function it_does_not_add_violation_if_subject_is_enabled(
        ExecutionContextInterface $context,
        Constraints\Enabled $constraint,
        ToggleableInterface $subject
    ) {
        $subject->isEnabled()->shouldBeCalled()->willReturn(true);

        $context->addViolation(Argument::cetera())->shouldNotBeCalled();

        $this->validate($subject, $constraint);
    }
}
