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
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CompositePromotionCouponEligibilityCheckerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('sylius.promotion_coupon_eligibility_checker')) {
            return;
        }

        $container->getDefinition('sylius.promotion_coupon_eligibility_checker')->setArguments([
            array_map(
                function ($id) {
                    return new Reference($id);
                },
                array_keys($container->findTaggedServiceIds('sylius.promotion_coupon_eligibility_checker'))
            )
        ]);
    }
}
