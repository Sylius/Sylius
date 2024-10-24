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
use Sylius\Bundle\CoreBundle\Validator\Constraints\ProductCodeExists;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ProductCodeExistsValidatorSpec extends ObjectBehavior
{
    const MESSAGE = 'sylius.product.code.not_exist';

    function let(ProductRepositoryInterface $productRepository, ExecutionContextInterface $context): void
    {
        $this->beConstructedWith($productRepository);

        $this->initialize($context);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_product_code_exists(
        Constraint $constraint,
    ): void {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('validate', ['product_code', $constraint])
        ;
    }

    function it_does_nothing_if_value_is_empty(
        ProductRepositoryInterface $productRepository,
        ExecutionContextInterface $context,
    ): void {
        $this->validate('', new ProductCodeExists());

        $context->buildViolation(self::MESSAGE)->shouldNotHaveBeenCalled();
        $productRepository->findOneBy(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_does_nothing_if_product_with_given_code_exists(
        ProductRepositoryInterface $productRepository,
        ExecutionContextInterface $context,
        ProductInterface $product,
    ): void {
        $productRepository->findOneByCode('product_code')->willReturn($product);
        $this->validate('product_code', new ProductCodeExists());

        $context->buildViolation(self::MESSAGE)->shouldNotHaveBeenCalled();
    }

    function it_adds_a_violation_if_product_with_given_code_does_not_exist(
        ProductRepositoryInterface $productRepository,
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $productRepository->findOneByCode('product_code')->willReturn(null);

        $constraintViolationBuilder->addViolation()->shouldBeCalled();
        $constraintViolationBuilder->atPath(Argument::any())->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->setParameter(Argument::cetera())->willReturn($constraintViolationBuilder);

        $context->buildViolation(self::MESSAGE)->shouldBeCalled()->willReturn($constraintViolationBuilder);

        $this->validate('product_code', new ProductCodeExists());
    }
}
