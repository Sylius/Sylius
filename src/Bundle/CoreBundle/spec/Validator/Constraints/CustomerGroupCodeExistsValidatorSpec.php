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

namespace spec\Sylius\Bundle\CoreBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Validator\Constraints\CustomerGroupCodeExists;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Component\Customer\Repository\CustomerGroupRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class CustomerGroupCodeExistsValidatorSpec extends ObjectBehavior
{
    const MESSAGE = 'sylius.customer_group.code.not_exist';

    function let(CustomerGroupRepositoryInterface $customerGroupRepository, ExecutionContextInterface $context): void
    {
        $this->beConstructedWith($customerGroupRepository);

        $this->initialize($context);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_customer_group_code_exists(
        Constraint $constraint,
    ): void {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('validate', ['customer_group_code', $constraint])
        ;
    }

    function it_does_nothing_if_value_is_empty(
        CustomerGroupRepositoryInterface $customerGroupRepository,
        ExecutionContextInterface $context,
    ): void {
        $this->validate('', new CustomerGroupCodeExists());

        $context->buildViolation(self::MESSAGE)->shouldNotHaveBeenCalled();
        $customerGroupRepository->findOneBy(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_does_nothing_if_customer_group_with_given_code_exists(
        CustomerGroupRepositoryInterface $customerGroupRepository,
        ExecutionContextInterface $context,
        CustomerGroupInterface $customerGroup,
    ): void {
        $customerGroupRepository->findOneBy(['code' => 'customer_group_code'])->willReturn($customerGroup);
        $this->validate('customer_group_code', new CustomerGroupCodeExists());

        $context->buildViolation(self::MESSAGE)->shouldNotHaveBeenCalled();
    }

    function it_adds_a_violation_if_customer_group_with_given_code_does_not_exist(
        CustomerGroupRepositoryInterface $customerGroupRepository,
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $customerGroupRepository->findOneBy(['code' => 'customer_group_code'])->willReturn(null);

        $constraintViolationBuilder->addViolation()->shouldBeCalled();
        $constraintViolationBuilder->atPath(Argument::any())->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->setParameter(Argument::cetera())->willReturn($constraintViolationBuilder);

        $context->buildViolation(self::MESSAGE)->shouldBeCalled()->willReturn($constraintViolationBuilder);

        $this->validate('customer_group_code', new CustomerGroupCodeExists());
    }
}
