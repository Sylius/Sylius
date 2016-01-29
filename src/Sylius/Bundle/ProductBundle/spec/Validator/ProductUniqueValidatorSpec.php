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

use Doctrine\Common\Persistence\ObjectRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ProductBundle\Validator\Constraint\ProductUnique;
use Sylius\Component\Product\Model\ProductInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ExecutionContext;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ProductUniqueValidatorSpec extends ObjectBehavior
{
    function let(ObjectRepository $productRepository, ExecutionContext $context)
    {
        $this->beConstructedWith($productRepository);
        $this->initialize($context);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ProductBundle\Validator\ProductUniqueValidator');
    }

    function it_is_a_constraint_validator()
    {
        $this->shouldImplement(ConstraintValidator::class);
    }

    function it_adds_violation_if_product_with_given_property_value_already_exists(
        $productRepository,
        ProductInterface $product,
        ProductInterface $conflictualProduct,
        $context
    ) {
        $constraint = new ProductUnique([
            'property' => 'name',
            'message' => 'Product with given name already exists.',
        ]);

        $product->getName()->willReturn('iPhone');
        $productRepository->findOneBy(['name' => 'iPhone'])->shouldBeCalled()->willReturn($conflictualProduct);

        $context->addViolationAt('name', 'Product with given name already exists.', Argument::any())->shouldBeCalled();

        $this->validate($product, $constraint);
    }

    function it_does_not_add_violation_if_product_with_given_property_value_does_not_exist(
        $productRepository,
        ProductInterface $product,
        $context
    ) {
        $constraint = new ProductUnique([
            'property' => 'name',
            'message' => 'Product with given name already exists.',
        ]);

        $product->getName()->willReturn('iPhone');
        $productRepository->findOneBy(['name' => 'iPhone'])->shouldBeCalled()->willReturn(null);

        $context->addViolationAt(Argument::any())->shouldNotBeCalled();

        $this->validate($product, $constraint);
    }

    function it_does_not_add_violation_if_conflictual_product_and_validated_one_are_the_same(
        $productRepository,
        ProductInterface $product,
        $context
    ) {
        $constraint = new ProductUnique([
            'property' => 'name',
            'message' => 'Product with given name already exists',
        ]);

        $product->getName()->willReturn('iPhone');
        $productRepository->findOneBy(['name' => 'iPhone'])->shouldBeCalled()->willReturn($product);

        $context->addViolationAt(Argument::any())->shouldNotBeCalled();

        $this->validate($product, $constraint);
    }
}
