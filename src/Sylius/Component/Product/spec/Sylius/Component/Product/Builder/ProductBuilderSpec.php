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

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Model\AttributeInterface;
use Sylius\Component\Product\Model\AttributeValueInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ProductBuilderSpec extends ObjectBehavior
{
    function let(
        ProductInterface $product,
        ObjectManager $productManager,
        RepositoryInterface $productRepository,
        RepositoryInterface $attributeRepository,
        RepositoryInterface $attributeValueRepository
    )
    {
        $this->beConstructedWith(
            $productManager,
            $productRepository,
            $attributeRepository,
            $attributeValueRepository
        );

        $productRepository->createNew()->shouldBeCalled()->willReturn($product);

        $this->create('Black GitHub Mug')->shouldReturn($this);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Product\Builder\ProductBuilder');
    }

    function it_adds_attribute_to_product_if_already_exists(
        $attributeRepository,
        $attributeValueRepository,
        $product,
        AttributeInterface $attribute,
        AttributeValueInterface $attributeValue
    )
    {
        $attributeRepository->findOneBy(array('name' => 'collection'))->shouldBeCalled()->willReturn($attribute);
        $attributeValueRepository->createNew()->shouldBeCalled()->willReturn($attributeValue);

        $attributeValue->setAttribute($attribute)->shouldBeCalled();
        $attributeValue->setValue(2013)->shouldBeCalled();

        $product->addAttribute($attributeValue)->shouldBeCalled();

        $this->addAttribute('collection', 2013)->shouldReturn($this);
    }

    function it_creates_attribute_if_it_does_not_exist(
        $attributeRepository,
        $attributeValueRepository,
        $productManager,
        $product,
        AttributeInterface $attribute,
        AttributeValueInterface $attributeValue
    )
    {
        $attributeRepository->findOneBy(array('name' => 'collection'))->shouldBeCalled()->willReturn(null);
        $attributeRepository->createNew()->shouldBeCalled()->willReturn($attribute);

        $attribute->setName('collection')->shouldBeCalled();
        $attribute->setPresentation('collection')->shouldBeCalled();

        $productManager->persist($attribute)->shouldBeCalled();
        $productManager->flush($attribute)->shouldBeCalled();

        $attributeValueRepository->createNew()->shouldBeCalled()->willReturn($attributeValue);

        $attributeValue->setAttribute($attribute)->shouldBeCalled();
        $attributeValue->setValue(2013)->shouldBeCalled();

        $product->addAttribute($attributeValue)->shouldBeCalled();

        $this->addAttribute('collection', 2013)->shouldReturn($this);
    }

    function it_saves_product($productManager, $product)
    {
        $productManager->persist($product)->shouldBeCalled();
        $productManager->flush($product)->shouldBeCalled();

        $this->save()->shouldReturn($product);
    }

    function it_saves_product_without_flushing_if_needed($productManager, $product)
    {
        $productManager->persist($product)->shouldBeCalled();
        $productManager->flush($product)->shouldNotBeCalled();

        $this->save(false)->shouldReturn($product);
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

    function it_throws_exception_when_product_method_is_not_defined($product)
    {
        $this->shouldThrow(new \BadMethodCallException('Product has no getFoo() method.'))->during('getFoo');
    }
}
