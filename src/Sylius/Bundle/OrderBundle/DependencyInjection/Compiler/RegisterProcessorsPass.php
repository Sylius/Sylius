<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
final class RegisterProcessorsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.order_processing.order_processor')) {
            return;
        }

        $orderProcessor = $container->findDefinition('sylius.order_processing.order_processor');

        foreach ($container->findTaggedServiceIds('sylius.order_processor') as $id => $attributes) {
            $priority = isset($attributes[0]['priority']) ? (int) $attributes[0]['priority'] : 0;

            $orderProcessor->addMethodCall('addProcessor', [new Reference($id), $priority]);
        }
    }
}
