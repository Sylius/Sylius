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
use Sylius\Bundle\ProductBundle\Validator\Constraint\ProductUnique;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ProductUniqueValidatorSpec extends ObjectBehavior
{
    /**
     * @param Doctrine\Common\Persistence\ObjectRepository $productRepository
     * @param Symfony\Component\Validator\ExecutionContext $context
     */
    function let($productRepository, $context)
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
        $this->shouldImplement('Symfony\Component\Validator\ConstraintValidator');
    }

    /**
     * @param Sylius\Bundle\ProductBundle\Model\ProductInterface $product
     * @param Sylius\Bundle\ProductBundle\Model\ProductInterface $conflictualProduct
     */
    function it_adds_violation_if_product_with_given_property_value_already_exists($productRepository, $product, $conflictualProduct, $context)
    {
        $constraint = new ProductUnique(array(
            'property' => 'name',
            'message'  => 'Product with given name already exists.'
        ));

        $product->getName()->willReturn('iPhone');
        $productRepository->findOneBy(array('name' => 'iPhone'))->shouldBeCalled()->willReturn($conflictualProduct);

        $context->addViolationAt('name', 'Product with given name already exists.', Argument::any())->shouldBeCalled();

        $this->validate($product, $constraint);
    }

    /**
     * @param Sylius\Bundle\ProductBundle\Model\ProductInterface $product
     */
    function it_does_not_add_violation_if_product_with_given_property_value_does_not_exist($productRepository, $product, $context)
    {
        $constraint = new ProductUnique(array(
            'property' => 'name',
            'message'  => 'Product with given name already exists.'
        ));

        $product->getName()->willReturn('iPhone');
        $productRepository->findOneBy(array('name' => 'iPhone'))->shouldBeCalled()->willReturn(null);

        $context->addViolationAt(Argument::any())->shouldNotBeCalled();

        $this->validate($product, $constraint);
    }

    /**
     * @param Sylius\Bundle\ProductBundle\Model\ProductInterface $product
     */
    function it_does_not_add_violation_if_conflictual_product_and_validated_one_are_the_same($productRepository, $product, $context)
    {
        $constraint = new ProductUnique(array(
            'property' => 'name',
            'message'  => 'Product with given name already exists'
        ));

        $product->getName()->willReturn('iPhone');
        $productRepository->findOneBy(array('name' => 'iPhone'))->shouldBeCalled()->willReturn($product);

        $context->addViolationAt(Argument::any())->shouldNotBeCalled();

        $this->validate($product, $constraint);
    }
}
