<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Tests\Form\Type\Taxon;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Bundle\CoreBundle\Form\Type\Taxon\ProductTaxonAutocompleteChoiceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceAutocompleteChoiceType;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ProductTaxonAutocompleteChoiceTypeTest extends TypeTestCase
{
    /**
     * @var ServiceRegistryInterface
     */
    private $resourceRepositoryRegistry;

    /**
     * @var FactoryInterface
     */
    private $productTaxonFactory;

    /**
     * @var RepositoryInterface
     */
    private $productTaxonRepository;

    protected function setUp()
    {
        $this->resourceRepositoryRegistry = $this->prophesize(ServiceRegistryInterface::class);
        $this->productTaxonFactory = $this->prophesize(FactoryInterface::class);
        $this->productTaxonRepository = $this->prophesize(RepositoryInterface::class);

        parent::setUp();
    }

    protected function getExtensions()
    {
        $productTaxonAutoCompleteType = new ProductTaxonAutocompleteChoiceType(
            $this->productTaxonFactory->reveal(),
            $this->productTaxonRepository->reveal()
        );
        $resourceAutoCompleteType = new ResourceAutocompleteChoiceType($this->resourceRepositoryRegistry->reveal());

        return [
            new PreloadedExtension([$productTaxonAutoCompleteType, $resourceAutoCompleteType], []),
        ];
    }

    /**
     * @test
     */
    public function it_creates_new_product_taxons_based_on_given_product_and_passed_taxon_codes()
    {
        $taxon = $this->prophesize(TaxonInterface::class);
        $product = $this->prophesize(ProductInterface::class);

        $taxonRepository = $this->prophesize(TaxonRepositoryInterface::class);

        $this->resourceRepositoryRegistry->get('sylius.taxon')->willReturn($taxonRepository);
        $taxonRepository->findOneBy(['code' => 'mug'])->willReturn($taxon);
        $taxonRepository->findOneBy(['code' => 'book'])->willReturn($taxon);
        $this->productTaxonRepository->findOneBy(['product' => $product, 'taxon' => $taxon])->willReturn(null);

        $productTaxon = $this->prophesize(ProductTaxonInterface::class);

        $this->productTaxonFactory->createNew()->willReturn($productTaxon);

        $form = $this->factory->create(ProductTaxonAutocompleteChoiceType::class, new ArrayCollection(), [
            'label' => 'sylius.form.product.taxons',
            'product' => $product->reveal(),
            'multiple' => true,
        ]);

        $form->submit('mug,book');
        $this->assertEquals(new ArrayCollection([$productTaxon->reveal(), $productTaxon->reveal()]), $form->getData());
    }

    /**
     * @test
     */
    public function it_returns_existing_product_taxons_based_on_given_product_and_passed_taxon_codes()
    {
        $taxon = $this->prophesize(TaxonInterface::class);
        $product = $this->prophesize(ProductInterface::class);
        $productTaxon = $this->prophesize(ProductTaxonInterface::class);
        $taxonRepository = $this->prophesize(TaxonRepositoryInterface::class);

        $this->resourceRepositoryRegistry->get('sylius.taxon')->willReturn($taxonRepository);
        $taxonRepository->findOneBy(['code' => 'mug'])->willReturn($taxon);
        $taxonRepository->findOneBy(['code' => 'book'])->willReturn($taxon);
        $this->productTaxonRepository->findOneBy(['product' => $product, 'taxon' => $taxon])->willReturn($productTaxon);

        $form = $this->factory->create(ProductTaxonAutocompleteChoiceType::class, new ArrayCollection(), [
            'label' => 'sylius.form.product.taxons',
            'product' => $product->reveal(),
            'multiple' => true,
        ]);

        $form->submit('mug,book');
        $this->assertEquals(new ArrayCollection([$productTaxon->reveal(), $productTaxon->reveal()]), $form->getData());
    }

    /**
     * @test
     */
    public function it_returns_new_product_taxon_based_on_given_product_and_passed_taxon_code()
    {
        $taxon = $this->prophesize(TaxonInterface::class);
        $product = $this->prophesize(ProductInterface::class);
        $taxonRepository = $this->prophesize(TaxonRepositoryInterface::class);

        $this->resourceRepositoryRegistry->get('sylius.taxon')->willReturn($taxonRepository);
        $taxonRepository->findOneBy(['code' => 'mug'])->willReturn($taxon);
        $this->productTaxonRepository->findOneBy(['product' => $product, 'taxon' => $taxon])->willReturn(null);

        $productTaxon = $this->prophesize(ProductTaxonInterface::class);

        $this->productTaxonFactory->createNew()->willReturn($productTaxon);

        $form = $this->factory->create(ProductTaxonAutocompleteChoiceType::class, null, [
            'label' => 'sylius.form.product.taxons',
            'product' => $product->reveal(),
            'multiple' => false,
        ]);

        $form->submit('mug');
        $this->assertEquals($productTaxon->reveal(), $form->getData());
    }

    /**
     * @test
     */
    public function it_returns_existing_product_taxon_based_on_given_product_and_passed_taxon_code()
    {
        $taxon = $this->prophesize(TaxonInterface::class);
        $product = $this->prophesize(ProductInterface::class);
        $productTaxon = $this->prophesize(ProductTaxonInterface::class);

        $taxonRepository = $this->prophesize(TaxonRepositoryInterface::class);

        $this->resourceRepositoryRegistry->get('sylius.taxon')->willReturn($taxonRepository);
        $taxonRepository->findOneBy(['code' => 'mug'])->willReturn($taxon);
        $this->productTaxonRepository->findOneBy(['product' => $product, 'taxon' => $taxon])->willReturn($productTaxon);

        $form = $this->factory->create(ProductTaxonAutocompleteChoiceType::class, null, [
            'label' => 'sylius.form.product.taxons',
            'product' => $product->reveal(),
            'multiple' => false,
        ]);

        $form->submit('mug');
        $this->assertEquals($productTaxon->reveal(), $form->getData());
    }
}
