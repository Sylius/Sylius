<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Registers all filter types in registry service.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class RegisterFiltersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.registry.grid_filter')) {
            return;
        }

        $registry = $container->getDefinition('sylius.registry.grid_filter');

        foreach ($container->findTaggedServiceIds('sylius.grid_filter') as $id => $attributes) {
            if (!isset($attributes[0]['type'])) {
                throw new \InvalidArgumentException('Tagged grid filters needs to have `type` attribute.');
            }

            $registry->addMethodCall('register', array($attributes[0]['type'], new Reference($id)));
        }
    }
}
