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

namespace Sylius\Bundle\AttributeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterAttributeTypePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('sylius.registry.attribute_type') || !$container->hasDefinition('sylius.form_registry.attribute_type')) {
            return;
        }

        $registry = $container->getDefinition('sylius.registry.attribute_type');
        $formRegistry = $container->getDefinition('sylius.form_registry.attribute_type');

        $attributeTypes = [];
        foreach ($container->findTaggedServiceIds('sylius.attribute.type') as $id => $attributesTypes) {
            $attributeType = $attributesTypes[0];
            if (!isset($attributeType['attribute_type'], $attributeType['label'], $attributeType['form_type'])) {
                throw new \InvalidArgumentException('Tagged attribute type needs to have `attribute_type`, `label` and `form_type` attributes.');
            }

            $registry->addMethodCall('register', [$attributeType['attribute_type'], new Reference($id)]);
            $formRegistry->addMethodCall('add', [$attributeType['attribute_type'], 'default', $attributeType['form_type']]);

            if (isset($attributeType['configuration_form_type'])) {
                $formRegistry->addMethodCall('add', [$attributeType['attribute_type'], 'configuration', $attributeType['configuration_form_type']]);
            }

            $attributeTypes[$attributeType['attribute_type']] = $attributeType['label'];
        }

        $container->setParameter('sylius.attribute.attribute_types', $attributeTypes);
    }
}
