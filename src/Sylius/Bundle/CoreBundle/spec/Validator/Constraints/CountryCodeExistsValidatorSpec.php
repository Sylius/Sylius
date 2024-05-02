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
use Sylius\Bundle\CoreBundle\Validator\Constraints\CountryCodeExists;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class CountryCodeExistsValidatorSpec extends ObjectBehavior
{
    const MESSAGE = 'sylius.country.code.not_exist';

    function let(RepositoryInterface $countryRepository, ExecutionContextInterface $context): void
    {
        $this->beConstructedWith($countryRepository);

        $this->initialize($context);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_country_code_exists(
        Constraint $constraint,
    ): void {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('validate', ['country_code', $constraint])
        ;
    }

    function it_does_nothing_if_value_is_empty(
        RepositoryInterface $countryRepository,
        ExecutionContextInterface $context,
    ): void {
        $this->validate('', new CountryCodeExists());

        $context->buildViolation(self::MESSAGE)->shouldNotHaveBeenCalled();
        $countryRepository->findOneBy(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_does_nothing_if_country_with_given_code_exists(
        RepositoryInterface $countryRepository,
        ExecutionContextInterface $context,
        CountryInterface $country,
    ): void {
        $countryRepository->findOneBy(['code' => 'country_code'])->willReturn($country);
        $this->validate('country_code', new CountryCodeExists());

        $context->buildViolation(self::MESSAGE)->shouldNotHaveBeenCalled();
    }

    function it_adds_a_violation_if_country_with_given_code_does_not_exist(
        RepositoryInterface $countryRepository,
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $countryRepository->findOneBy(['code' => 'country_code'])->willReturn(null);

        $constraintViolationBuilder->addViolation()->shouldBeCalled();
        $constraintViolationBuilder->atPath(Argument::any())->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->setParameter(Argument::cetera())->willReturn($constraintViolationBuilder);

        $context->buildViolation(self::MESSAGE)->shouldBeCalled()->willReturn($constraintViolationBuilder);

        $this->validate('country_code', new CountryCodeExists());
    }
}
