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

namespace spec\Sylius\Bundle\AddressingBundle\Validator\Constraints;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\AddressingBundle\Validator\Constraints\UniqueProvinceCollection;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class UniqueProvinceCollectionValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $executionContext): void
    {
        $this->beConstructedWith();
        $this->initialize($executionContext);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_exception_when_value_is_not_a_collection(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [
                new \stdClass(),
                new UniqueProvinceCollection(),
            ])
        ;
    }

    function it_throws_exception_when_collection_contains_something_other_than_provinces(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [
                new ArrayCollection([new \stdClass()]),
                new UniqueProvinceCollection(),
            ])
        ;
    }

    function it_throws_exception_when_constraint_is_not_a_unique_province_collection_codes(
        Constraint $constraint,
    ): void {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [
                new ArrayCollection(),
                $constraint,
            ])
        ;
    }

    function it_does_nothing_when_collection_is_empty(ExecutionContextInterface $context): void
    {
        $context->addViolation(Argument::any())->shouldNotBeCalled();

        $this->validate(new ArrayCollection(), new UniqueProvinceCollection());
    }

    function it_does_nothing_when_all_provinces_have_unique_codes(
        ExecutionContextInterface $executionContext,
        ProvinceInterface $firstProvince,
        ProvinceInterface $secondProvince,
    ): void {
        $firstProvince->getCode()->willReturn('first');
        $firstProvince->getName()->willReturn('first');
        $secondProvince->getCode()->willReturn('second');
        $secondProvince->getName()->willReturn('second');

        $executionContext->addViolation(Argument::any())->shouldNotBeCalled();

        $this->validate(
            new ArrayCollection([
                $firstProvince->getWrappedObject(),
                $secondProvince->getWrappedObject(),
            ]),
            new UniqueProvinceCollection(),
        );
    }

    function it_checks_uniqueness_with_incomplete_codes(
        ExecutionContextInterface $executionContext,
        ProvinceInterface $province,
        ProvinceInterface $sameProvinceWithCode,
    ): void {
        $constraint = new UniqueProvinceCollection();

        $province->getCode()->willReturn(null);
        $province->getName()->willReturn('name');
        $sameProvinceWithCode->getCode()->willReturn('code');
        $sameProvinceWithCode->getName()->willReturn('name');

        $executionContext->addViolation($constraint->message)->shouldBeCalled();

        $this->validate(
            new ArrayCollection([
                $province->getWrappedObject(),
                $sameProvinceWithCode->getWrappedObject(),
            ]),
            $constraint,
        );
    }

    function it_checks_uniqueness_with_incomplete_names(
        ExecutionContextInterface $executionContext,
        ProvinceInterface $province,
        ProvinceInterface $sameProvinceWithName,
    ): void {
        $constraint = new UniqueProvinceCollection();

        $province->getCode()->willReturn('code');
        $province->getName()->willReturn(null);
        $sameProvinceWithName->getCode()->willReturn('code');
        $sameProvinceWithName->getName()->willReturn('name');

        $executionContext->addViolation($constraint->message)->shouldBeCalled();

        $this->validate(
            new ArrayCollection([
                $province->getWrappedObject(),
                $sameProvinceWithName->getWrappedObject(),
            ]),
            $constraint,
        );
    }

    function it_adds_violation_when_codes_are_duplicated(
        ExecutionContextInterface $executionContext,
        ProvinceInterface $firstProvince,
        ProvinceInterface $secondProvince,
        ProvinceInterface $thirdProvince,
    ): void {
        $constraint = new UniqueProvinceCollection();

        $firstProvince->getCode()->willReturn('same');
        $firstProvince->getName()->willReturn('first');
        $secondProvince->getCode()->willReturn('same');
        $secondProvince->getName()->willReturn('second');
        $thirdProvince->getCode()->willReturn('different');
        $thirdProvince->getName()->willReturn('third');

        $executionContext->addViolation($constraint->message)->shouldBeCalled();

        $this->validate(
            new ArrayCollection([
                $firstProvince->getWrappedObject(),
                $secondProvince->getWrappedObject(),
                $thirdProvince->getWrappedObject(),
            ]),
            $constraint,
        );
    }

    function it_adds_violation_when_names_are_duplicated(
        ExecutionContextInterface $executionContext,
        ProvinceInterface $firstProvince,
        ProvinceInterface $secondProvince,
        ProvinceInterface $thirdProvince,
    ): void {
        $constraint = new UniqueProvinceCollection();

        $firstProvince->getCode()->willReturn('first');
        $firstProvince->getName()->willReturn('first');
        $secondProvince->getCode()->willReturn('second');
        $secondProvince->getName()->willReturn('same');
        $thirdProvince->getCode()->willReturn('third');
        $thirdProvince->getName()->willReturn('same');

        $executionContext->addViolation($constraint->message)->shouldBeCalled();

        $this->validate(
            new ArrayCollection([
                $firstProvince->getWrappedObject(),
                $secondProvince->getWrappedObject(),
                $thirdProvince->getWrappedObject(),
            ]),
            $constraint,
        );
    }
}
