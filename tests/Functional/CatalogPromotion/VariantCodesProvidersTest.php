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

namespace Sylius\Tests\Functional\CatalogPromotion;

use Doctrine\ORM\EntityManagerInterface;
use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Provider\InForProductScopeVariantCodesProvider;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Provider\InForTaxonsScopeVariantCodesProvider;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Provider\VariantCodesWithAppliedPromotionProvider;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class VariantCodesProvidersTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private InForTaxonsScopeVariantCodesProvider $taxonsProvider;

    private InForProductScopeVariantCodesProvider $productsProvider;

    private VariantCodesWithAppliedPromotionProvider $appliedPromotionProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures();

        $container = self::getContainer();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $this->entityManager = $entityManager;

        /** @var class-string $productVariantClass */
        $productVariantClass = $container->getParameter('sylius.model.product_variant.class');
        /** @var class-string $productTaxonClass */
        $productTaxonClass = $container->getParameter('sylius.model.product_taxon.class');
        /** @var class-string $taxonClass */
        $taxonClass = $container->getParameter('sylius.model.taxon.class');
        /** @var class-string $channelPricingClass */
        $channelPricingClass = $container->getParameter('sylius.model.channel_pricing.class');

        $this->taxonsProvider = new InForTaxonsScopeVariantCodesProvider($entityManager, $productVariantClass, $productTaxonClass, $taxonClass);
        $this->productsProvider = new InForProductScopeVariantCodesProvider($entityManager, $productVariantClass);
        $this->appliedPromotionProvider = new VariantCodesWithAppliedPromotionProvider($entityManager, $productVariantClass, $channelPricingClass);
    }

    #[Test]
    public function it_provides_variants_of_a_configured_taxon_and_of_its_descendants(): void
    {
        $codes = $this->taxonsProvider->provide($this->createScope('for_taxons', ['taxons' => ['dogs']]));

        self::assertEqualsCanonicalizing(
            ['VARIANT_DOGS_1', 'VARIANT_DOGS_2', 'VARIANT_PUPPIES', 'VARIANT_DOGS_AND_PUPPIES'],
            $codes,
        );
    }

    #[Test]
    public function it_provides_variants_down_to_the_grandchildren_of_a_configured_taxon(): void
    {
        $codes = $this->taxonsProvider->provide($this->createScope('for_taxons', ['taxons' => ['animals']]));

        self::assertEqualsCanonicalizing(
            [
                'VARIANT_ANIMALS',
                'VARIANT_DOGS_1',
                'VARIANT_DOGS_2',
                'VARIANT_PUPPIES',
                'VARIANT_CATS',
                'VARIANT_DOGS_AND_PUPPIES',
            ],
            $codes,
        );
    }

    #[Test]
    public function it_does_not_provide_variants_of_the_parent_of_a_configured_taxon(): void
    {
        $codes = $this->taxonsProvider->provide($this->createScope('for_taxons', ['taxons' => ['puppies']]));

        self::assertEqualsCanonicalizing(['VARIANT_PUPPIES', 'VARIANT_DOGS_AND_PUPPIES'], $codes);
    }

    #[Test]
    public function it_does_not_provide_variants_of_products_outside_of_the_configured_taxons_tree(): void
    {
        $codes = $this->taxonsProvider->provide($this->createScope('for_taxons', ['taxons' => ['cats']]));

        self::assertSame(['VARIANT_CATS'], $codes);
    }

    #[Test]
    public function it_provides_variants_of_many_configured_taxons_without_duplicates(): void
    {
        $codes = $this->taxonsProvider->provide($this->createScope('for_taxons', ['taxons' => ['dogs', 'puppies', 'trees']]));

        self::assertEqualsCanonicalizing(
            ['VARIANT_DOGS_1', 'VARIANT_DOGS_2', 'VARIANT_PUPPIES', 'VARIANT_DOGS_AND_PUPPIES', 'VARIANT_TREES'],
            $codes,
        );
        self::assertSame($codes, array_values(array_unique($codes)));
    }

    #[Test]
    public function it_provides_no_variants_if_no_taxon_is_configured(): void
    {
        self::assertSame([], $this->taxonsProvider->provide($this->createScope('for_taxons', ['taxons' => []])));
    }

    #[Test]
    public function it_ignores_configured_taxons_that_do_not_exist(): void
    {
        $codes = $this->taxonsProvider->provide($this->createScope('for_taxons', ['taxons' => ['unknown', 'cats', 'also_unknown']]));

        self::assertSame(['VARIANT_CATS'], $codes);
    }

    #[Test]
    public function it_provides_no_variants_if_none_of_the_configured_taxons_exist(): void
    {
        self::assertSame([], $this->taxonsProvider->provide($this->createScope('for_taxons', ['taxons' => ['unknown']])));
    }

    #[Test]
    public function it_throws_an_exception_if_the_scope_does_not_contain_taxons_configuration(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('This rule should have configured taxons');

        $this->taxonsProvider->provide($this->createScope('for_taxons', []));
    }

    #[Test]
    public function it_provides_variants_of_configured_products(): void
    {
        $codes = $this->productsProvider->provide($this->createScope('for_products', ['products' => ['PRODUCT_DOGS', 'PRODUCT_CATS']]));

        self::assertEqualsCanonicalizing(['VARIANT_DOGS_1', 'VARIANT_DOGS_2', 'VARIANT_CATS'], $codes);
    }

    #[Test]
    public function it_does_not_provide_variants_of_products_that_are_not_configured(): void
    {
        $codes = $this->productsProvider->provide($this->createScope('for_products', ['products' => ['PRODUCT_TREES']]));

        self::assertSame(['VARIANT_TREES'], $codes);
    }

    #[Test]
    public function it_provides_no_variants_if_no_product_is_configured(): void
    {
        self::assertSame([], $this->productsProvider->provide($this->createScope('for_products', ['products' => []])));
    }

    #[Test]
    public function it_provides_variants_having_the_promotion_applied_on_any_of_their_channels(): void
    {
        $codes = $this->appliedPromotionProvider->provide($this->findPromotion('applied'));

        // VARIANT_DOGS_1: applied on the web channel only, VARIANT_CATS: on the mobile channel only,
        // VARIANT_DOGS_2: on both channels (must be returned once).
        self::assertEqualsCanonicalizing(['VARIANT_DOGS_1', 'VARIANT_DOGS_2', 'VARIANT_CATS'], $codes);
        self::assertSame($codes, array_values(array_unique($codes)));
    }

    #[Test]
    public function it_does_not_provide_variants_having_only_other_promotions_applied(): void
    {
        self::assertSame(['VARIANT_TREES'], $this->appliedPromotionProvider->provide($this->findPromotion('other')));
    }

    #[Test]
    public function it_provides_no_variants_for_a_promotion_applied_nowhere(): void
    {
        self::assertSame([], $this->appliedPromotionProvider->provide($this->findPromotion('unused')));
    }

    /** @param array<string, array<string>> $configuration */
    private function createScope(string $type, array $configuration): CatalogPromotionScopeInterface
    {
        /** @var class-string<CatalogPromotionScopeInterface> $scopeClass */
        $scopeClass = self::getContainer()->getParameter('sylius.model.catalog_promotion_scope.class');

        $scope = new $scopeClass();
        $scope->setType($type);
        $scope->setConfiguration($configuration);

        return $scope;
    }

    private function findPromotion(string $code): CatalogPromotionInterface
    {
        /** @var class-string<CatalogPromotionInterface> $promotionClass */
        $promotionClass = self::getContainer()->getParameter('sylius.model.catalog_promotion.class');

        $promotion = $this->entityManager->getRepository($promotionClass)->findOneBy(['code' => $code]);
        self::assertNotNull($promotion);

        return $promotion;
    }

    private function loadFixtures(): void
    {
        /** @var LoaderInterface $fixtureLoader */
        $fixtureLoader = self::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');

        $fixtureLoader->load([
            __DIR__ . '/../../DataFixtures/ORM/resources/catalog_promotion_variant_codes.yml',
        ], [], [], PurgeMode::createDeleteMode());
    }
}
