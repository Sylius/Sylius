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

namespace Sylius\Bundle\ShippingBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Bundle\ShippingBundle\Attribute\AsShippingCalculator;
use Sylius\Bundle\ShippingBundle\Attribute\AsShippingMethodResolver;
use Sylius\Bundle\ShippingBundle\Attribute\AsShippingMethodRuleChecker;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SyliusShippingExtension extends AbstractResourceExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load(sprintf('services/integrations/%s.xml', $config['driver']));

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $container->setParameter('sylius.shipping.shipping_method_rule.validation_groups', $config['shipping_method_rule']['validation_groups']);
        $container->setParameter('sylius.shipping.shipping_method_calculator.validation_groups', $config['shipping_method_calculator']['validation_groups']);

        $loader->load('services.xml');
        $this->registerAutoconfiguration($container);
    }

    private function registerAutoconfiguration(ContainerBuilder $container): void
    {
        $container->registerAttributeForAutoconfiguration(
            AsShippingCalculator::class,
            static function (ChildDefinition $definition, AsShippingCalculator $attribute): void {
                $definition->addTag(AsShippingCalculator::SERVICE_TAG, [
                    'calculator' => $attribute->getCalculator(),
                    'label' => $attribute->getLabel(),
                    'form-type' => $attribute->getFormType(),
                    'priority' => $attribute->getPriority(),
                ]);
            },
        );

        $container->registerAttributeForAutoconfiguration(
            AsShippingMethodResolver::class,
            static function (ChildDefinition $definition, AsShippingMethodResolver $attribute): void {
                $definition->addTag(AsShippingMethodResolver::SERVICE_TAG, [
                    'type' => $attribute->getType(),
                    'label' => $attribute->getLabel(),
                    'priority' => $attribute->getPriority(),
                ]);
            },
        );

        $container->registerAttributeForAutoconfiguration(
            AsShippingMethodRuleChecker::class,
            static function (ChildDefinition $definition, AsShippingMethodRuleChecker $attribute): void {
                $definition->addTag(AsShippingMethodRuleChecker::SERVICE_TAG, [
                    'type' => $attribute->getType(),
                    'label' => $attribute->getLabel(),
                    'form-type' => $attribute->getFormType(),
                    'priority' => $attribute->getPriority(),
                ]);
            },
        );
    }
}
