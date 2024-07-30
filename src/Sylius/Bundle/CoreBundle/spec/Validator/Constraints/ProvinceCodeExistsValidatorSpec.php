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
use Sylius\Bundle\CoreBundle\Validator\Constraints\ProvinceCodeExists;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ProvinceCodeExistsValidatorSpec extends ObjectBehavior
{
    const MESSAGE = 'sylius.province.code.not_exist';

    function let(RepositoryInterface $provinceRepository, ExecutionContextInterface $context): void
    {
        $this->beConstructedWith($provinceRepository);

        $this->initialize($context);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_province_code_exists(
        Constraint $constraint,
    ): void {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('validate', ['province_code', $constraint])
        ;
    }

    function it_does_nothing_if_value_is_empty(
        RepositoryInterface $provinceRepository,
        ExecutionContextInterface $context,
    ): void {
        $this->validate('', new ProvinceCodeExists());

        $context->buildViolation(self::MESSAGE)->shouldNotHaveBeenCalled();
        $provinceRepository->findOneBy(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_does_nothing_if_province_with_given_code_exists(
        RepositoryInterface $provinceRepository,
        ExecutionContextInterface $context,
        ProvinceInterface $province,
    ): void {
        $provinceRepository->findOneBy(['code' => 'province_code'])->willReturn($province);
        $this->validate('province_code', new ProvinceCodeExists());

        $context->buildViolation(self::MESSAGE)->shouldNotHaveBeenCalled();
    }

    function it_adds_a_violation_if_province_with_given_code_does_not_exist(
        RepositoryInterface $provinceRepository,
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $provinceRepository->findOneBy(['code' => 'province_code'])->willReturn(null);

        $constraintViolationBuilder->addViolation()->shouldBeCalled();
        $constraintViolationBuilder->atPath(Argument::any())->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->setParameter(Argument::cetera())->willReturn($constraintViolationBuilder);

        $context->buildViolation(self::MESSAGE)->shouldBeCalled()->willReturn($constraintViolationBuilder);

        $this->validate('province_code', new ProvinceCodeExists());
    }
}
