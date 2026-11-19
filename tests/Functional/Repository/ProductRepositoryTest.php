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

namespace Sylius\Tests\Functional\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use PHPUnit\Framework\Attributes\Test;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ProductRepositoryTest extends KernelTestCase
{
    /** @var ProductRepositoryInterface<ProductInterface> */
    private ProductRepositoryInterface $productRepository;

    /** @var TaxonRepositoryInterface<TaxonInterface> */
    private TaxonRepositoryInterface $taxonRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $entityManager->clear();

        /** @var ProductRepositoryInterface<ProductInterface> $productRepository */
        $productRepository = self::getContainer()->get('sylius.repository.product');
        $this->productRepository = $productRepository;

        /** @var TaxonRepositoryInterface<TaxonInterface> $taxonRepository */
        $taxonRepository = self::getContainer()->get('sylius.repository.taxon');
        $this->taxonRepository = $taxonRepository;
    }

    #[Test]
    public function it_finds_products_belonging_to_a_taxon(): void
    {
        /** @var TaxonInterface $mugsTaxon */
        $mugsTaxon = $this->taxonRepository->findOneBy(['code' => 'find_by_taxon_mugs']);

        $products = $this->productRepository->findByTaxon($mugsTaxon);

        self::assertCount(2, $products);
        self::assertEqualsCanonicalizing(
            ['FIND_BY_TAXON_MUGS_ONLY', 'FIND_BY_TAXON_MUGS_AND_BOOKS'],
            $this->getProductCodes($products),
        );
    }

    #[Test]
    public function it_does_not_return_products_from_other_taxons(): void
    {
        /** @var TaxonInterface $booksTaxon */
        $booksTaxon = $this->taxonRepository->findOneBy(['code' => 'find_by_taxon_books']);

        $products = $this->productRepository->findByTaxon($booksTaxon);

        self::assertCount(2, $products);
        self::assertEqualsCanonicalizing(
            ['FIND_BY_TAXON_MUGS_AND_BOOKS', 'FIND_BY_TAXON_BOOKS_ONLY'],
            $this->getProductCodes($products),
        );
    }

    #[Test]
    public function it_returns_the_full_product_taxons_collection_not_only_filtered_ones(): void
    {
        /** @var TaxonInterface $mugsTaxon */
        $mugsTaxon = $this->taxonRepository->findOneBy(['code' => 'find_by_taxon_mugs']);

        $products = $this->productRepository->findByTaxon($mugsTaxon);

        $productInBothTaxons = null;
        foreach ($products as $product) {
            if ($product->getCode() === 'FIND_BY_TAXON_MUGS_AND_BOOKS') {
                $productInBothTaxons = $product;

                break;
            }
        }

        self::assertNotNull($productInBothTaxons);
        self::assertCount(
            2,
            $productInBothTaxons->getProductTaxons(),
            'Product assigned to two taxons must expose both in its productTaxons collection, not just the filtered one.',
        );
    }

    /**
     * @param ProductInterface[] $products
     *
     * @return string[]
     */
    private function getProductCodes(array $products): array
    {
        return array_map(
            static fn (ProductInterface $product): string => (string) $product->getCode(),
            $products,
        );
    }

    private function loadFixtures(): void
    {
        /** @var LoaderInterface $fixtureLoader */
        $fixtureLoader = self::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');

        $fixtureLoader->load([
            __DIR__ . '/../../DataFixtures/ORM/resources/product_repository_find_by_taxon.yml',
        ], [], [], PurgeMode::createDeleteMode());
    }
}
