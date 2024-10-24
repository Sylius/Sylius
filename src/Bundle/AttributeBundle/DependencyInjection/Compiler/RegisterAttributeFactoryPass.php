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

namespace Sylius\Bundle\AttributeBundle\DependencyInjection\Compiler;

use Sylius\Component\Attribute\Factory\AttributeFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterAttributeFactoryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('sylius.registry.attribute_type')) {
            return;
        }

        $registry = new Reference('sylius.registry.attribute_type');

        foreach (array_keys($container->getParameter('sylius.attribute.subjects')) as $subject) {
            $oldAttributeFactory = $container->getDefinition(sprintf('sylius.factory.%s_attribute', $subject));
            $attributeFactoryDefinition = new Definition(AttributeFactory::class, [$oldAttributeFactory, $registry]);
            $attributeFactoryDefinition->setPublic(true);

            $container->setDefinition(sprintf('sylius.factory.%s_attribute', $subject), $attributeFactoryDefinition);
        }
    }
}
