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

namespace Sylius\Bundle\ShippingBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Component\Shipping\Attribute\AsShippingCalculator;
use Sylius\Component\Shipping\Attribute\AsShippingMethodResolver;
use Sylius\Component\Shipping\Attribute\AsShippingMethodRuleChecker;
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

        $loader->load('services.xml');

        $container->registerAttributeForAutoconfiguration(
            AsShippingCalculator::class,
            static function (ChildDefinition $definition, AsShippingCalculator $attribute) {
                $definition->addTag('sylius.shipping_calculator', [
                    'calculator' => $attribute->calculator,
                    'label' => $attribute->label,
                    'formType' => $attribute->formType
                ]);
            }
        );

        $container->registerAttributeForAutoconfiguration(
            AsShippingMethodRuleChecker::class,
            static function (ChildDefinition $definition, AsShippingMethodRuleChecker $attribute) {
                $definition->addTag('sylius.shipping_method_rule_checker', [
                    'type' => $attribute->type,
                    'label' => $attribute->label,
                    'formType' => $attribute->formType
                ]);
            }
        );

        $container->registerAttributeForAutoconfiguration(
            AsShippingMethodResolver::class,
            static function (ChildDefinition $definition, AsShippingMethodResolver $attribute) {
                $definition->addTag('sylius.shipping_method_resolver', [
                    'type' => $attribute->type,
                    'label' => $attribute->label,
                    'priority' => $attribute->priority
                ]);
            }
        );
    }
}
