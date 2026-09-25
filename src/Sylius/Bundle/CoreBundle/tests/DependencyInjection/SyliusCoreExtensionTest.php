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

namespace Tests\Sylius\Bundle\CoreBundle\DependencyInjection;

use Doctrine\Bundle\MigrationsBundle\DependencyInjection\DoctrineMigrationsExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
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
use Sylius\Component\Core\Filesystem\Adapter\FilesystemAdapterInterface;
use SyliusLabs\DoctrineMigrationsExtraBundle\DependencyInjection\SyliusLabsDoctrineMigrationsExtraExtension;
use Symfony\Component\DependencyInjection\Definition;
use Tests\Sylius\Bundle\CoreBundle\Stub\CatalogPromotionApplicatorCriteriaStub;
use Tests\Sylius\Bundle\CoreBundle\Stub\CatalogPromotionPriceCalculatorStub;
use Tests\Sylius\Bundle\CoreBundle\Stub\EntityObserverStub;
use Tests\Sylius\Bundle\CoreBundle\Stub\OrderItemsTaxesApplicatorStub;
use Tests\Sylius\Bundle\CoreBundle\Stub\OrderItemUnitsTaxesApplicatorStub;
use Tests\Sylius\Bundle\CoreBundle\Stub\OrdersTotalsProviderStub;
use Tests\Sylius\Bundle\CoreBundle\Stub\ProductVariantMapProviderStub;
use Tests\Sylius\Bundle\CoreBundle\Stub\TaxCalculationStrategyStub;
use Tests\Sylius\Bundle\CoreBundle\Stub\UriBasedSectionResolverStub;

final class SyliusCoreExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container->setParameter('kernel.secret', 'test_secret');
    }

    #[Test]
    public function it_autoconfigures_prepending_doctrine_migrations_with_proper_migrations_path_for_test_env(): void
    {
        $this->testPrependingDoctrineMigrations('test');
    }

    #[Test]
    public function it_autoconfigures_prepending_doctrine_migrations_with_proper_migrations_path_for_test_cached_env(): void
    {
        $this->testPrependingDoctrineMigrations('test_cached');
    }

    #[Test]
    public function it_autoconfigures_prepending_doctrine_migrations_with_proper_migrations_path_for_dev_env(): void
    {
        $this->testPrependingDoctrineMigrations('dev');
    }

    #[Test]
    public function it_does_not_autoconfigure_prepending_doctrine_migrations_if_it_is_disabled_for_test_env(): void
    {
        $this->testNotPrependingDoctrineMigrations('test');
    }

    #[Test]
    public function it_does_not_autoconfigure_prepending_doctrine_migrations_if_it_is_disabled_for_test_cached_env(): void
    {
        $this->testNotPrependingDoctrineMigrations('test_cached');
    }

    #[Test]
    public function it_does_not_autoconfigure_prepending_doctrine_migrations_if_it_is_disabled_for_dev_env(): void
    {
        $this->testNotPrependingDoctrineMigrations('dev');
    }

    #[Test]
    public function it_loads_default_order_by_identifier_parameter_value_properly(): void
    {
        $this->container->setParameter('kernel.environment', 'dev');

        $this->load();

        $this->assertContainerBuilderHasParameter('sylius_core.order_by_identifier', false);
    }

    #[Test]
    public function it_loads_order_by_identifier_parameter_value_properly(): void
    {
        $this->container->setParameter('kernel.environment', 'dev');

        $this->load(['order_by_identifier' => true]);
        $this->assertContainerBuilderHasParameter('sylius_core.order_by_identifier', true);

        $this->load(['order_by_identifier' => false]);
        $this->assertContainerBuilderHasParameter('sylius_core.order_by_identifier', false);
    }

    #[Test]
    public function it_loads_batch_size_parameter_value_properly(): void
    {
        $this->container->setParameter('kernel.environment', 'dev');

        $this->load(['catalog_promotions' => ['batch_size' => 200]]);

        $this->assertContainerBuilderHasParameter('sylius_core.catalog_promotions.batch_size', 200);
    }

    #[Test]
    public function it_loads_max_int_value_properly(): void
    {
        $this->container->setParameter('kernel.environment', 'dev');

        $this->load(['max_int_value' => 200]);

        $this->assertContainerBuilderHasParameter('sylius_core.max_int_value', 200);
    }

    #[Test]
    public function it_loads_default_batch_size_properly(): void
    {
        $this->container->setParameter('kernel.environment', 'dev');

        $this->load();

        $this->assertContainerBuilderHasParameter('sylius_core.catalog_promotions.batch_size', 100);
    }

    #[Test]
    public function it_aliases_default_filesystem_adapter_properly(): void
    {
        $this->container->setParameter('kernel.environment', 'dev');

        $this->load();

        $this->assertContainerBuilderHasAlias('sylius.adapter.filesystem.default', 'sylius.adapter.filesystem.flysystem');
        $this->assertContainerBuilderHasAlias(FilesystemAdapterInterface::class, 'sylius.adapter.filesystem.default');
    }

    #[Test]
    public function it_aliases_flysystem_filesystem_adapter_properly(): void
    {
        $this->container->setParameter('kernel.environment', 'dev');

        $this->load(['filesystem' => ['adapter' => 'flysystem']]);

        $this->assertContainerBuilderHasAlias('sylius.adapter.filesystem.default', 'sylius.adapter.filesystem.flysystem');
        $this->assertContainerBuilderHasAlias(FilesystemAdapterInterface::class, 'sylius.adapter.filesystem.default');
    }

    #[Test]
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

    #[Test]
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
            ['type' => 'custom', 'priority' => 9],
        );
    }

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
    public function it_loads_checkout_payment_allowed_states_configuration_properly(): void
    {
        $this->container->setParameter('kernel.environment', 'dev');

        $this->load(['checkout' => ['payment' => ['allowed_states' => ['new', 'test']]]]);

        $this->assertContainerBuilderHasParameter('sylius_core.checkout.payment.allowed_states', ['new', 'test']);
    }

    #[Test]
    public function it_does_not_load_telemetry_services_when_disabled_via_config(): void
    {
        $this->container->setParameter('kernel.environment', 'dev');

        $this->load(['telemetry' => ['enabled' => false]]);

        $this->assertContainerBuilderHasParameter('sylius_core.telemetry.enabled', false);
        $this->assertContainerBuilderNotHasService('sylius.telemetry.listener');
    }

    #[Test]
    #[DataProvider('telemetryDisabledEnvValueProvider')]
    public function it_does_not_load_telemetry_services_when_disabled_via_env_variable(string $envValue): void
    {
        $this->container->setParameter('kernel.environment', 'prod');

        $previousEnvValue = getenv('SYLIUS_TELEMETRY_ENABLED');
        putenv('SYLIUS_TELEMETRY_ENABLED=' . $envValue);

        try {
            $this->load();

            $this->assertContainerBuilderNotHasService('sylius.telemetry.listener');
        } finally {
            if ($previousEnvValue === false) {
                putenv('SYLIUS_TELEMETRY_ENABLED');
            } else {
                putenv('SYLIUS_TELEMETRY_ENABLED=' . $previousEnvValue);
            }
        }
    }

    public static function telemetryDisabledEnvValueProvider(): iterable
    {
        yield 'zero' => ['0'];
        yield 'false' => ['false'];
    }

    #[Test]
    public function it_does_not_load_telemetry_services_in_dev_environment(): void
    {
        $this->container->setParameter('kernel.environment', 'dev');

        $this->load();

        $this->assertContainerBuilderNotHasService('sylius.telemetry.listener');
    }

    #[Test]
    public function it_does_not_load_telemetry_services_in_test_environment(): void
    {
        $this->container->setParameter('kernel.environment', 'test');

        $this->load();

        $this->assertContainerBuilderNotHasService('sylius.telemetry.listener');
    }

    #[Test]
    public function it_loads_telemetry_services_in_prod_environment(): void
    {
        $this->container->setParameter('kernel.environment', 'prod');

        $this->load();

        $this->assertContainerBuilderHasService('sylius.telemetry.listener');
    }

    #[Test]
    public function it_loads_telemetry_services_in_staging_environment(): void
    {
        $this->container->setParameter('kernel.environment', 'staging');

        $this->load();

        $this->assertContainerBuilderHasService('sylius.telemetry.listener');
    }

    #[Test]
    #[DataProvider('telemetryEnvironmentProvider')]
    public function it_loads_or_skips_telemetry_services_based_on_environment(string $env, bool $shouldLoad): void
    {
        $this->container->setParameter('kernel.environment', $env);

        $this->load();

        if ($shouldLoad) {
            $this->assertContainerBuilderHasService('sylius.telemetry.listener');
        } else {
            $this->assertContainerBuilderNotHasService('sylius.telemetry.listener');
        }
    }

    #[Test]
    public function it_loads_default_granular_telemetry_parameters(): void
    {
        $this->container->setParameter('kernel.environment', 'prod');

        $this->load();

        $this->assertContainerBuilderHasParameter('sylius_core.telemetry.business', true);
        $this->assertContainerBuilderHasParameter('sylius_core.telemetry.technical', true);
        $this->assertContainerBuilderHasParameter('sylius_core.telemetry.plugins', true);
    }

    #[Test]
    public function it_disables_business_telemetry_via_config(): void
    {
        $this->container->setParameter('kernel.environment', 'prod');

        $this->load(['telemetry' => ['business' => false]]);

        $this->assertContainerBuilderHasParameter('sylius_core.telemetry.business', false);
        $this->assertContainerBuilderHasParameter('sylius_core.telemetry.technical', true);
        $this->assertContainerBuilderHasParameter('sylius_core.telemetry.plugins', true);
    }

    #[Test]
    public function it_disables_technical_telemetry_via_config(): void
    {
        $this->container->setParameter('kernel.environment', 'prod');

        $this->load(['telemetry' => ['technical' => false]]);

        $this->assertContainerBuilderHasParameter('sylius_core.telemetry.business', true);
        $this->assertContainerBuilderHasParameter('sylius_core.telemetry.technical', false);
        $this->assertContainerBuilderHasParameter('sylius_core.telemetry.plugins', true);
    }

    #[Test]
    public function it_disables_plugins_telemetry_via_config(): void
    {
        $this->container->setParameter('kernel.environment', 'prod');

        $this->load(['telemetry' => ['plugins' => false]]);

        $this->assertContainerBuilderHasParameter('sylius_core.telemetry.business', true);
        $this->assertContainerBuilderHasParameter('sylius_core.telemetry.technical', true);
        $this->assertContainerBuilderHasParameter('sylius_core.telemetry.plugins', false);
    }

    #[Test]
    #[DataProvider('granularTelemetryEnvProvider')]
    public function it_disables_granular_telemetry_via_env_variable(string $envVar, string $paramName, string $envValue): void
    {
        $this->container->setParameter('kernel.environment', 'prod');

        $previousEnvValue = getenv($envVar);
        putenv($envVar . '=' . $envValue);

        try {
            $this->load();

            $this->assertContainerBuilderHasParameter($paramName, false);
        } finally {
            if ($previousEnvValue === false) {
                putenv($envVar);
            } else {
                putenv($envVar . '=' . $previousEnvValue);
            }
        }
    }

    public static function granularTelemetryEnvProvider(): iterable
    {
        yield 'business with false' => ['SYLIUS_TELEMETRY_BUSINESS', 'sylius_core.telemetry.business', 'false'];
        yield 'business with 0' => ['SYLIUS_TELEMETRY_BUSINESS', 'sylius_core.telemetry.business', '0'];
        yield 'technical with false' => ['SYLIUS_TELEMETRY_TECHNICAL', 'sylius_core.telemetry.technical', 'false'];
        yield 'technical with 0' => ['SYLIUS_TELEMETRY_TECHNICAL', 'sylius_core.telemetry.technical', '0'];
        yield 'plugins with false' => ['SYLIUS_TELEMETRY_PLUGINS', 'sylius_core.telemetry.plugins', 'false'];
        yield 'plugins with 0' => ['SYLIUS_TELEMETRY_PLUGINS', 'sylius_core.telemetry.plugins', '0'];
    }

    #[Test]
    public function it_env_variable_overrides_config_for_granular_telemetry(): void
    {
        $this->container->setParameter('kernel.environment', 'prod');

        $previousEnvValue = getenv('SYLIUS_TELEMETRY_BUSINESS');
        putenv('SYLIUS_TELEMETRY_BUSINESS=false');

        try {
            $this->load(['telemetry' => ['business' => true]]);

            $this->assertContainerBuilderHasParameter('sylius_core.telemetry.business', false);
        } finally {
            if ($previousEnvValue === false) {
                putenv('SYLIUS_TELEMETRY_BUSINESS');
            } else {
                putenv('SYLIUS_TELEMETRY_BUSINESS=' . $previousEnvValue);
            }
        }
    }

    public static function telemetryEnvironmentProvider(): iterable
    {
        yield 'dev' => ['dev', false];
        yield 'dev_local' => ['dev_local', false];
        yield 'development' => ['development', false];
        yield 'test' => ['test', false];
        yield 'test_cached' => ['test_cached', false];
        yield 'testing' => ['testing', false];

        yield 'prod' => ['prod', true];
        yield 'production' => ['production', true];
        yield 'staging' => ['staging', true];
        yield 'qa' => ['qa', true];
        yield 'uat' => ['uat', true];
        yield 'demo' => ['demo', true];
        yield 'preview' => ['preview', true];
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
            $doctrineMigrationsExtensionConfig[0]['migrations_paths']['Sylius\Bundle\CoreBundle\Migrations'],
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
            $syliusLabsDoctrineMigrationsExtraExtensionConfig[0]['migrations']['Sylius\Bundle\CoreBundle\Migrations'],
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
