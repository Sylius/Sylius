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

use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;

/**
 * @author Daniel Gorgan <danut007ro@gmail.com>
 */
class RegisterOrderItemFactoryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.factory.order_item')) {
            return;
        }

        $factoryDefinition = new Definition(Factory::class, [new Parameter('sylius.model.order_item.class')]);

        $orderItemFactoryDefinition = new Definition(
            $container->getParameter('sylius.factory.order_item.class'),
            [$factoryDefinition]
        );

        $container->setDefinition('sylius.factory.order_item', $orderItemFactoryDefinition);
    }
}
