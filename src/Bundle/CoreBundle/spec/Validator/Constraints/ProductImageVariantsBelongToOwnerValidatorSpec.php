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

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ProductImageVariantsBelongToOwner;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ProductImageVariantsBelongToOwnerValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $executionContext): void
    {
        $this->initialize($executionContext);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_an_exception_if_value_is_not_product_image(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new \stdClass(), new ProductImageVariantsBelongToOwner()])
        ;
    }

    function it_throws_an_exception_if_constraint_is_not_product_image_variants_belong_to_owner(
        Constraint $constraint,
        ProductImageInterface $image,
    ): void {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [$image, $constraint])
        ;
    }

    function it_adds_a_violation_if_any_variant_does_not_belong_to_a_product_which_is_an_owner(
        ExecutionContextInterface $executionContext,
        ProductImageInterface $image,
        ProductInterface $product,
        ProductVariantInterface $variant,
    ): void {
        $image->getOwner()->willReturn($product);
        $image->getProductVariants()->willReturn(new ArrayCollection([$variant->getWrappedObject()]));

        $product->getCode()->willReturn('MUG');
        $product->hasVariant($variant)->willReturn(false);

        $variant->getCode()->willReturn('GREEN_SHIRT');

        $executionContext
            ->addViolation(
                'sylius.product_image.product_variant.not_belong_to_owner',
                ['%productVariantCode%' => 'GREEN_SHIRT', '%ownerCode%' => 'MUG'],
            )
            ->shouldBeCalled()
        ;

        $this->validate($image, new ProductImageVariantsBelongToOwner());
    }

    function it_does_nothing_if_all_variants_belong_to_a_product_which_is_an_owner(
        ExecutionContextInterface $executionContext,
        ProductImageInterface $image,
        ProductInterface $product,
        ProductVariantInterface $firstVariant,
        ProductVariantInterface $secondVariant,
    ): void {
        $image->getOwner()->willReturn($product);
        $image->getProductVariants()->willReturn(new ArrayCollection([
            $firstVariant->getWrappedObject(),
            $secondVariant->getWrappedObject(),
        ]));

        $product->getCode()->willReturn('MUG');
        $product->hasVariant($firstVariant)->willReturn(true);
        $product->hasVariant($secondVariant)->willReturn(true);

        $executionContext
            ->addViolation(
                'sylius.product_image.product_variant.not_belong_to_owner',
                Argument::any(),
            )
            ->shouldNotBeCalled()
        ;

        $this->validate($image, new ProductImageVariantsBelongToOwner());
    }
}
