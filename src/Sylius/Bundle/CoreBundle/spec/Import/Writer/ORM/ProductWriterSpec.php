<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Import\Writer\ORM;

use Doctrine\ORM\EntityManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Archetype\Model\ArchetypeInterface;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ProductWriterSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $productRepository,
        RepositoryInterface $archetypeRepository,
        RepositoryInterface $taxCategoryRepository,
        RepositoryInterface $shippingCategoryRepository, 
        EntityManager $em)
    {
        $this->beConstructedWith(
            $productRepository,
            $archetypeRepository,
            $taxCategoryRepository,
            $shippingCategoryRepository,
            $em);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Import\Writer\ORM\ProductWriter');
    }

    function it_is_abstract_doctrine_writer_object()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Import\Writer\ORM\AbstractDoctrineWriter');
    }

    function it_implements_writer_interface()
    {
        $this->shouldImplement('Sylius\Component\ImportExport\Writer\WriterInterface');
    }

    function it_creates_new_product_if_it_does_not_exist(
            $productRepository,
            $archetypeRepository,
            $taxCategoryRepository,
            $shippingCategoryRepository,
            Product $product,
            ArchetypeInterface $archetype,
            TaxCategoryInterface $taxCategory,
            ShippingCategoryInterface $shippingCategory)
    {
        $data = array(
            'id' => 1,
            'name' => 'testProduct', 
            'price' => 2, 
            'description' => 'Long lorem ipsum', 
            'short_description' => 'lorem', 
            'archetype' => 'testArchetype', 
            'tax_category' => 'testTaxCategory', 
            'shipping_category' => 'testShippingCategory', 
            'is_available_on' => '2015-02-10 10:02:09', 
            'meta_keywords' => 'Sint, fuga, quo, magni, hic.', 
            'meta_description' => 'Autem quos tempora culpa facere nulla.', 
            'createdAt' => '2015-02-10 10:02:09');

        $productRepository->find('1')->willReturn(null);
        $productRepository->createNew()->willReturn($product);

        $archetypeRepository->findOneBy(array('code' => 'testArchetype'))->willReturn($archetype);
        $taxCategoryRepository->findOneBy(array('name' => 'testTaxCategory'))->willReturn($taxCategory);
        $shippingCategoryRepository->find('testShippingCategory')->willReturn($shippingCategory);

        $product->setName('testProduct');
        $product->setPrice('2');
        $product->setDescription('Long lorem ipsum');
        $product->setShortDescription('lorem');
        $product->setArchetype($archetype);
        $product->setTaxCategory($taxCategory);
        $product->setShippingCategory($shippingCategory);
        $product->setAvailableOn(new \DateTime('2015-02-10 10:02:09'));
        $product->setMetaKeyWords('Sint, fuga, quo, magni, hic.');
        $product->setMetaDescription('Autem quos tempora culpa facere nulla.');
        $product->setCreatedAt(new \DateTime('2015-02-10 10:02:09'));
        
        $this->process($data)->shouldReturn($product);
    }

    function it_updates_product_if_it_exists(
            $productRepository,
            $archetypeRepository,
            $taxCategoryRepository,
            $shippingCategoryRepository,
            Product $product,
            ArchetypeInterface $archetype,
            TaxCategoryInterface $taxCategory,
            ShippingCategoryInterface $shippingCategory)
    {
        $data = array(
            'id' => 1,
            'name' => 'testProduct', 
            'price' => 2, 
            'description' => 'Long lorem ipsum', 
            'short_description' => 'lorem', 
            'archetype' => 'testArchetype', 
            'tax_category' => 'testTaxCategory', 
            'shipping_category' => 'testShippingCategory', 
            'is_available_on' => '2015-02-10 10:02:09', 
            'meta_keywords' => 'Sint, fuga, quo, magni, hic.', 
            'meta_description' => 'Autem quos tempora culpa facere nulla.', 
            'createdAt' => '2015-02-10 10:02:09');

        $productRepository->find('1')->willReturn($product);
        $productRepository->createNew()->shouldNotBeCalled();

        $archetypeRepository->findOneBy(array('code' => 'testArchetype'))->willReturn($archetype);
        $taxCategoryRepository->findOneBy(array('name' => 'testTaxCategory'))->willReturn($taxCategory);
        $shippingCategoryRepository->find('testShippingCategory')->willReturn($shippingCategory);

        $product->setName('testProduct');
        $product->setPrice('2');
        $product->setDescription('Long lorem ipsum');
        $product->setShortDescription('lorem');
        $product->setArchetype($archetype);
        $product->setTaxCategory($taxCategory);
        $product->setShippingCategory($shippingCategory);
        $product->setAvailableOn(new \DateTime('2015-02-10 10:02:09'));
        $product->setMetaKeyWords('Sint, fuga, quo, magni, hic.');
        $product->setMetaDescription('Autem quos tempora culpa facere nulla.');
        $product->setCreatedAt(new \DateTime('2015-02-10 10:02:09'));
        
        $this->process($data)->shouldReturn($product);
    }

    function it_has_type()
    {
        $this->getType()->shouldReturn('product');
    }
}