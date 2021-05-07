<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ProductVariantExist;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ProductVariantExistValidatorSpec extends ObjectBehavior
{
    function let(ProductVariantRepositoryInterface $productVariantRepository): void
    {
        $this->beConstructedWith($productVariantRepository);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_an_exception_if_value_is_not_an_instance_of_add_item_to_cart_command(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new CompleteOrder(), new ProductVariantExist()]);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_product_variant_exist(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new AddItemToCart('productCode', 'productVariantCode', 1), new class() extends Constraint {
            }])
        ;
    }

    function it_adds_violation_if_product_variant_does_not_exist(
        ProductVariantRepositoryInterface $productVariantRepository,
        ExecutionContextInterface $executionContext
    ): void {
        $this->initialize($executionContext);

        $value = new AddItemToCart('productCode', 'productVariantCode', 1);
        $constraint = new ProductVariantExist();
        $constraint->message = 'message';

        $productVariantRepository->findOneBy(['code' => 'productVariantCode'])->willReturn(null);

        $executionContext
            ->addViolation('message', ['%productVariantCode%' => 'productVariantCode'])
            ->shouldBeCalled()
        ;

        $this->validate($value, $constraint);
    }

    function it_does_nothing_if_product_variant_does_exist(
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductVariantInterface $productVariant,
        ExecutionContextInterface $executionContext
    ): void {
        $this->initialize($executionContext);

        $value = new AddItemToCart('productCode', 'productVariantCode', 1);
        $constraint = new ProductVariantExist();
        $constraint->message = 'message';

        $productVariantRepository->findOneBy(['code' => 'productVariantCode'])->willReturn($productVariant);

        $executionContext
            ->addViolation('message', ['%productVariantCode%' => 'productVariantCode'])
            ->shouldNotBeCalled()
        ;

        $this->validate($value, $constraint);
    }
}
