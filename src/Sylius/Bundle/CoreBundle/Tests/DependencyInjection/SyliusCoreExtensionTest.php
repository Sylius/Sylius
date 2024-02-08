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
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\DefinitionHasTagConstraint;
use Sylius\Bundle\CoreBundle\DependencyInjection\SyliusCoreExtension;
use Sylius\Component\Core\Filesystem\Adapter\FilesystemAdapterInterface;
use Sylius\Component\Core\Filesystem\Adapter\FlysystemFilesystemAdapter;
use Sylius\Component\Core\Filesystem\Adapter\GaufretteFilesystemAdapter;
use SyliusLabs\DoctrineMigrationsExtraBundle\DependencyInjection\SyliusLabsDoctrineMigrationsExtraExtension;

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
    public function it_aliases_gaufrette_filesystem_adapter_properly(): void
    {
        $this->container->setParameter('kernel.environment', 'dev');

        $this->load(['filesystem' => ['adapter' => 'gaufrette']]);

        $this->assertContainerBuilderHasAlias(FilesystemAdapterInterface::class, GaufretteFilesystemAdapter::class);
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
