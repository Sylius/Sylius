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

namespace Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\BackwardsCompatibility;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CancelOrderStateMachineCallbackPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('sm.configs')) {
            return;
        }

        /** @var array $smConfigs */
        $smConfigs = $container->getParameter('sm.configs');

        if (isset($smConfigs['sylius_order']['callbacks']['after']['sylis_cancel_order'])) {
            trigger_deprecation(
                'sylius/core-bundle',
                '1.11',
                'Callback "%s" was renamed to "%s". The old name will be removed in Sylius 2.0, use the new name to override it.',
                'winzou_state_machine.sylius_order.callbacks.after.sylis_cancel_order',
                'winzou_state_machine.sylius_order.callbacks.after.sylius_cancel_order',
            );

            $smConfigs['sylius_order']['callbacks']['after']['sylius_cancel_order'] = $smConfigs['sylius_order']['callbacks']['after']['sylis_cancel_order'];
            unset($smConfigs['sylius_order']['callbacks']['after']['sylis_cancel_order']);
            $container->setParameter('sm.configs', $smConfigs);
        }
    }
}
