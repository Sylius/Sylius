<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Registers all promotion benefits in registry service.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class RegisterPromotionBenefitsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.registry.promotion_benefit')) {
            return;
        }

        $registry = $container->getDefinition('sylius.registry.promotion_benefit');
        $benefits = array();

        foreach ($container->findTaggedServiceIds('sylius.promotion_benefit') as $id => $attributes) {
            if (!isset($attributes[0]['type']) || !isset($attributes[0]['label'])) {
                throw new \InvalidArgumentException('Tagged promotion benefit needs to have `type` and `label` attributes.');
            }

            $benefits[$attributes[0]['type']] = $attributes[0]['label'];

            $registry->addMethodCall('register', array($attributes[0]['type'], new Reference($id)));
        }

        $container->setParameter('sylius.promotion_benefits', $benefits);
    }

}
