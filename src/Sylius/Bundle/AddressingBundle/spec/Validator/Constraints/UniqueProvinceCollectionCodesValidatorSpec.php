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
use Sylius\Bundle\AddressingBundle\Validator\Constraints\UniqueProvinceCollectionCodes;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class UniqueProvinceCollectionCodesValidatorSpec extends ObjectBehavior
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
                new UniqueProvinceCollectionCodes(),
            ])
        ;
    }

    function it_throws_exception_when_collection_contains_something_other_than_provinces(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [
                new ArrayCollection([new \stdClass()]),
                new UniqueProvinceCollectionCodes(),
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

        $this->validate(new ArrayCollection(), new UniqueProvinceCollectionCodes());
    }

    function it_does_nothing_when_some_provinces_have_no_code(
        ExecutionContextInterface $executionContext,
        ProvinceInterface $hasCode,
        ProvinceInterface $doesNotHaveCode,
    ): void {
        $hasCode->getCode()->willReturn('code');
        $doesNotHaveCode->getCode()->willReturn(null);

        $executionContext->addViolation(Argument::any())->shouldNotBeCalled();

        $this->validate(
            new ArrayCollection([
                $hasCode->getWrappedObject(),
                $doesNotHaveCode->getWrappedObject(),
            ]),
            new UniqueProvinceCollectionCodes(),
        );
    }

    function it_does_nothing_when_all_provinces_have_unique_codes(
        ExecutionContextInterface $executionContext,
        ProvinceInterface $firstProvince,
        ProvinceInterface $secondProvince,
    ): void {
        $firstProvince->getCode()->willReturn('first');
        $secondProvince->getCode()->willReturn('second');

        $executionContext->addViolation(Argument::any())->shouldNotBeCalled();

        $this->validate(
            new ArrayCollection([
                $firstProvince->getWrappedObject(),
                $secondProvince->getWrappedObject(),
            ]),
            new UniqueProvinceCollectionCodes(),
        );
    }

    function it_adds_violation_when_codes_are_duplicated(
        ExecutionContextInterface $executionContext,
        ProvinceInterface $firstProvince,
        ProvinceInterface $secondProvince,
        ProvinceInterface $thirdProvince,
    ): void {
        $constraint = new UniqueProvinceCollectionCodes();
        $firstProvince->getCode()->willReturn('same');
        $secondProvince->getCode()->willReturn('same');
        $thirdProvince->getCode()->willReturn('different');

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
