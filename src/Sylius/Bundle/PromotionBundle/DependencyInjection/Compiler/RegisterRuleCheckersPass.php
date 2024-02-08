<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\PromotionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterRuleCheckersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('sylius.registry_promotion_rule_checker') || !$container->has('sylius.form_registry.promotion_rule_checker')) {
            return;
        }

        $promotionRuleCheckerRegistry = $container->getDefinition('sylius.registry_promotion_rule_checker');
        $promotionRuleCheckerFormTypeRegistry = $container->getDefinition('sylius.form_registry.promotion_rule_checker');

        $promotionRuleCheckerTypeToLabelMap = [];
        foreach ($container->findTaggedServiceIds('sylius.promotion_rule_checker') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['type'], $attribute['label'], $attribute['form_type'])) {
                    throw new \InvalidArgumentException('Tagged rule checker `' . $id . '` needs to have `type`, `form_type` and `label` attributes.');
                }

                $promotionRuleCheckerTypeToLabelMap[$attribute['type']] = $attribute['label'];
                $promotionRuleCheckerRegistry->addMethodCall('register', [$attribute['type'], new Reference($id)]);
                $promotionRuleCheckerFormTypeRegistry->addMethodCall('add', [$attribute['type'], 'default', $attribute['form_type']]);
            }
        }

        $container->setParameter('sylius.promotion_rules', $promotionRuleCheckerTypeToLabelMap);
    }
}
