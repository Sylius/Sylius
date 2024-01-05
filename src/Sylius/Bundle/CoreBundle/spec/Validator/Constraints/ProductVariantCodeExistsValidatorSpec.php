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
use Sylius\Bundle\CoreBundle\Validator\Constraints\ProductVariantCodeExists;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ProductVariantCodeExistsValidatorSpec extends ObjectBehavior
{
    function let(
        ProductVariantRepositoryInterface $productVariantRepository,
        ExecutionContextInterface $context,
    ): void {
        $this->beConstructedWith($productVariantRepository);

        $this->initialize($context);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    function it_throws_exception_when_passed_constraint_is_not_product_variant_code_exists(
        Constraint $constraint,
    ): void {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('validate', ['variant_code', $constraint])
        ;
    }

    function it_does_nothing_when_passed_value_is_a_null(
        ProductVariantRepositoryInterface $productVariantRepository,
        ExecutionContextInterface $context,
    ): void {
        $productVariantRepository->findOneBy(Argument::any())->shouldNotBeCalled();
        $context->buildViolation(Argument::any())->shouldNotBeCalled();

        $this->validate(null, new ProductVariantCodeExists());
    }

    function it_does_nothing_when_passed_value_is_an_empty_string(
        ProductVariantRepositoryInterface $productVariantRepository,
        ExecutionContextInterface $context,
    ): void {
        $productVariantRepository->findOneBy(Argument::any())->shouldNotBeCalled();
        $context->buildViolation(Argument::any())->shouldNotBeCalled();

        $this->validate('', new ProductVariantCodeExists());
    }

    function it_does_nothing_when_a_variant_with_passed_code_exists(
        ProductVariantRepositoryInterface $productVariantRepository,
        ExecutionContextInterface $context,
        ProductVariantInterface $variant,
    ): void {
        $context->buildViolation(Argument::any())->shouldNotBeCalled();

        $productVariantRepository->findOneBy(['code' => 'test'])->willReturn($variant);

        $this->validate('test', new ProductVariantCodeExists());
    }

    function it_adds_violation_when_variant_with_passed_code_does_not_exist(
        ProductVariantRepositoryInterface $productVariantRepository,
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $violationBuilder,
    ): void {
        $constraint = new ProductVariantCodeExists();

        $productVariantRepository->findOneBy(['code' => 'test'])->willReturn(null);

        $context->buildViolation($constraint->message)->willReturn($violationBuilder);
        $violationBuilder->setParameter('{{ code }}', 'test')->willReturn($violationBuilder);
        $violationBuilder->addViolation()->shouldBeCalled();

        $this->validate('test', $constraint);
    }
}
