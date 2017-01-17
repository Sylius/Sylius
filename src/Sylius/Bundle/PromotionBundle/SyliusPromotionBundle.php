<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle;

use Sylius\Bundle\PromotionBundle\DependencyInjection\Compiler\CompositePromotionCouponEligibilityCheckerPass;
use Sylius\Bundle\PromotionBundle\DependencyInjection\Compiler\CompositePromotionEligibilityCheckerPass;
use Sylius\Bundle\PromotionBundle\DependencyInjection\Compiler\RegisterPromotionActionsPass;
use Sylius\Bundle\PromotionBundle\DependencyInjection\Compiler\RegisterRuleCheckersPass;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
final class SyliusPromotionBundle extends AbstractResourceBundle
{
    /**
     * {@inheritdoc}
     */
    public function getSupportedDrivers()
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new CompositePromotionEligibilityCheckerPass());
        $container->addCompilerPass(new CompositePromotionCouponEligibilityCheckerPass());

        $container->addCompilerPass(new RegisterRuleCheckersPass());
        $container->addCompilerPass(new RegisterPromotionActionsPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Sylius\Component\Promotion\Model';
    }
}
