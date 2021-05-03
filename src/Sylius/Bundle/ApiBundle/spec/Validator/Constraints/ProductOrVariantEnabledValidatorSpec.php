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

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Bundle\ApiBundle\Validator\Constraints\OrderNotEmpty;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ProductOrVariantEnabled;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ProductOrVariantEnabledValidatorSpec extends ObjectBehavior
{
    function let(ProductVariantRepositoryInterface $productVariantRepository): void
    {
        $this->beConstructedWith($productVariantRepository);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_an_exception_if_value_is_not_an_instance_of_product_or_variant_enabled_class(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new CompleteOrder(), new class() extends Constraint {
            }])
        ;
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_add_item_to_cart(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', ['', new class() extends Constraint {
            }])
        ;
    }

    function it_adds_violation_if_product_variant_is_disabled(
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        ExecutionContextInterface $executionContext
    ): void {
        $this->initialize($executionContext);

        $value = new AddItemToCart('productVariantCode', 1);
        $constraint = new ProductOrVariantEnabled();
        $constraint->message = 'message';

        $productVariantRepository->findOneBy(['code' => 'productVariantCode'])->willReturn($productVariant);
        $productVariant->isEnabled()->willReturn(false);
        $productVariant->getName()->willReturn('NAME');
        $executionContext
            ->addViolation(
                'message',
                ['%productName%' => 'NAME']
            )
            ->shouldBeCalled();

        $productVariant->getProduct()->willReturn($product);

        $this->validate($value, $constraint);
    }

    function it_adds_violation_if_product_is_disabled(
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        ExecutionContextInterface $executionContext
    ): void {
        $this->initialize($executionContext);

        $value = new AddItemToCart('productVariantCode', 1);
        $constraint = new ProductOrVariantEnabled();
        $constraint->message = 'message';

        $productVariantRepository->findOneBy(['code' => 'productVariantCode'])->willReturn($productVariant);
        $productVariant->isEnabled()->willReturn(true);
        $productVariant->getName()->willReturn('NAME');
        $executionContext
            ->addViolation(
                'message',
                ['%productName%' => 'NAME']
            )
            ->shouldNotBeCalled();

        $productVariant->getProduct()->willReturn($product);

        $product->isEnabled()->willReturn(false);
        $product->getName()->willReturn('PRODUCTNAME');
        $executionContext
            ->addViolation(
                'message',
                ['%productName%' => 'PRODUCTNAME']
            )
            ->shouldBeCalled();

        $this->validate($value, $constraint);
    }

    function it_does_nothing_if_product_and_variant_are_enabled(
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        ExecutionContextInterface $executionContext
    ): void {
        $this->initialize($executionContext);

        $value = new AddItemToCart('productVariantCode', 1);
        $constraint = new ProductOrVariantEnabled();
        $constraint->message = 'message';

        $productVariantRepository->findOneBy(['code' => 'productVariantCode'])->willReturn($productVariant);
        $productVariant->isEnabled()->willReturn(true);
        $productVariant->getName()->willReturn('NAME');
        $executionContext
            ->addViolation(
                'message',
                ['%productName%' => 'NAME']
            )
            ->shouldNotBeCalled();

        $productVariant->getProduct()->willReturn($product);

        $product->isEnabled()->willReturn(true);
        $product->getName()->willReturn('PRODUCTNAME');
        $executionContext
            ->addViolation(
                'message',
                ['%productName%' => 'PRODUCTNAME']
            )
            ->shouldNotBeCalled();

        $this->validate($value, $constraint);
    }
}
