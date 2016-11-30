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
use Sylius\Bundle\ResourceBundle\Validator\Constraints\Enabled;
use Sylius\Bundle\ResourceBundle\Validator\EnabledValidator;
use Sylius\Component\Resource\Model\ToggleableInterface;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class EnabledValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $context)
    {
        $this->initialize($context);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(EnabledValidator::class);
    }

    function it_is_constraint_validator()
    {
        $this->shouldHaveType(ConstraintValidatorInterface::class);
    }

    function it_does_not_apply_to_null_values(ExecutionContextInterface $context)
    {
        $context->addViolation(Argument::cetera())->shouldNotBeCalled();

        $this->validate(null, new Enabled());
    }

    function it_throws_an_exception_if_subject_does_not_implement_toggleable_interface(ExecutionContextInterface $context)
    {
        $context->addViolation(Argument::cetera())->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->duringValidate(new \stdClass(), new Enabled());
    }

    function it_adds_violation_if_subject_is_disabled(
        ExecutionContextInterface $context,
        ToggleableInterface $subject
    ) {
        $subject->isEnabled()->shouldBeCalled()->willReturn(false);

        $context->addViolation(Argument::cetera())->shouldBeCalled();

        $this->validate($subject, new Enabled());
    }

    function it_does_not_add_violation_if_subject_is_enabled(
        ExecutionContextInterface $context,
        ToggleableInterface $subject
    ) {
        $subject->isEnabled()->shouldBeCalled()->willReturn(true);

        $context->addViolation(Argument::cetera())->shouldNotBeCalled();

        $this->validate($subject, new Enabled());
    }
}
