<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Tests\DependencyInjection;

use Doctrine\Bundle\MigrationsBundle\DependencyInjection\DoctrineMigrationsExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\DefinitionHasTagConstraint;
use Sylius\Bundle\CoreBundle\DependencyInjection\SyliusCoreExtension;
use SyliusLabs\DoctrineMigrationsExtraBundle\DependencyInjection\SyliusLabsDoctrineMigrationsExtraExtension;

final class SyliusCoreExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container->setParameter('kernel.secret', 'test_secret');
    }

    /** @test */
    /** @test */
    public function it_brings_back_previous_order_processing_priorities(): void
    {
        $this->container->setParameter('kernel.environment', 'dev');

        $this->load(['process_shipments_before_recalculating_prices' => true]);

        $this->assertThat(
            $this->container->findDefinition('sylius.order_processing.order_prices_recalculator'),
            new DefinitionHasTagConstraint('sylius.order_processor', ['priority' => 40])
        );

        $this->assertThat(
            $this->container->findDefinition('sylius.order_processing.order_prices_recalculator'),
            $this->logicalNot(new DefinitionHasTagConstraint('sylius.order_processor', ['priority' => 50]))
        );

        $this->assertThat(
            $this->container->findDefinition('sylius.order_processing.order_shipment_processor'),
            new DefinitionHasTagConstraint('sylius.order_processor', ['priority' => 50])
        );

        $this->assertThat(
            $this->container->findDefinition('sylius.order_processing.order_shipment_processor'),
            $this->logicalNot(new DefinitionHasTagConstraint('sylius.order_processor', ['priority' => 40]))
        );
    }

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
    public function it_does_not_load_telemetry_services_when_disabled_via_config(): void
    {
        $this->container->setParameter('kernel.environment', 'dev');

        $this->load(['telemetry' => ['enabled' => false]]);

        $this->assertContainerBuilderHasParameter('sylius_core.telemetry.enabled', false);
        $this->assertContainerBuilderNotHasService('sylius.telemetry.listener');
    }

    /**
     * @test
     * @dataProvider telemetryDisabledEnvValueProvider
     */
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

    public function telemetryDisabledEnvValueProvider(): iterable
    {
        yield 'zero' => ['0'];
        yield 'false' => ['false'];
    }

    /** @test */
    public function it_does_not_load_telemetry_services_in_dev_environment(): void
    {
        $this->container->setParameter('kernel.environment', 'dev');

        $this->load();

        $this->assertContainerBuilderNotHasService('sylius.telemetry.listener');
    }

    /** @test */
    public function it_does_not_load_telemetry_services_in_test_environment(): void
    {
        $this->container->setParameter('kernel.environment', 'test');

        $this->load();

        $this->assertContainerBuilderNotHasService('sylius.telemetry.listener');
    }

    /** @test */
    public function it_loads_telemetry_services_in_prod_environment(): void
    {
        $this->container->setParameter('kernel.environment', 'prod');

        $this->load();

        $this->assertContainerBuilderHasService('sylius.telemetry.listener');
    }

    /** @test */
    public function it_loads_telemetry_services_in_staging_environment(): void
    {
        $this->container->setParameter('kernel.environment', 'staging');

        $this->load();

        $this->assertContainerBuilderHasService('sylius.telemetry.listener');
    }

    /**
     * @test
     * @dataProvider telemetryEnvironmentProvider
     */
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

    /** @test */
    public function it_loads_default_granular_telemetry_parameters(): void
    {
        $this->container->setParameter('kernel.environment', 'prod');

        $this->load();

        $this->assertContainerBuilderHasParameter('sylius_core.telemetry.business', true);
        $this->assertContainerBuilderHasParameter('sylius_core.telemetry.technical', true);
        $this->assertContainerBuilderHasParameter('sylius_core.telemetry.plugins', true);
    }

    /** @test */
    public function it_disables_business_telemetry_via_config(): void
    {
        $this->container->setParameter('kernel.environment', 'prod');

        $this->load(['telemetry' => ['business' => false]]);

        $this->assertContainerBuilderHasParameter('sylius_core.telemetry.business', false);
        $this->assertContainerBuilderHasParameter('sylius_core.telemetry.technical', true);
        $this->assertContainerBuilderHasParameter('sylius_core.telemetry.plugins', true);
    }

    /** @test */
    public function it_disables_technical_telemetry_via_config(): void
    {
        $this->container->setParameter('kernel.environment', 'prod');

        $this->load(['telemetry' => ['technical' => false]]);

        $this->assertContainerBuilderHasParameter('sylius_core.telemetry.business', true);
        $this->assertContainerBuilderHasParameter('sylius_core.telemetry.technical', false);
        $this->assertContainerBuilderHasParameter('sylius_core.telemetry.plugins', true);
    }

    /** @test */
    public function it_disables_plugins_telemetry_via_config(): void
    {
        $this->container->setParameter('kernel.environment', 'prod');

        $this->load(['telemetry' => ['plugins' => false]]);

        $this->assertContainerBuilderHasParameter('sylius_core.telemetry.business', true);
        $this->assertContainerBuilderHasParameter('sylius_core.telemetry.technical', true);
        $this->assertContainerBuilderHasParameter('sylius_core.telemetry.plugins', false);
    }

    /**
     * @test
     * @dataProvider granularTelemetryEnvProvider
     */
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

    public function granularTelemetryEnvProvider(): iterable
    {
        yield 'business with false' => ['SYLIUS_TELEMETRY_BUSINESS', 'sylius_core.telemetry.business', 'false'];
        yield 'business with 0' => ['SYLIUS_TELEMETRY_BUSINESS', 'sylius_core.telemetry.business', '0'];
        yield 'technical with false' => ['SYLIUS_TELEMETRY_TECHNICAL', 'sylius_core.telemetry.technical', 'false'];
        yield 'technical with 0' => ['SYLIUS_TELEMETRY_TECHNICAL', 'sylius_core.telemetry.technical', '0'];
        yield 'plugins with false' => ['SYLIUS_TELEMETRY_PLUGINS', 'sylius_core.telemetry.plugins', 'false'];
        yield 'plugins with 0' => ['SYLIUS_TELEMETRY_PLUGINS', 'sylius_core.telemetry.plugins', '0'];
    }

    /** @test */
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

    public function telemetryEnvironmentProvider(): iterable
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
            $doctrineMigrationsExtensionConfig[0]['migrations_paths']['Sylius\Bundle\CoreBundle\Migrations']
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
            $syliusLabsDoctrineMigrationsExtraExtensionConfig[0]['migrations']['Sylius\Bundle\CoreBundle\Migrations']
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
