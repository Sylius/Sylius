<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ShippingBundle\DependencyInjection\Compiler;

use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterRuleCheckersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('sylius.registry.shipping_method_rule_checker') || !$container->has('sylius.form_registry.shipping_method_rule_checker')) {
            return;
        }

        $ruleCheckerRegistry = $container->getDefinition('sylius.registry.shipping_method_rule_checker');
        $ruleCheckerFormTypeRegistry = $container->getDefinition('sylius.form_registry.shipping_method_rule_checker');

        $ruleCheckerTypeToLabelMap = [];
        foreach ($container->findTaggedServiceIds('sylius.shipping_method_rule_checker') as $id => $attributes) {
            if (!isset($attributes[0]['type'], $attributes[0]['label'], $attributes[0]['form_type'])) {
                throw new InvalidArgumentException('Tagged shipping method rule checker `' . $id . '` needs to have `type`, `form_type` and `label` attributes.');
            }

            $ruleCheckerTypeToLabelMap[$attributes[0]['type']] = $attributes[0]['label'];
            $ruleCheckerRegistry->addMethodCall('register', [$attributes[0]['type'], new Reference($id)]);
            $ruleCheckerFormTypeRegistry->addMethodCall('add', [$attributes[0]['type'], 'default', $attributes[0]['form_type']]);
        }

        $container->setParameter('sylius.shipping_method_rules', $ruleCheckerTypeToLabelMap);
    }
}
