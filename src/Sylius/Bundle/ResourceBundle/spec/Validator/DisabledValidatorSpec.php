<?php

namespace spec\Sylius\Bundle\ResourceBundle\Validator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Validator\Constraints;
use Sylius\Component\Resource\Model\ToggleableInterface;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @mixin \Sylius\Bundle\ResourceBundle\Validator\DisabledValidator
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class DisabledValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $context)
    {
        $this->initialize($context);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Validator\DisabledValidator');
    }

    function it_is_constraint_validator()
    {
        $this->shouldHaveType(ConstraintValidatorInterface::class);
    }

    function it_does_not_apply_to_null_values(ExecutionContextInterface $context, Constraints\Disabled $constraint)
    {
        $context->addViolation(Argument::cetera())->shouldNotBeCalled();

        $this->validate(null, $constraint);
    }

    function it_throws_an_exception_if_subject_does_not_implement_toggleable_interface(
        ExecutionContextInterface $context,
        Constraints\Disabled $constraint,
        \stdClass $subject
    ) {
        $context->addViolation(Argument::cetera())->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->duringValidate($subject, $constraint);
    }

    function it_adds_violation_if_subject_is_enabled(
        ExecutionContextInterface $context,
        Constraints\Disabled $constraint,
        ToggleableInterface $subject
    ) {
        $constraint->message = "foobar";

        $subject->isEnabled()->shouldBeCalled()->willReturn(true);

        $context->addViolation(Argument::cetera())->shouldBeCalled();

        $this->validate($subject, $constraint);
    }

    function it_does_not_add_violation_if_subject_is_disabled(
        ExecutionContextInterface $context,
        Constraints\Disabled $constraint,
        ToggleableInterface $subject
    ) {
        $subject->isEnabled()->shouldBeCalled()->willReturn(false);

        $context->addViolation(Argument::cetera())->shouldNotBeCalled();

        $this->validate($subject, $constraint);
    }
}
