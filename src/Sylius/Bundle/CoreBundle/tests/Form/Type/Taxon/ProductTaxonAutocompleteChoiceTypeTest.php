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

namespace Tests\Sylius\Bundle\CoreBundle\Form\Type\Taxon;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Bundle\CoreBundle\Form\Type\Taxon\ProductTaxonAutocompleteChoiceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceAutocompleteChoiceType;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

final class ProductTaxonAutocompleteChoiceTypeTest extends TypeTestCase
{
    private MockObject&ServiceRegistryInterface $resourceRepositoryRegistry;

    private FactoryInterface&MockObject $productTaxonFactory;

    private MockObject&RepositoryInterface $productTaxonRepository;

    protected function setUp(): void
    {
        $this->resourceRepositoryRegistry = $this->createMock(ServiceRegistryInterface::class);
        $this->productTaxonFactory = $this->createMock(FactoryInterface::class);
        $this->productTaxonRepository = $this->createMock(RepositoryInterface::class);

        parent::setUp();
    }

    protected function getExtensions(): array
    {
        $productTaxonAutoCompleteType = new ProductTaxonAutocompleteChoiceType(
            $this->productTaxonFactory,
            $this->productTaxonRepository,
        );
        $resourceAutoCompleteType = new ResourceAutocompleteChoiceType($this->resourceRepositoryRegistry);

        return [
            new PreloadedExtension([$productTaxonAutoCompleteType, $resourceAutoCompleteType], []),
        ];
    }

    #[Test]
    public function it_creates_new_product_taxons_based_on_given_product_and_passed_taxon_codes(): void
    {
        $taxon = $this->createMock(TaxonInterface::class);
        $product = $this->createMock(ProductInterface::class);

        /** @var TaxonRepositoryInterface&MockObject $taxonRepository */
        $taxonRepository = $this->createMock(TaxonRepositoryInterface::class);

        $this->resourceRepositoryRegistry->method('get')->with('sylius.taxon')->willReturn($taxonRepository);
        $taxonRepository->method('findOneBy')->willReturnMap([
            [['code' => 'mug'], $taxon],
            [['code' => 'book'], $taxon],
        ]);
        $this->productTaxonRepository->method('findOneBy')->with(['product' => $product, 'taxon' => $taxon])->willReturn(null);

        $productTaxon = $this->createMock(ProductTaxonInterface::class);

        $this->productTaxonFactory->method('createNew')->willReturn($productTaxon);

        $form = $this->factory->create(ProductTaxonAutocompleteChoiceType::class, new ArrayCollection(), [
            'label' => 'sylius.form.product.taxons',
            'product' => $product,
            'multiple' => true,
        ]);

        $form->submit('mug,book');
        $this->assertEquals(new ArrayCollection([$productTaxon, $productTaxon]), $form->getData());
    }

    #[Test]
    public function it_returns_existing_product_taxons_based_on_given_product_and_passed_taxon_codes(): void
    {
        $taxon = $this->createMock(TaxonInterface::class);
        $product = $this->createMock(ProductInterface::class);
        $productTaxon = $this->createMock(ProductTaxonInterface::class);

        /** @var TaxonRepositoryInterface&MockObject $taxonRepository */
        $taxonRepository = $this->createMock(TaxonRepositoryInterface::class);

        $this->resourceRepositoryRegistry->method('get')->with('sylius.taxon')->willReturn($taxonRepository);
        $taxonRepository->method('findOneBy')->willReturnMap([
            [['code' => 'mug'], $taxon],
            [['code' => 'book'], $taxon],
        ]);
        $this->productTaxonRepository->method('findOneBy')->with(['product' => $product, 'taxon' => $taxon])->willReturn($productTaxon);

        $form = $this->factory->create(ProductTaxonAutocompleteChoiceType::class, new ArrayCollection(), [
            'label' => 'sylius.form.product.taxons',
            'product' => $product,
            'multiple' => true,
        ]);

        $form->submit('mug,book');
        $this->assertEquals(new ArrayCollection([$productTaxon, $productTaxon]), $form->getData());
    }

    #[Test]
    public function it_returns_new_product_taxon_based_on_given_product_and_passed_taxon_code(): void
    {
        $taxon = $this->createMock(TaxonInterface::class);
        $product = $this->createMock(ProductInterface::class);

        /** @var TaxonRepositoryInterface&MockObject $taxonRepository */
        $taxonRepository = $this->createMock(TaxonRepositoryInterface::class);

        $this->resourceRepositoryRegistry->method('get')->with('sylius.taxon')->willReturn($taxonRepository);
        $taxonRepository->method('findOneBy')->with(['code' => 'mug'])->willReturn($taxon);
        $this->productTaxonRepository->method('findOneBy')->with(['product' => $product, 'taxon' => $taxon])->willReturn(null);

        $productTaxon = $this->createMock(ProductTaxonInterface::class);

        $this->productTaxonFactory->method('createNew')->willReturn($productTaxon);

        $form = $this->factory->create(ProductTaxonAutocompleteChoiceType::class, null, [
            'label' => 'sylius.form.product.taxons',
            'product' => $product,
            'multiple' => false,
        ]);

        $form->submit('mug');
        $this->assertEquals($productTaxon, $form->getData());
    }

    #[Test]
    public function it_returns_existing_product_taxon_based_on_given_product_and_passed_taxon_code(): void
    {
        $taxon = $this->createMock(TaxonInterface::class);
        $product = $this->createMock(ProductInterface::class);
        $productTaxon = $this->createMock(ProductTaxonInterface::class);

        /** @var TaxonRepositoryInterface&MockObject $taxonRepository */
        $taxonRepository = $this->createMock(TaxonRepositoryInterface::class);

        $this->resourceRepositoryRegistry->method('get')->with('sylius.taxon')->willReturn($taxonRepository);
        $taxonRepository->method('findOneBy')->with(['code' => 'mug'])->willReturn($taxon);
        $this->productTaxonRepository->method('findOneBy')->with(['product' => $product, 'taxon' => $taxon])->willReturn($productTaxon);

        $form = $this->factory->create(ProductTaxonAutocompleteChoiceType::class, null, [
            'label' => 'sylius.form.product.taxons',
            'product' => $product,
            'multiple' => false,
        ]);

        $form->submit('mug');
        $this->assertEquals($productTaxon, $form->getData());
    }
}
