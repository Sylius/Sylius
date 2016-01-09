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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class RegisterDriversPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $registry = $container->getDefinition('sylius.registry.grid_driver');

        foreach ($container->findTaggedServiceIds('sylius.grid_driver') as $id => $attributes) {
            if (!isset($attributes[0]['alias']))  {
                throw new \InvalidArgumentException('Tagged grid drivers needs to have `alias` attribute.');
            }

            $registry->addMethodCall('register', array($attributes[0]['alias'], new Reference($id)));
        }
    }
}
