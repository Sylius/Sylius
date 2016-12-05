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

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Bundle\ResourceBundle\Validator\Constraints\WithinCollectionUniqueCode;
use Sylius\Bundle\ResourceBundle\Validator\WithinCollectionUniqueCodeValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class WithinCollectionUniqueCodeValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $context)
    {
        $this->initialize($context);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(WithinCollectionUniqueCodeValidator::class);
    }

    function it_is_constraint_validator()
    {
        $this->shouldHaveType(ConstraintValidatorInterface::class);
    }

    function it_adds_violation_if_resources_in_collection_has_the_same_code(
        CodeAwareInterface $firstEntity,
        CodeAwareInterface $secondEntity,
        CodeAwareInterface $thirdEntity,
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $violationBuilder
    ) {
        $firstEntity->getCode()->willReturn('122');
        $secondEntity->getCode()->willReturn('1234');
        $thirdEntity->getCode()->willReturn('122');
        $constraint = new WithinCollectionUniqueCode();

        $context->buildViolation(Argument::type('string'))->shouldBeCalled()->willReturn($violationBuilder);
        $violationBuilder->atPath('[0].code')->shouldBeCalled()->willReturn($violationBuilder);

        $context->buildViolation(Argument::type('string'))->shouldBeCalled()->willReturn($violationBuilder);
        $violationBuilder->atPath('[2].code')->shouldBeCalled()->willReturn($violationBuilder);

        $violationBuilder->addViolation()->shouldBeCalledTimes(2);
        
        $this->validate(
            [$firstEntity->getWrappedObject(), $secondEntity->getWrappedObject(), $thirdEntity->getWrappedObject()],
            $constraint
        );
    }

    function it_adds_violation_if_resources_in_collection_has_the_same_code_and_one_resource_not_have_code_at_all(
        CodeAwareInterface $firstEntity,
        CodeAwareInterface $secondEntity,
        CodeAwareInterface $thirdEntity,
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $violationBuilder
    ) {
        $firstEntity->getCode()->willReturn('122');
        $secondEntity->getCode()->willReturn(null);
        $thirdEntity->getCode()->willReturn('122');
        $constraint = new WithinCollectionUniqueCode();

        $context->buildViolation(Argument::type('string'))->shouldBeCalled()->willReturn($violationBuilder);
        $violationBuilder->atPath('[0].code')->shouldBeCalled()->willReturn($violationBuilder);

        $context->buildViolation(Argument::type('string'))->shouldBeCalled()->willReturn($violationBuilder);
        $violationBuilder->atPath('[2].code')->shouldBeCalled()->willReturn($violationBuilder);

        $violationBuilder->addViolation()->shouldBeCalledTimes(2);

        $this->validate(
            [$firstEntity->getWrappedObject(), $secondEntity->getWrappedObject(), $thirdEntity->getWrappedObject()],
            $constraint
        );
    }

    function it_does_not_add_violation_if_resources_in_collection_has_different_codes(
        CodeAwareInterface $firstEntity,
        CodeAwareInterface $secondEntity,
        Constraint $constraint,
        ConstraintViolationBuilderInterface $violationBuilder
    ) {
        $firstEntity->getCode()->willReturn('122');
        $secondEntity->getCode()->willReturn('1234');

        $violationBuilder->addViolation()->shouldNotBeCalled();

        $this->validate([ $firstEntity->getWrappedObject(), $secondEntity->getWrappedObject()], $constraint);
    }

    function it_does_not_add_violation_if_resources_in_collection_has_no_codes(
        CodeAwareInterface $firstEntity,
        CodeAwareInterface $secondEntity,
        Constraint $constraint,
        ConstraintViolationBuilderInterface $violationBuilder
    ) {
        $firstEntity->getCode()->willReturn(null);
        $secondEntity->getCode()->willReturn(null);

        $violationBuilder->addViolation()->shouldNotBeCalled();

        $this->validate([ $firstEntity->getWrappedObject(), $secondEntity->getWrappedObject()], $constraint);
    }

    function it_does_not_add_violation_if_one_resource_has_code_and_the_second_does_not_have(
        CodeAwareInterface $firstEntity,
        CodeAwareInterface $secondEntity,
        Constraint $constraint,
        ConstraintViolationBuilderInterface $violationBuilder
    ) {
        $firstEntity->getCode()->willReturn(null);
        $secondEntity->getCode()->willReturn('122');

        $violationBuilder->addViolation()->shouldNotBeCalled();

        $this->validate([ $firstEntity->getWrappedObject(), $secondEntity->getWrappedObject()], $constraint);
    }
}
