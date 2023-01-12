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

namespace Sylius\Bundle\CoreBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
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
        $container->setParameter('sylius_core.catalog_promotions.batch_size', $config['catalog_promotions']['batch_size']);

        $env = $container->getParameter('kernel.environment');
        if ('test' === $env || 'test_cached' === $env) {
            $loader->load('test_services.xml');
        }

        if ($config['process_shipments_before_recalculating_prices']) {
            $this->switchOrderProcessorsPriorities(
                $container->getDefinition('sylius.order_processing.order_shipment_processor'),
                $container->getDefinition('sylius.order_processing.order_prices_recalculator'),
            );
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        $config = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);

        $this->prependSyliusThemeBundle($container, $config['driver']);
        $this->prependHwiOauth($container);
        $this->prependDoctrineMigrations($container);
        $this->prependJmsSerializerIfAdminApiBundleIsNotPresent($container);
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

    private function switchOrderProcessorsPriorities(
        Definition $firstServiceDefinition,
        Definition $secondServiceDefinition,
    ) {
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
}
