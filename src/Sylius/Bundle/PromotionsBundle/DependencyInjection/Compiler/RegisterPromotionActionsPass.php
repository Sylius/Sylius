<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Registers all promotion actions in registry service.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class RegisterPromotionActionsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.registry.promotion_action')) {
            return;
        }

        $registry = $container->getDefinition('sylius.registry.promotion_action');
        $checkers = array();

        foreach ($container->findTaggedServiceIds('sylius.promotion_action') as $id => $attributes) {
                $checkers[$attributes[0]['type']] = $attributes[0]['label'];

            $registry->addMethodCall('registerAction', array($attributes[0]['type'], new Reference($id)));
        }

        $container->setParameter('sylius.promotion_actions', $checkers);
    }
}
