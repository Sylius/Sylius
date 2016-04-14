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
 * Registers all rule checkers in registry service.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class RegisterRuleCheckersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.registry.promotion_rule_checker')) {
            return;
        }

        $registry = $container->getDefinition('sylius.registry.promotion_rule_checker');
        $checkers = [];

        $checkersServices = $container->findTaggedServiceIds('sylius.promotion_rule_checker');
        ksort($checkersServices);

        foreach ($checkersServices as $id => $attributes) {
            if (!isset($attributes[0]['type']) || !isset($attributes[0]['label'])) {
                throw new \InvalidArgumentException('Tagged rule checker needs to have `type` and `label` attributes.');
            }

            $checkers[$attributes[0]['type']] = $attributes[0]['label'];

            $registry->addMethodCall('register', [$attributes[0]['type'], new Reference($id)]);
        }

        $container->setParameter('sylius.promotion_rules', $checkers);
    }
}
