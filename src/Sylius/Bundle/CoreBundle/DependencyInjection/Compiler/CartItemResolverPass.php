<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CartItemResolverPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $chain = $container->getDefinition('sylius.cart_item_resolver.chainable');

        $resolvers = array();
        foreach ($container->findTaggedServiceIds('sylius.item_resolver') as $id => $attributes) {
            $priority = isset($attributes[0]['priority']) ? $attributes[0]['priority'] : 0;
            $resolvers[$priority][] = array(
                'reference' => new Reference($id),
                'alias'     => $attributes[0]['alias']
            );
        }

        if (empty($resolvers)) {
            return;
        }

        // sort by priority and flatten
        krsort($resolvers);
        foreach ($resolvers as $priority) {
            foreach ($priority as $resolver) {
                $chain->addMethodCall('register', array($resolver['alias'], $resolver['reference']));
            }
        }

        $container->setDefinition('sylius.cart_item_resolver.default', $chain);
    }
}
