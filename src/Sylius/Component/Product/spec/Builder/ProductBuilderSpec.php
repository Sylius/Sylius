<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Product\Builder;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Model\AttributeInterface;
use Sylius\Component\Product\Model\AttributeValueInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Resource\Factory\ResourceFactoryInterface;
use Sylius\Component\Resource\Manager\ResourceManagerInterface;
use Sylius\Component\Resource\Repository\ResourceRepositoryInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ProductBuilderSpec extends ObjectBehavior
{
    function let(
        ProductInterface $product,
        ResourceManagerInterface $productManager,
        ResourceFactoryInterface $productFactory,
        ResourceRepositoryInterface $attributeRepository,
        ResourceFactoryInterface $attributeFactory,
        ResourceFactoryInterface $attributeValueFactory
    ) {
        $this->beConstructedWith(
            $productManager,
            $productFactory,
            $attributeRepository,
            $attributeFactory,
            $attributeValueFactory
        );

        $productFactory->createNew()->shouldBeCalled()->willReturn($product);

        $this->create('Black GitHub Mug')->shouldReturn($this);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Product\Builder\ProductBuilder');
    }

    function it_adds_attribute_to_product_if_already_exists(
        $attributeRepository,
        $attributeValueFactory,
        $product,
        AttributeInterface $attribute,
        AttributeValueInterface $attributeValue
    ) {
        $attributeRepository->findOneBy(array('name' => 'collection'))->shouldBeCalled()->willReturn($attribute);
        $attributeValueFactory->createNew()->shouldBeCalled()->willReturn($attributeValue);

        $attributeValue->setAttribute($attribute)->shouldBeCalled();
        $attributeValue->setValue(2013)->shouldBeCalled();

        $product->addAttribute($attributeValue)->shouldBeCalled();

        $this->addAttribute('collection', 2013)->shouldReturn($this);
    }

    function it_creates_attribute_if_it_does_not_exist(
        $attributeRepository,
        $attributeValueFactory,
        $attributeFactory,
        $productManager,
        $product,
        AttributeInterface $attribute,
        AttributeValueInterface $attributeValue
    ) {
        $attributeRepository->findOneBy(array('name' => 'collection'))->shouldBeCalled()->willReturn(null);
        $attributeFactory->createNew()->shouldBeCalled()->willReturn($attribute);

        $attribute->setName('collection')->shouldBeCalled();
        $attribute->setPresentation('collection')->shouldBeCalled();

        $attributeValueFactory->createNew()->shouldBeCalled()->willReturn($attributeValue);

        $attributeValue->setAttribute($attribute)->shouldBeCalled();
        $attributeValue->setValue(2013)->shouldBeCalled();

        $product->addAttribute($attributeValue)->shouldBeCalled();

        $this->addAttribute('collection', 2013)->shouldReturn($this);
    }

    function it_saves_product($productManager, $product)
    {
        $productManager->persist($product)->shouldBeCalled();
        $productManager->flush()->shouldBeCalled();

        $this->save()->shouldReturn($product);
    }

    function it_proxies_undefined_methods_to_product($product)
    {
        $name = 'Black GitHub Mug';
        $description = "Coffee. Tea. Coke. Water. Let's face it — humans need to drink liquids";

        $product->setName($name)->shouldBeCalled();
        $product->setDescription($description)->shouldBeCalled();

        $this->setName($name)->shouldReturn($this);
        $this->setDescription($description)->shouldReturn($this);
    }

    function it_throws_exception_when_product_method_is_not_defined()
    {
        $this->shouldThrow(new \BadMethodCallException('Product has no "getFoo()" method.'))->during('getFoo');
    }
}
