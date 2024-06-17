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

namespace Sylius\Bundle\CoreBundle\DependencyInjection;

use Sylius\Bundle\CoreBundle\Attribute\AsCatalogPromotionApplicatorCriteria;
use Sylius\Bundle\CoreBundle\Attribute\AsCatalogPromotionPriceCalculator;
use Sylius\Bundle\CoreBundle\Attribute\AsEntityObserver;
use Sylius\Bundle\CoreBundle\Attribute\AsOrderItemsTaxesApplicator;
use Sylius\Bundle\CoreBundle\Attribute\AsOrderItemUnitsTaxesApplicator;
use Sylius\Bundle\CoreBundle\Attribute\AsOrdersTotalsProvider;
use Sylius\Bundle\CoreBundle\Attribute\AsProductVariantMapProvider;
use Sylius\Bundle\CoreBundle\Attribute\AsTaxCalculationStrategy;
use Sylius\Bundle\CoreBundle\Attribute\AsUriBasedSectionResolver;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Component\Core\Filesystem\Adapter\FilesystemAdapterInterface;
use Sylius\Component\Core\Filesystem\Adapter\FlysystemFilesystemAdapter;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SyliusCoreExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    use PrependDoctrineMigrationsTrait;

    private static array $bundles = [
        'sylius_addressing',
        'sylius_attribute',
        'sylius_channel',
        'sylius_currency',
        'sylius_customer',
        'sylius_inventory',
        'sylius_locale',
        'sylius_order',
        'sylius_payment',
        'sylius_payum',
        'sylius_product',
        'sylius_promotion',
        'sylius_review',
        'sylius_shipping',
        'sylius_taxation',
        'sylius_taxonomy',
        'sylius_user',
        'sylius_variation',
    ];

    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load(sprintf('services/integrations/%s.xml', $config['driver']));

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $loader->load('services.xml');

        $container->setParameter('sylius_core.taxation.shipping_address_based_taxation', $config['shipping_address_based_taxation']);
        $container->setParameter('sylius_core.order_by_identifier', $config['order_by_identifier']);
        $container->setParameter('sylius_core.order_token_length', $config['order_token_length']);
        $container->setParameter('sylius_core.catalog_promotions.batch_size', $config['catalog_promotions']['batch_size']);
        $container->setParameter('sylius_core.price_history.batch_size', $config['price_history']['batch_size']);
        $container->setParameter('sylius_core.orders_statistics.intervals_map', $config['orders_statistics']['intervals_map'] ?? []);
        $container->setParameter('sylius_core.max_int_value', $config['max_int_value']);

        /** @var string $env */
        $env = $container->getParameter('kernel.environment');
        if (str_starts_with($env, 'test')) {
            $loader->load('test_services.xml');
        }

        if ($config['process_shipments_before_recalculating_prices']) {
            $this->switchOrderProcessorsPriorities(
                $container->getDefinition('sylius.order_processing.order_shipment_processor'),
                $container->getDefinition('sylius.order_processing.order_prices_recalculator'),
            );
        }

        $container->setAlias(
            FilesystemAdapterInterface::class,
            match ($config['filesystem']['adapter']) {
                'default', 'flysystem' => FlysystemFilesystemAdapter::class,
                'gaufrette' => 'Sylius\Component\Core\Filesystem\Adapter\GaufretteFilesystemAdapter',
                default => throw new \InvalidArgumentException(sprintf(
                    'Invalid filesystem adapter "%s" provided.',
                    $config['filesystem']['adapter'],
                )),
            },
        );

        $this->registerAutoconfiguration($container);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $config = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);

        $this->prependSyliusThemeBundle($container, $config['driver']);
        $this->prependHwiOauth($container);
        $this->prependDoctrineMigrations($container);
        $this->prependJmsSerializerIfAdminApiBundleIsNotPresent($container);
        $this->prependSyliusOrderBundle($container, $config);
    }

    protected function getMigrationsNamespace(): string
    {
        return 'Sylius\Bundle\CoreBundle\Migrations';
    }

    protected function getMigrationsDirectory(): string
    {
        return '@SyliusCoreBundle/Migrations';
    }

    protected function getNamespacesOfMigrationsExecutedBefore(): array
    {
        return [];
    }

    private function prependHwiOauth(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('hwi_oauth')) {
            return;
        }

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services/integrations/hwi_oauth.xml');
    }

    private function prependSyliusThemeBundle(ContainerBuilder $container, string $driver): void
    {
        if (!$container->hasExtension('sylius_theme')) {
            return;
        }

        foreach ($container->getExtensions() as $name => $extension) {
            if (in_array($name, self::$bundles, true)) {
                $container->prependExtensionConfig($name, ['driver' => $driver]);
            }
        }

        $container->prependExtensionConfig('sylius_theme', ['context' => 'sylius.theme.context.channel_based']);
    }

    private function prependJmsSerializerIfAdminApiBundleIsNotPresent(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('jms_serializer')) {
            return;
        }

        if ($container->hasExtension('sylius_admin_api')) {
            return;
        }

        $container->prependExtensionConfig('jms_serializer', [
            'metadata' => [
                'directories' => [
                    'sylius-core' => [
                        'namespace_prefix' => 'Sylius\Component\Core',
                        'path' => '@SyliusCoreBundle/Resources/config/serializer',
                    ],
                ],
            ],
            'property_naming' => [
                'id' => 'jms_serializer.identical_property_naming_strategy',
            ],
        ]);
    }

    private function prependSyliusOrderBundle(ContainerBuilder $container, array $config): void
    {
        if (!$container->hasExtension('sylius_order')) {
            return;
        }

        $container->prependExtensionConfig('sylius_order', [
            'autoconfigure_with_attributes' => $config['autoconfigure_with_attributes'] ?? false,
        ]);
    }

    private function switchOrderProcessorsPriorities(
        Definition $firstServiceDefinition,
        Definition $secondServiceDefinition,
    ): void {
        $firstServicePriority = $firstServiceDefinition->getTag('sylius.order_processor')[0]['priority'];
        $secondServicePriority = $secondServiceDefinition->getTag('sylius.order_processor')[0]['priority'];

        $firstServiceDefinition->clearTag('sylius.order_processor');
        $secondServiceDefinition->clearTag('sylius.order_processor');

        $firstServiceDefinition->addTag(
            'sylius.order_processor',
            ['priority' => $secondServicePriority],
        );
        $secondServiceDefinition->addTag(
            'sylius.order_processor',
            ['priority' => $firstServicePriority],
        );
    }

    private function registerAutoconfiguration(ContainerBuilder $container): void
    {
        $container->registerAttributeForAutoconfiguration(
            AsCatalogPromotionApplicatorCriteria::class,
            static function (ChildDefinition $definition, AsCatalogPromotionApplicatorCriteria $attribute): void {
                $definition->addTag(AsCatalogPromotionApplicatorCriteria::SERVICE_TAG, ['priority' => $attribute->getPriority()]);
            },
        );

        $container->registerAttributeForAutoconfiguration(
            AsCatalogPromotionPriceCalculator::class,
            static function (ChildDefinition $definition, AsCatalogPromotionPriceCalculator $attribute): void {
                $definition->addTag(AsCatalogPromotionPriceCalculator::SERVICE_TAG, ['priority' => $attribute->getPriority()]);
            },
        );

        $container->registerAttributeForAutoconfiguration(
            AsEntityObserver::class,
            static function (ChildDefinition $definition, AsEntityObserver $attribute): void {
                $definition->addTag(AsEntityObserver::SERVICE_TAG, ['priority' => $attribute->getPriority()]);
            },
        );

        $container->registerAttributeForAutoconfiguration(
            AsOrderItemsTaxesApplicator::class,
            static function (ChildDefinition $definition, AsOrderItemsTaxesApplicator $attribute): void {
                $definition->addTag(AsOrderItemsTaxesApplicator::SERVICE_TAG, ['priority' => $attribute->getPriority()]);
            },
        );

        $container->registerAttributeForAutoconfiguration(
            AsOrderItemUnitsTaxesApplicator::class,
            static function (ChildDefinition $definition, AsOrderItemUnitsTaxesApplicator $attribute): void {
                $definition->addTag(AsOrderItemUnitsTaxesApplicator::SERVICE_TAG, ['priority' => $attribute->getPriority()]);
            },
        );

        $container->registerAttributeForAutoconfiguration(
            AsProductVariantMapProvider::class,
            static function (ChildDefinition $definition, AsProductVariantMapProvider $attribute): void {
                $definition->addTag(AsProductVariantMapProvider::SERVICE_TAG, ['priority' => $attribute->getPriority()]);
            },
        );

        $container->registerAttributeForAutoconfiguration(
            AsTaxCalculationStrategy::class,
            static function (ChildDefinition $definition, AsTaxCalculationStrategy $attribute): void {
                $definition->addTag(AsTaxCalculationStrategy::SERVICE_TAG, [
                    'type' => $attribute->getType(),
                    'label' => $attribute->getLabel(),
                    'priority' => $attribute->getPriority(),
                ]);
            },
        );

        $container->registerAttributeForAutoconfiguration(
            AsUriBasedSectionResolver::class,
            static function (ChildDefinition $definition, AsUriBasedSectionResolver $attribute): void {
                $definition->addTag(AsUriBasedSectionResolver::SERVICE_TAG, ['priority' => $attribute->getPriority()]);
            },
        );

        $container->registerAttributeForAutoconfiguration(
            AsOrdersTotalsProvider::class,
            static function (ChildDefinition $definition, AsOrdersTotalsProvider $attribute): void {
                $definition->addTag(AsOrdersTotalsProvider::SERVICE_TAG, ['type' => $attribute->getType()]);
            },
        );
    }
}
