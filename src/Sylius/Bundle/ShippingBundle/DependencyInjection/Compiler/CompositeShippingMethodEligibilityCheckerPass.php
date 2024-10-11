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

namespace Sylius\Bundle\ShippingBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class CompositeShippingMethodEligibilityCheckerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('sylius.eligibility_checker.shipping_method')) {
            return;
        }

        $container->getDefinition('sylius.eligibility_checker.shipping_method')->setArguments([
            array_map(
                static fn ($id): Reference => new Reference($id),
                array_keys($container->findTaggedServiceIds('sylius.shipping_method_eligibility_checker')),
            ),
        ]);
    }
}
