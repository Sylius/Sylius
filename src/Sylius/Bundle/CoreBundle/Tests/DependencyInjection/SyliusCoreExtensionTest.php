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
use Symfony\Component\VarDumper\VarDumper;

final class SyliusCoreExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function it_brings_back_previous_order_processing_priorities(): void
    {
        $this->container->setParameter('kernel.environment', 'dev');

        $this->load(['bring_back_previous_order_processing_priorities' => true]);

//        $this->assertContainerBuilderHasServiceDefinitionWithTag(
//            'sylius.order_processing.order_shipment_processor',
//            'sylius.order_processor',
//            ['priority' => 50]
//        );

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'sylius.order_processing.order_prices_recalculator',
            'sylius.order_processor',
            ['priority' => 40]
        );

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'sylius.order_processing.order_prices_recalculator',
            'sylius.order_processor',
            ['priority' => 50]
        );
    }

    /**
     * @test
     */
    public function it_autoconfigures_prepending_doctrine_migrations_with_proper_migrations_path_for_test_env(): void
    {
        $this->testPrependingDoctrineMigrations('test');
    }

    /**
     * @test
     */
    public function it_autoconfigures_prepending_doctrine_migrations_with_proper_migrations_path_for_test_cached_env(): void
    {
        $this->testPrependingDoctrineMigrations('test_cached');
    }

    /**
     * @test
     */
    public function it_autoconfigures_prepending_doctrine_migrations_with_proper_migrations_path_for_dev_env(): void
    {
        $this->testPrependingDoctrineMigrations('dev');
    }

    /**
     * @test
     */
    public function it_does_not_autoconfigure_prepending_doctrine_migrations_if_it_is_disabled_for_test_env(): void
    {
        $this->testNotPrependingDoctrineMigrations('test');
    }

    /**
     * @test
     */
    public function it_does_not_autoconfigure_prepending_doctrine_migrations_if_it_is_disabled_for_test_cached_env(): void
    {
        $this->testNotPrependingDoctrineMigrations('test_cached');
    }

    /**
     * @test
     */
    public function it_does_not_autoconfigure_prepending_doctrine_migrations_if_it_is_disabled_for_dev_env(): void
    {
        $this->testNotPrependingDoctrineMigrations('dev');
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
            $doctrineMigrationsExtensionConfig[0]['migrations_paths']['Sylius\Bundle\CoreBundle\Migrations']
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
