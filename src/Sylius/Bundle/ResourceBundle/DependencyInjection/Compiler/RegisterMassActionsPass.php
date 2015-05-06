<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Registers all mass action types in calculator registry service.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class RegisterMassActionsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.registry.mass_action')) {
            return;
        }

        $registry = $container->getDefinition('sylius.registry.mass_action');
        $actions = array();

        foreach ($container->findTaggedServiceIds('sylius.mass_action') as $id => $attributes) {
            if (!isset($attributes[0]['type']) || !isset($attributes[0]['type'])) {
                throw new \InvalidArgumentException('Tagged mass actions must have `type` attribute.');
            }

            $type = $attributes[0]['type'];
            $actions[$type] = $attributes[0]['type'];

            $registry->addMethodCall('register', array($type, new Reference($id)));
        }

        $container->setParameter('sylius.mass_actions', $actions);
    }
}
