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

namespace Sylius\Bundle\AttributeBundle\DependencyInjection;

use Sylius\Bundle\AttributeBundle\Attribute\AsAttributeType;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SyliusAttributeExtension extends AbstractResourceExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.xml');

        $this->registerResources('sylius', $config['driver'], $this->resolveResources($config['resources'], $container), $container);
        $this->registerAutoconfiguration($container);
    }

    private function resolveResources(array $resources, ContainerBuilder $container): array
    {
        $container->setParameter('sylius.attribute.subjects', $resources);

        $resolvedResources = [];
        foreach ($resources as $subjectName => $subjectConfig) {
            foreach ($subjectConfig as $resourceName => $resourceConfig) {
                if (is_array($resourceConfig)) {
                    $resolvedResources[$subjectName . '_' . $resourceName] = $resourceConfig;
                }
            }
        }

        return $resolvedResources;
    }

    private function registerAutoconfiguration(ContainerBuilder $container): void
    {
        $container->registerAttributeForAutoconfiguration(
            AsAttributeType::class,
            static function (ChildDefinition $definition, AsAttributeType $attribute): void {
                $definition->addTag(AsAttributeType::SERVICE_TAG, [
                    'attribute-type' => $attribute->getType(),
                    'label' => $attribute->getLabel(),
                    'form-type' => $attribute->getFormType(),
                    'priority' => $attribute->getPriority(),
                ]);
            },
        );
    }
}
