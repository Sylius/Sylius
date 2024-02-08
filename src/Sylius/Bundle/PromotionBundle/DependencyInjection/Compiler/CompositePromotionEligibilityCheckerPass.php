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

final class CompositePromotionEligibilityCheckerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('sylius.promotion_eligibility_checker')) {
            return;
        }

        $container->getDefinition('sylius.promotion_eligibility_checker')->setArguments([
            array_map(
                fn ($id) => new Reference($id),
                array_keys($container->findTaggedServiceIds('sylius.promotion_eligibility_checker')),
            ),
        ]);
    }
}
