<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ProductBundle\Builder;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ProductBundle\Model\ProductInterface;
use Sylius\Bundle\ProductBundle\Model\ProductPropertyInterface;
use Sylius\Bundle\ProductBundle\Model\PropertyInterface;
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ProductBuilderSpec extends ObjectBehavior
{
    function let(
        ProductInterface $product,
        ObjectManager $productManager,
        RepositoryInterface $productRepository,
        RepositoryInterface $propertyRepository,
        RepositoryInterface $productPropertyRepository
    )
    {
        $this->beConstructedWith(
            $productManager,
            $productRepository,
            $propertyRepository,
            $productPropertyRepository
        );

        $productRepository->createNew()->shouldBeCalled()->willReturn($product);

        $this->create('Black GitHub Mug')->shouldReturn($this);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ProductBundle\Builder\ProductBuilder');
    }

    function it_adds_property_to_product_if_already_exists(
        $propertyRepository,
        $productPropertyRepository,
        $product,
        PropertyInterface $property,
        ProductPropertyInterface $productProperty
    )
    {
        $propertyRepository->findOneBy(array('name' => 'collection'))->shouldBeCalled()->willReturn($property);
        $productPropertyRepository->createNew()->shouldBeCalled()->willReturn($productProperty);

        $productProperty->setProperty($property)->shouldBeCalled();
        $productProperty->setValue(2013)->shouldBeCalled();

        $product->addProperty($productProperty)->shouldBeCalled();

        $this->addProperty('collection', 2013)->shouldReturn($this);
    }

    function it_creates_property_if_it_does_not_exist(
        $propertyRepository,
        $productPropertyRepository,
        $productManager,
        $product,
        PropertyInterface $property,
        ProductPropertyInterface $productProperty
    )
    {
        $propertyRepository->findOneBy(array('name' => 'collection'))->shouldBeCalled()->willReturn(null);
        $propertyRepository->createNew()->shouldBeCalled()->willReturn($property);

        $property->setName('collection')->shouldBeCalled();
        $property->setPresentation('collection')->shouldBeCalled();

        $productManager->persist($property)->shouldBeCalled();
        $productManager->flush($property)->shouldBeCalled();

        $productPropertyRepository->createNew()->shouldBeCalled()->willReturn($productProperty);

        $productProperty->setProperty($property)->shouldBeCalled();
        $productProperty->setValue(2013)->shouldBeCalled();

        $product->addProperty($productProperty)->shouldBeCalled();

        $this->addProperty('collection', 2013)->shouldReturn($this);
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
