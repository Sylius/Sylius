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

namespace Sylius\Bundle\PromotionBundle;

use Sylius\Bundle\PromotionBundle\DependencyInjection\Compiler\CompositePromotionCouponEligibilityCheckerPass;
use Sylius\Bundle\PromotionBundle\DependencyInjection\Compiler\CompositePromotionEligibilityCheckerPass;
use Sylius\Bundle\PromotionBundle\DependencyInjection\Compiler\RegisterPromotionActionsPass;
use Sylius\Bundle\PromotionBundle\DependencyInjection\Compiler\RegisterRuleCheckersPass;
use Sylius\Bundle\PromotionBundle\DependencyInjection\Compiler\SetCatalogPromotionActionTypesPass;
use Sylius\Bundle\PromotionBundle\DependencyInjection\Compiler\SetCatalogPromotionScopeTypesPass;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SyliusPromotionBundle extends AbstractResourceBundle
{
    public function getSupportedDrivers(): array
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new CompositePromotionEligibilityCheckerPass());
        $container->addCompilerPass(new CompositePromotionCouponEligibilityCheckerPass());

        $container->addCompilerPass(new RegisterRuleCheckersPass());
        $container->addCompilerPass(new RegisterPromotionActionsPass());

        $container->addCompilerPass(new SetCatalogPromotionActionTypesPass());
        $container->addCompilerPass(new SetCatalogPromotionScopeTypesPass());
    }

    protected function getModelNamespace(): string
    {
        return 'Sylius\Component\Promotion\Model';
    }
}
