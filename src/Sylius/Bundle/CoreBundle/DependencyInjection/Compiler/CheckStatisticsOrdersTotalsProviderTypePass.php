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

namespace Sylius\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CheckStatisticsOrdersTotalsProviderTypePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('sylius_core.orders_statistics.intervals_map')) {
            return;
        }

        $intervalsTypes = $container->getParameter('sylius_core.orders_statistics.intervals_map');
        $ordersTotalsProviderTypes = [];

        foreach ($container->findTaggedServiceIds('sylius.statistics.orders_totals_provider') as $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['type'])) {
                    throw new \InvalidArgumentException('Tagged orders totals providers need to have `type` attribute.');
                }

                $ordersTotalsProviderTypes[] = $attribute['type'];
            }
        }

        foreach ($intervalsTypes as $type => $interval) {
            if (!in_array($type, $ordersTotalsProviderTypes, true)) {
                throw new \InvalidArgumentException(sprintf('There is no orders totals provider for interval type "%s"', $type));
            }
        }
    }
}
