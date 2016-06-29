<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\PromotionBundle;

use Sylius\PromotionBundle\DependencyInjection\Compiler\RegisterPromotionActionsPass;
use Sylius\PromotionBundle\DependencyInjection\Compiler\RegisterRuleCheckersPass;
use Sylius\ResourceBundle\AbstractResourceBundle;
use Sylius\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Promotions are used to give discounts or other types of rewards to customers.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class SyliusPromotionBundle extends AbstractResourceBundle
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

        $container->addCompilerPass(new RegisterRuleCheckersPass());
        $container->addCompilerPass(new RegisterPromotionActionsPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Sylius\Promotion\Model';
    }
}
