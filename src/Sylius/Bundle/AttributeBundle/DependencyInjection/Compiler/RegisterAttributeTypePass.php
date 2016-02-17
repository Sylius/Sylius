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
class RegisterAttributeTypePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.registry.attribute_type')) {
            return;
        }

        $registry = $container->getDefinition('sylius.registry.attribute_type');
        $attributeTypes = [];

        foreach ($container->findTaggedServiceIds('sylius.attribute.type') as $id => $attributes) {
            if (!isset($attributes[0]['attribute-type']) || !isset($attributes[0]['label'])) {
                throw new \InvalidArgumentException('Tagged attribute type needs to have `attribute-type` and `label` attributes.');
            }

            $name = $attributes[0]['attribute-type'];
            $attributeTypes[$name] = $attributes[0]['label'];
            $registry->addMethodCall('register', [$name, new Reference($id)]);
        }

        $container->setParameter('sylius.attribute.attribute_types', $attributeTypes);
    }
}
