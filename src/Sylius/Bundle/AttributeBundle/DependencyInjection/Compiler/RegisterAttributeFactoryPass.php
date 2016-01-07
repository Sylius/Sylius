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

use Sylius\Bundle\AttributeBundle\Factory\AttributeFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class RegisterAttributeFactoryPass implements CompilerPassInterface
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

        $oldAttributeFactory = $container->getDefinition('sylius.factory.product_attribute');
        $attributeFactoryDefinition = new Definition(AttributeFactory::class);

        $attributeFactory = $container->setDefinition('sylius.factory.product_attribute', $attributeFactoryDefinition);
        $attributeFactory->addArgument($oldAttributeFactory);
        $attributeFactory->addArgument($registry);
    }
}
