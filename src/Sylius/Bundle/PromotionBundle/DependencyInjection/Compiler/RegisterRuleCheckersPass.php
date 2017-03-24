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
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
final class RegisterRuleCheckersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('sylius.registry_promotion_rule_checker') || !$container->has('sylius.form_registry.promotion_rule_checker')) {
            return;
        }

        $promotionRuleCheckerRegistry = $container->getDefinition('sylius.registry_promotion_rule_checker');
        $promotionRuleCheckerFormTypeRegistry = $container->getDefinition('sylius.form_registry.promotion_rule_checker');

        $promotionRuleCheckerTypeToLabelMap = [];
        foreach ($container->findTaggedServiceIds('sylius.promotion_rule_checker') as $id => $attributes) {
            if (!isset($attributes[0]['type'], $attributes[0]['label'], $attributes[0]['form-type'])) {
                throw new \InvalidArgumentException('Tagged rule checker `'.$id.'` needs to have `type`, `form-type` and `label` attributes.');
            }

            $promotionRuleCheckerTypeToLabelMap[$attributes[0]['type']] = $attributes[0]['label'];
            $promotionRuleCheckerRegistry->addMethodCall('register', [$attributes[0]['type'], new Reference($id)]);
            $promotionRuleCheckerFormTypeRegistry->addMethodCall('add', [$attributes[0]['type'], 'default', $attributes[0]['form-type']]);
        }

        $container->setParameter('sylius.promotion_rules', $promotionRuleCheckerTypeToLabelMap);
    }
}
