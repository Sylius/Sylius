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

use Sylius\Component\Attribute\Factory\AttributeFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class RegisterAttributeFactoryPass implements CompilerPassInterface
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

        foreach (array_keys($container->getParameter('sylius.attribute.subjects')) as $subject) {
            $oldAttributeFactory = $container->getDefinition(sprintf('sylius.factory.%s_attribute', $subject));
            $attributeFactoryDefinition = new Definition(AttributeFactory::class);

            $attributeFactory = $container->setDefinition(sprintf('sylius.factory.%s_attribute', $subject), $attributeFactoryDefinition);
            $attributeFactory->addArgument($oldAttributeFactory);
            $attributeFactory->addArgument($registry);
        }
    }
}
