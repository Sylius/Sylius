<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class RegisterAttributeTypePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.registry.attribute_type') || !$container->hasDefinition('sylius.form_registry.attribute_type')) {
            return;
        }

        $registry = $container->getDefinition('sylius.registry.attribute_type');
        $formRegistry = $container->getDefinition('sylius.form_registry.attribute_type');

        $attributeTypes = [];
        foreach ($container->findTaggedServiceIds('sylius.attribute.type') as $id => $attributesTypes) {
            $attributeType = $attributesTypes[0];
            if (!isset($attributeType['attribute-type'], $attributeType['label'], $attributeType['form-type'])) {
                throw new \InvalidArgumentException('Tagged attribute type needs to have `attribute-type`, `label` and `form-type` attributes.');
            }

            $registry->addMethodCall('register', [$attributeType['attribute-type'], new Reference($id)]);
            $formRegistry->addMethodCall('add', [$attributeType['attribute-type'], 'default', $attributeType['form-type']]);

            if (isset($attributeType['configuration-form-type'])) {
                $formRegistry->addMethodCall('add', [$attributeType['attribute-type'], 'configuration', $attributeType['configuration-form-type']]);
            }

            $attributeTypes[$attributeType['attribute-type']] = $attributeType['label'];
        }

        $container->setParameter('sylius.attribute.attribute_types', $attributeTypes);
    }
}
