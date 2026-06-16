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

use Sylius\Component\Core\Promotion\Action\ChannelAwarePromotionApplicator;
use Sylius\Component\Core\Promotion\Checker\Eligibility\ChannelAwarePromotionRulesEligibilityChecker;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class OverridePromotionServicesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition('sylius.checker.promotion.rules_eligibility')) {
            $container->getDefinition('sylius.checker.promotion.rules_eligibility')
                ->setClass(ChannelAwarePromotionRulesEligibilityChecker::class);
        }

        if ($container->hasDefinition('sylius.action.applicator.promotion')) {
            $container->getDefinition('sylius.action.applicator.promotion')
                ->setClass(ChannelAwarePromotionApplicator::class);
        }
    }
}
