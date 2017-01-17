<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ProductBundle\Validator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ProductBundle\Validator\Constraint\UniqueSimpleProductCode;
use Sylius\Bundle\ProductBundle\Validator\UniqueSimpleProductCodeValidator;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Product\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class UniqueSimpleProductCodeValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $context, ProductVariantRepositoryInterface $productVariantRepository) {
        $this->beConstructedWith($productVariantRepository);
        $this->initialize($context);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UniqueSimpleProductCodeValidator::class);
    }

    function it_is_a_constraint_validator()
    {
        $this->shouldImplement(ConstraintValidator::class);
    }

    function it_does_not_add_violation_if_product_is_configurable(
        ExecutionContextInterface $context,
        ProductInterface $product
    ) {
        $constraint = new UniqueSimpleProductCode([
            'message' => 'Simple product code has to be unique',
        ]);

        $product->isSimple()->willReturn(false);

        $context->buildViolation(Argument::any())->shouldNotBeCalled();

        $this->validate($product, $constraint);
    }

    function it_does_not_add_violation_if_product_is_simple_but_code_has_not_been_used_among_neither_producs_nor_product_variants(
        ExecutionContextInterface $context,
        ProductInterface $product,
        ProductVariantRepositoryInterface $productVariantRepository
    ) {
        $constraint = new UniqueSimpleProductCode([
            'message' => 'Simple product code has to be unique',
        ]);

        $product->isSimple()->willReturn(true);
        $product->getCode()->willReturn('AWESOME_PRODUCT');

        $context->buildViolation(Argument::any())->shouldNotBeCalled();

        $productVariantRepository->findOneBy(['code' => 'AWESOME_PRODUCT'])->willReturn(null);

        $this->validate($product, $constraint);
    }

    function it_does_not_add_violation_if_product_is_simple_code_has_been_used_but_for_the_same_product(
        ExecutionContextInterface $context,
        ProductInterface $product,
        ProductVariantInterface $existingProductVariant,
        ProductVariantRepositoryInterface $productVariantRepository
    ) {
        $constraint = new UniqueSimpleProductCode([
            'message' => 'Simple product code has to be unique',
        ]);

        $product->isSimple()->willReturn(true);
        $product->getCode()->willReturn('AWESOME_PRODUCT');
        $product->getId()->willReturn(1);

        $context->buildViolation(Argument::any())->shouldNotBeCalled();

        $productVariantRepository->findOneBy(['code' => 'AWESOME_PRODUCT'])->willReturn($existingProductVariant);
        $existingProductVariant->getProduct()->willReturn($product);

        $this->validate($product, $constraint);
    }

    function it_add_violation_if_product_is_simple_and_code_has_been_used_in_other_product_variant(
        ExecutionContextInterface $context,
        ProductInterface $product,
        ProductInterface $existingProduct,
        ProductVariantInterface $existingProductVariant,
        ProductVariantRepositoryInterface $productVariantRepository,
        ConstraintViolationBuilderInterface $constraintViolationBuilder
    ) {
        $constraint = new UniqueSimpleProductCode([
            'message' => 'Simple product code has to be unique',
        ]);

        $product->isSimple()->willReturn(true);
        $product->getCode()->willReturn('AWESOME_PRODUCT');
        $product->getId()->willReturn(1);

        $context->buildViolation('Simple product code has to be unique', Argument::cetera())->willReturn($constraintViolationBuilder);

        $constraintViolationBuilder->atPath('code')->shouldBeCalled()->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled()->willReturn($constraintViolationBuilder);

        $productVariantRepository->findOneBy(['code' => 'AWESOME_PRODUCT'])->willReturn($existingProductVariant);
        $existingProductVariant->getProduct()->willReturn($existingProduct);
        $existingProduct->getId()->willReturn(2);

        $this->validate($product, $constraint);
    }
}
