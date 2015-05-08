<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AffiliateBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterRuleCheckersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.registry.affiliate_goal_rule_checker')) {
            return;
        }

        $registry = $container->getDefinition('sylius.registry.affiliate_goal_rule_checker');
        $checkers = array();

        foreach ($container->findTaggedServiceIds('sylius.affiliate_goal_rule_checker') as $id => $attributes) {
            if (!isset($attributes[0]['type'], $attributes[0]['label'])) {
                throw new \InvalidArgumentException('Tagged rule checker needs to have `type` and `label` attributes.');
            }

            $checkers[$attributes[0]['type']] = $attributes[0]['label'];

            $registry->addMethodCall('register', array($attributes[0]['type'], new Reference($id)));
        }

        $container->setParameter('sylius.affiliate_goal_rules', $checkers);
    }
}
