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
use Sylius\Bundle\CoreBundle\SectionResolver\SectionInterface;
use Sylius\Bundle\CoreBundle\SectionResolver\UriBasedSectionResolverInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Attribute\AsTaxCalculationStrategy;
use Sylius\Component\Core\Attribute\AsUriBasedSectionResolver;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Taxation\Strategy\TaxCalculationStrategyInterface;
use SyliusLabs\DoctrineMigrationsExtraBundle\DependencyInjection\SyliusLabsDoctrineMigrationsExtraExtension;
use Symfony\Component\DependencyInjection\Definition;

final class SyliusCoreExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_brings_back_previous_order_processing_priorities(): void
    {
        $this->container->setParameter('kernel.environment', 'dev');

        $this->load(['process_shipments_before_recalculating_prices' => true]);

        $this->assertThat(
            $this->container->findDefinition('sylius.order_processing.order_prices_recalculator'),
            new DefinitionHasTagConstraint('sylius.order_processor', ['priority' => 40]),
        );

        $this->assertThat(
            $this->container->findDefinition('sylius.order_processing.order_prices_recalculator'),
            $this->logicalNot(new DefinitionHasTagConstraint('sylius.order_processor', ['priority' => 50])),
        );

        $this->assertThat(
            $this->container->findDefinition('sylius.order_processing.order_shipment_processor'),
            new DefinitionHasTagConstraint('sylius.order_processor', ['priority' => 50]),
        );

        $this->assertThat(
            $this->container->findDefinition('sylius.order_processing.order_shipment_processor'),
            $this->logicalNot(new DefinitionHasTagConstraint('sylius.order_processor', ['priority' => 40])),
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
    public function it_loads_batch_size_parameter_value_properly(): void
    {
        $this->container->setParameter('kernel.environment', 'dev');

        $this->load(['catalog_promotions' => ['batch_size' => 200]]);

        $this->assertContainerBuilderHasParameter('sylius_core.catalog_promotions.batch_size', 200);
    }

    /** @test */
    public function it_loads_default_batch_size_properly(): void
    {
        $this->container->setParameter('kernel.environment', 'dev');

        $this->load();

        $this->assertContainerBuilderHasParameter('sylius_core.catalog_promotions.batch_size', 100);
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

    /** @test */
    public function it_autoconfigures_tax_calculation_strategy_with_attribute(): void
    {
        $this->container->setParameter('kernel.environment', 'prod');

        $this->container->register(
            'acme.tax_calculation_strategy_autoconfigured',
            DummyTaxCalculationStrategy::class
        )->setAutoconfigured(true);

        $this->container->register(
            'acme.prioritized_tax_calculation_strategy_autoconfigured',
            PrioritizedDummyTaxCalculationStrategy::class
        )->setAutoconfigured(true);

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.tax_calculation_strategy_autoconfigured',
            'sylius.taxation.calculation_strategy',
            [
                'type' => 'dummy',
                'label' => 'dummy',
                'priority' => 0
            ]
        );

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.prioritized_tax_calculation_strategy_autoconfigured',
            'sylius.taxation.calculation_strategy',
            [
                'type' => 'dummy',
                'label' => 'dummy',
                'priority' => 128
            ]
        );
    }

    /** @test */
    public function it_autoconfigures_uri_based_section_resolver_with_attribute(): void
    {
        $this->container->setParameter('kernel.environment', 'prod');

        $this->container->register(
            'acme.uri_based_section_resolver_autoconfigured',
            DummyUriBasedSectionResolver::class
        )->setAutoconfigured(true);

        $this->container->register(
            'acme.prioritized_uri_based_section_resolver_autoconfigured',
            PrioritizedDummyUriBasedSectionResolver::class
        )->setAutoconfigured(true);

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.uri_based_section_resolver_autoconfigured',
            'sylius.uri_based_section_resolver',
            [
                'priority' => 0
            ]
        );

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.prioritized_uri_based_section_resolver_autoconfigured',
            'sylius.uri_based_section_resolver',
            [
                'priority' => 128
            ]
        );
    }

    /** @test */
    public function it_autoconfigures_uri_based_section_resolver(): void
    {
        $this->container->setParameter('kernel.environment', 'prod');

        $this->container->setDefinition(
            'acme.uri_based_section_resolver_autoconfigured',
            (new Definition())
                ->setClass(self::getMockClass(UriBasedSectionResolverInterface::class))
                ->setAutoconfigured(true)
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.uri_based_section_resolver_autoconfigured',
            'sylius.uri_based_section_resolver'
        );
    }

    private function configureContainer(string $env): void
    {
        $this->container->setParameter('kernel.environment', $env);
        $this->container->setParameter('kernel.debug', true);

        $this->container->registerExtension(new DoctrineMigrationsExtension());
        $this->container->registerExtension(new SyliusLabsDoctrineMigrationsExtraExtension());
    }
}


#[AsTaxCalculationStrategy(type: 'dummy', label: 'dummy')]
class DummyTaxCalculationStrategy implements TaxCalculationStrategyInterface
{
    public function applyTaxes(OrderInterface $order, ZoneInterface $zone): void
    {
        return;
    }

    public function getType(): string
    {
        return 'dummy';
    }

    public function supports(OrderInterface $order, ZoneInterface $zone): bool
    {
        return true;
    }
}

#[AsTaxCalculationStrategy(type: 'dummy', label: 'dummy', priority: 128)]
class PrioritizedDummyTaxCalculationStrategy implements TaxCalculationStrategyInterface
{
    public function applyTaxes(OrderInterface $order, ZoneInterface $zone): void
    {
        return;
    }

    public function getType(): string
    {
        return 'dummy';
    }

    public function supports(OrderInterface $order, ZoneInterface $zone): bool
    {
        return true;
    }
}

#[AsUriBasedSectionResolver()]
class DummyUriBasedSectionResolver implements UriBasedSectionResolverInterface
{
    public function getSection(string $uri): SectionInterface
    {
        return new class implements SectionInterface {};
    }
}

#[AsUriBasedSectionResolver(priority: 128)]
class PrioritizedDummyUriBasedSectionResolver implements UriBasedSectionResolverInterface
{
    public function getSection(string $uri): SectionInterface
    {
        return new class implements SectionInterface {};
    }
}

