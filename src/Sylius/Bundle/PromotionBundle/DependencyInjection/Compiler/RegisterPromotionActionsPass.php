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

final class RegisterPromotionActionsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('sylius.registry_promotion_action') || !$container->has('sylius.form_registry.promotion_action')) {
            return;
        }

        $promotionActionRegistry = $container->getDefinition('sylius.registry_promotion_action');
        $promotionActionFormTypeRegistry = $container->getDefinition('sylius.form_registry.promotion_action');

        $promotionActionTypeToLabelMap = [];
        foreach ($container->findTaggedServiceIds('sylius.promotion_action') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['type'], $attribute['label'], $attribute['form_type'])) {
                    throw new \InvalidArgumentException('Tagged promotion action `' . $id . '` needs to have `type`, `form_type` and `label` attributes.');
                }

                $promotionActionTypeToLabelMap[$attribute['type']] = $attribute['label'];
                $promotionActionRegistry->addMethodCall('register', [$attribute['type'], new Reference($id)]);
                $promotionActionFormTypeRegistry->addMethodCall('add', [$attribute['type'], 'default', $attribute['form_type']]);
            }
        }

        $container->setParameter('sylius.promotion_actions', $promotionActionTypeToLabelMap);
    }
}
