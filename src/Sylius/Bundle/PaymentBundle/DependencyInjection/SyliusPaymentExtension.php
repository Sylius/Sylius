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

namespace Sylius\Bundle\PaymentBundle\DependencyInjection;

use Sylius\Bundle\PaymentBundle\Attribute\AsGatewayConfigurationType;
use Sylius\Bundle\PaymentBundle\Attribute\AsNotifyPaymentProvider;
use Sylius\Bundle\PaymentBundle\Attribute\AsPaymentMethodsResolver;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SyliusPaymentExtension extends AbstractResourceExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $loader->load('services.xml');

        $container->setParameter('sylius.payment_gateways', $config['gateways']);
        $container->setParameter('sylius.gateway_config.validation_groups', $config['gateway_config']['validation_groups']);
        $container->setParameter('sylius.payment_request.states_to_be_cancelled_when_payment_method_changed', $config['payment_request']['states_to_be_cancelled_when_payment_method_changed']);

        $this->configureEncryption($config['encryption'], $container);

        $this->registerAutoconfiguration($container);
    }

    private function registerAutoconfiguration(ContainerBuilder $container): void
    {
        $container->registerAttributeForAutoconfiguration(
            AsGatewayConfigurationType::class,
            static function (ChildDefinition $definition, AsGatewayConfigurationType $attribute): void {
                $definition->addTag(AsGatewayConfigurationType::SERVICE_TAG, [
                    'type' => $attribute->getType(),
                    'label' => $attribute->getLabel(),
                    'priority' => $attribute->getPriority(),
                ]);
            },
        );

        $container->registerAttributeForAutoconfiguration(
            AsPaymentMethodsResolver::class,
            static function (ChildDefinition $definition, AsPaymentMethodsResolver $attribute): void {
                $definition->addTag(AsPaymentMethodsResolver::SERVICE_TAG, [
                    'type' => $attribute->getType(),
                    'label' => $attribute->getLabel(),
                    'priority' => $attribute->getPriority(),
                ]);
            },
        );

        $container->registerAttributeForAutoconfiguration(
            AsNotifyPaymentProvider::class,
            static function (ChildDefinition $definition, AsNotifyPaymentProvider $attribute): void {
                $definition->addTag(AsNotifyPaymentProvider::SERVICE_TAG, [
                    'priority' => $attribute->getPriority(),
                ]);
            },
        );
    }

    /** @param array<string, mixed> $encryptionConfig */
    private function configureEncryption(
        array $encryptionConfig,
        ContainerBuilder $container,
    ): void {
        $container->setParameter('sylius.encryption.enabled', $encryptionConfig['enabled']);
        if (false === $encryptionConfig['enabled']) {
            return;
        }

        $container->setParameter('sylius.encryption.disabled_for_factories', $encryptionConfig['disabled_for_factories']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config/services/encryption'));

        $loader->load('encryption.xml');
    }
}
