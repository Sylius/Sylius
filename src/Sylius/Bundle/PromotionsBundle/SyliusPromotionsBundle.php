<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Sylius\Bundle\PromotionsBundle\DependencyInjection\Compiler\RegisterPromotionActionsPass;
use Sylius\Bundle\PromotionsBundle\DependencyInjection\Compiler\RegisterRuleCheckersPass;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\ResolveDoctrineTargetEntitiesPass;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Promotions are used to give discounts or other types of rewards to customers.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class SyliusPromotionsBundle extends Bundle
{
    public static function getSupportedDrivers()
    {
        return array(
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM
        );
    }

    public function build(ContainerBuilder $container)
    {
        $interfaces = array(
            'Sylius\Bundle\PromotionsBundle\Model\PromotionInterface'        => 'sylius.model.promotion.class',
            'Sylius\Bundle\PromotionsBundle\Model\CouponInterface'           => 'sylius.model.promotion_coupon.class',
            'Sylius\Bundle\PromotionsBundle\Model\RuleInterface'             => 'sylius.model.promotion_rule.class',
            'Sylius\Bundle\PromotionsBundle\Model\ActionInterface'           => 'sylius.model.promotion_action.class',
            'Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface' => 'sylius.model.promotion_subject.class',
        );

        $container->addCompilerPass(new ResolveDoctrineTargetEntitiesPass('sylius_promotions', $interfaces));
        $container->addCompilerPass(new RegisterRuleCheckersPass());
        $container->addCompilerPass(new RegisterPromotionActionsPass());

        $mappings = array(
            realpath(__DIR__ . '/Resources/config/doctrine/model') => 'Sylius\Bundle\PromotionsBundle\Model',
        );

        $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings, array('doctrine.orm.entity_manager'), 'sylius_promotions.driver.doctrine/orm'));
    }
}
