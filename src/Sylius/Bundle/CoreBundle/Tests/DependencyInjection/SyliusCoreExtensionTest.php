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

namespace Sylius\Bundle\CoreBundle\Tests\DependencyInjection;

use Doctrine\Bundle\MigrationsBundle\DependencyInjection\DoctrineMigrationsExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\CoreBundle\Attribute\AsCatalogPromotionApplicatorCriteria;
use Sylius\Bundle\CoreBundle\Attribute\AsCatalogPromotionPriceCalculator;
use Sylius\Bundle\CoreBundle\Attribute\AsEntityObserver;
use Sylius\Bundle\CoreBundle\Attribute\AsOrderItemsTaxesApplicator;
use Sylius\Bundle\CoreBundle\Attribute\AsOrderItemUnitsTaxesApplicator;
use Sylius\Bundle\CoreBundle\Attribute\AsOrdersTotalsProvider;
use Sylius\Bundle\CoreBundle\Attribute\AsProductVariantMapProvider;
use Sylius\Bundle\CoreBundle\Attribute\AsTaxCalculationStrategy;
use Sylius\Bundle\CoreBundle\Attribute\AsUriBasedSectionResolver;
use Sylius\Bundle\CoreBundle\DependencyInjection\SyliusCoreExtension;
use Sylius\Bundle\CoreBundle\Tests\Stub\CatalogPromotionApplicatorCriteriaStub;
use Sylius\Bundle\CoreBundle\Tests\Stub\CatalogPromotionPriceCalculatorStub;
use Sylius\Bundle\CoreBundle\Tests\Stub\EntityObserverStub;
use Sylius\Bundle\CoreBundle\Tests\Stub\OrderItemsTaxesApplicatorStub;
use Sylius\Bundle\CoreBundle\Tests\Stub\OrderItemUnitsTaxesApplicatorStub;
use Sylius\Bundle\CoreBundle\Tests\Stub\OrdersTotalsProviderStub;
use Sylius\Bundle\CoreBundle\Tests\Stub\ProductVariantMapProviderStub;
use Sylius\Bundle\CoreBundle\Tests\Stub\TaxCalculationStrategyStub;
use Sylius\Bundle\CoreBundle\Tests\Stub\UriBasedSectionResolverStub;
use Sylius\Bundle\OrderBundle\DependencyInjection\SyliusOrderExtension;
use Sylius\Component\Core\Filesystem\Adapter\FilesystemAdapterInterface;
use Sylius\Component\Core\Filesystem\Adapter\FlysystemFilesystemAdapter;
use SyliusLabs\DoctrineMigrationsExtraBundle\DependencyInjection\SyliusLabsDoctrineMigrationsExtraExtension;
use Symfony\Component\DependencyInjection\Definition;

final class SyliusCoreExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_autoconfigures_prepending_doctrine_migrations_with_proper_migrations_path_for_test_env(): void
    {
        $this->testPrependingDoctrineMigrations('test');
    }

    /** @test */
    public function it_autoconfigures_prepending_doctrine_migrations_with_proper_migrations_path_for_test_cached_env(): void
    {
        $this->testPrependingDoctrineMigrations('test_cached');
    }

    /** @test */
    public function it_autoconfigures_prepending_doctrine_migrations_with_proper_migrations_path_for_dev_env(): void
    {
        $this->testPrependingDoctrineMigrations('dev');
    }

    /**
     * @test
     *
     * @dataProvider provideAutoconfigureWithAttributesData
     */
    public function it_prepends_sylius_order_bundle_configuration_with_proper_values(bool $value, bool $orderBundleValue): void
    {
        $this->container->setParameter('kernel.environment', 'dev');
        $this->container->registerExtension(new SyliusOrderExtension());
        $this->container->loadFromExtension('sylius_core', [
            'autoconfigure_with_attributes' => $value,
        ]);
        $this->container->loadFromExtension('sylius_order', [
            'autoconfigure_with_attributes' => $orderBundleValue,
        ]);

        $this->load();

        $syliusOrderConfig = $this->container->getExtensionConfig('sylius_order');
        $this->assertEquals($value, $syliusOrderConfig[0]['autoconfigure_with_attributes']);
    }

    public static function provideAutoconfigureWithAttributesData(): iterable
    {
        yield [true, false];
        yield [false, true];
    }

    /** @test */
    public function it_does_not_autoconfigure_prepending_doctrine_migrations_if_it_is_disabled_for_test_env(): void
    {
        $this->testNotPrependingDoctrineMigrations('test');
    }

    /** @test */
    public function it_does_not_autoconfigure_prepending_doctrine_migrations_if_it_is_disabled_for_test_cached_env(): void
    {
        $this->testNotPrependingDoctrineMigrations('test_cached');
    }

    /** @test */
    public function it_does_not_autoconfigure_prepending_doctrine_migrations_if_it_is_disabled_for_dev_env(): void
    {
        $this->testNotPrependingDoctrineMigrations('dev');
    }

    /** @test */
    public function it_loads_default_order_by_identifier_parameter_value_properly(): void
    {
        $this->container->setParameter('kernel.environment', 'dev');

        $this->load();

        $this->assertContainerBuilderHasParameter('sylius_core.order_by_identifier', true);
    }

    /** @test */
    public function it_loads_order_by_identifier_parameter_value_properly(): void
    {
        $this->container->setParameter('kernel.environment', 'dev');

        $this->load(['order_by_identifier' => true]);
        $this->assertContainerBuilderHasParameter('sylius_core.order_by_identifier', true);

        $this->load(['order_by_identifier' => false]);
        $this->assertContainerBuilderHasParameter('sylius_core.order_by_identifier', false);
    }

    /** @test */
    public function it_loads_batch_size_parameter_value_properly(): void
    {
        $this->container->setParameter('kernel.environment', 'dev');

        $this->load(['catalog_promotions' => ['batch_size' => 200]]);

        $this->assertContainerBuilderHasParameter('sylius_core.catalog_promotions.batch_size', 200);
    }

    /** @test */
    public function it_loads_max_int_value_properly(): void
    {
        $this->container->setParameter('kernel.environment', 'dev');

        $this->load(['max_int_value' => 200]);

        $this->assertContainerBuilderHasParameter('sylius_core.max_int_value', 200);
    }

    /** @test */
    public function it_loads_default_batch_size_properly(): void
    {
        $this->container->setParameter('kernel.environment', 'dev');

        $this->load();

        $this->assertContainerBuilderHasParameter('sylius_core.catalog_promotions.batch_size', 100);
    }

    /** @test */
    public function it_aliases_default_filesystem_adapter_properly(): void
    {
        $this->container->setParameter('kernel.environment', 'dev');

        $this->load();

        $this->assertContainerBuilderHasAlias(FilesystemAdapterInterface::class, FlysystemFilesystemAdapter::class);
    }

    /** @test */
    public function it_aliases_flysystem_filesystem_adapter_properly(): void
    {
        $this->container->setParameter('kernel.environment', 'dev');

        $this->load(['filesystem' => ['adapter' => 'flysystem']]);

        $this->assertContainerBuilderHasAlias(FilesystemAdapterInterface::class, FlysystemFilesystemAdapter::class);
    }

    /** @test */
    public function it_autoconfigures_catalog_promotion_applicator_criteria_with_attribute(): void
    {
        $this->container->setParameter('kernel.environment', 'prod');
        $this->container->setDefinition(
            'acme.catalog_promotion_applicator_criteria_with_attribute',
            (new Definition())
                ->setClass(CatalogPromotionApplicatorCriteriaStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.catalog_promotion_applicator_criteria_with_attribute',
            AsCatalogPromotionApplicatorCriteria::SERVICE_TAG,
            ['priority' => 20],
        );
    }

    /** @test */
    public function it_autoconfigures_catalog_promotion_price_calculator_with_attribute(): void
    {
        $this->container->setParameter('kernel.environment', 'prod');
        $this->container->setDefinition(
            'acme.catalog_promotion_price_calculator_with_attribute',
            (new Definition())
                ->setClass(CatalogPromotionPriceCalculatorStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.catalog_promotion_price_calculator_with_attribute',
            AsCatalogPromotionPriceCalculator::SERVICE_TAG,
            ['priority' => 9],
        );
    }

    /** @test */
    public function it_autoconfigures_entity_observer_with_attribute(): void
    {
        $this->container->setParameter('kernel.environment', 'prod');
        $this->container->setDefinition(
            'acme.entity_observer_with_attribute',
            (new Definition())
                ->setClass(EntityObserverStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.entity_observer_with_attribute',
            AsEntityObserver::SERVICE_TAG,
            ['priority' => 5],
        );
    }

    /** @test */
    public function it_autoconfigures_order_items_taxes_applicator_with_attribute(): void
    {
        $this->container->setParameter('kernel.environment', 'prod');
        $this->container->setDefinition(
            'acme.order_items_taxes_applicator_with_attribute',
            (new Definition())
                ->setClass(OrderItemsTaxesApplicatorStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.order_items_taxes_applicator_with_attribute',
            AsOrderItemsTaxesApplicator::SERVICE_TAG,
            ['priority' => 15],
        );
    }

    /** @test */
    public function it_autoconfigures_order_item_units_taxes_applicator_with_attribute(): void
    {
        $this->container->setParameter('kernel.environment', 'prod');
        $this->container->setDefinition(
            'acme.order_item_units_taxes_applicator_with_attribute',
            (new Definition())
                ->setClass(OrderItemUnitsTaxesApplicatorStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.order_item_units_taxes_applicator_with_attribute',
            AsOrderItemUnitsTaxesApplicator::SERVICE_TAG,
            ['priority' => 15],
        );
    }

    /** @test */
    public function it_autoconfigures_product_variant_map_provider_with_attribute(): void
    {
        $this->container->setParameter('kernel.environment', 'prod');
        $this->container->setDefinition(
            'acme.product_variant_map_provider_with_attribute',
            (new Definition())
                ->setClass(ProductVariantMapProviderStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.product_variant_map_provider_with_attribute',
            AsProductVariantMapProvider::SERVICE_TAG,
            ['priority' => 4],
        );
    }

    /** @test */
    public function it_autoconfigures_tax_calculation_strategy_with_attribute(): void
    {
        $this->container->setParameter('kernel.environment', 'prod');
        $this->container->setDefinition(
            'acme.tax_calculation_strategy_with_attribute',
            (new Definition())
                ->setClass(TaxCalculationStrategyStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.tax_calculation_strategy_with_attribute',
            AsTaxCalculationStrategy::SERVICE_TAG,
            [
                'type' => 'test',
                'label' => 'Test',
                'priority' => 15,
            ],
        );
    }

    /** @test */
    public function it_autoconfigures_uri_based_section_resolver_with_attribute(): void
    {
        $this->container->setParameter('kernel.environment', 'prod');
        $this->container->setDefinition(
            'acme.uri_based_section_resolver_with_attribute',
            (new Definition())
                ->setClass(UriBasedSectionResolverStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.uri_based_section_resolver_with_attribute',
            AsUriBasedSectionResolver::SERVICE_TAG,
            ['priority' => 20],
        );
    }

    /** @test */
    public function it_autoconfigures_orders_totals_provider_with_attribute(): void
    {
        $this->container->setParameter('kernel.environment', 'prod');
        $this->container->setDefinition(
            'acme.orders_totals_provider',
            (new Definition())
                ->setClass(OrdersTotalsProviderStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.orders_totals_provider',
            AsOrdersTotalsProvider::SERVICE_TAG,
            ['type' => 'stub'],
        );
    }

    /** @test */
    public function it_sets_the_orders_statistics_intervals_map_parameter(): void
    {
        $this->container->setParameter('kernel.environment', 'prod');
        $this->load([
            'orders_statistics' => [
                'intervals_map' => [
                    'day' => [
                        'interval' => 'P1D',
                        'period_format' => 'YYYY-MM-DD',
                    ],
                    'month' => [
                        'interval' => 'P1M',
                        'period_format' => 'YYYY-MM',
                    ],
                ],
            ],
        ]);

        $this->assertContainerBuilderHasParameter('sylius_core.orders_statistics.intervals_map', [
            'day' => [
                'interval' => 'P1D',
                'period_format' => 'YYYY-MM-DD',
            ],
            'month' => [
                'interval' => 'P1M',
                'period_format' => 'YYYY-MM',
            ],
        ]);
    }

    protected function getContainerExtensions(): array
    {
        return [new SyliusCoreExtension()];
    }

    private function testPrependingDoctrineMigrations(string $env): void
    {
        $this->configureContainer($env);

        $this->load();

        $doctrineMigrationsExtensionConfig = $this->container->getExtensionConfig('doctrine_migrations');

        $this->assertTrue(isset(
            $doctrineMigrationsExtensionConfig[0]['migrations_paths']['Sylius\Bundle\CoreBundle\Migrations']
        ));
        $this->assertSame(
            '@SyliusCoreBundle/Migrations',
            $doctrineMigrationsExtensionConfig[0]['migrations_paths']['Sylius\Bundle\CoreBundle\Migrations'],
        );

        $syliusLabsDoctrineMigrationsExtraExtensionConfig = $this
            ->container
            ->getExtensionConfig('sylius_labs_doctrine_migrations_extra')
        ;

        $this->assertTrue(isset(
            $syliusLabsDoctrineMigrationsExtraExtensionConfig[0]['migrations']['Sylius\Bundle\CoreBundle\Migrations']
        ));
        $this->assertSame(
            [],
            $syliusLabsDoctrineMigrationsExtraExtensionConfig[0]['migrations']['Sylius\Bundle\CoreBundle\Migrations'],
        );
    }

    private function testNotPrependingDoctrineMigrations(string $env): void
    {
        $this->configureContainer($env);

        $this->container->setParameter('sylius_core.prepend_doctrine_migrations', false);

        $this->load();

        $doctrineMigrationsExtensionConfig = $this->container->getExtensionConfig('doctrine_migrations');

        $this->assertEmpty($doctrineMigrationsExtensionConfig);

        $syliusLabsDoctrineMigrationsExtraExtensionConfig = $this
            ->container
            ->getExtensionConfig('sylius_labs_doctrine_migrations_extra')
        ;

        $this->assertEmpty($syliusLabsDoctrineMigrationsExtraExtensionConfig);
    }

    private function configureContainer(string $env): void
    {
        $this->container->setParameter('kernel.environment', $env);
        $this->container->setParameter('kernel.debug', true);

        $this->container->registerExtension(new DoctrineMigrationsExtension());
        $this->container->registerExtension(new SyliusLabsDoctrineMigrationsExtraExtension());
    }
}
