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

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Sylius\Bundle\PromotionBundle\DependencyInjection\Compiler\RegisterPromotionActionsPass;
use Sylius\Bundle\PromotionBundle\DependencyInjection\Compiler\RegisterRuleCheckersPass;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\ResolveDoctrineTargetEntitiesPass;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Promotions are used to give discounts or other types of rewards to customers.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class SyliusPromotionBundle extends Bundle
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
            'Sylius\Component\Promotion\Model\PromotionInterface'        => 'sylius.model.promotion.class',
            'Sylius\Component\Promotion\Model\CouponInterface'           => 'sylius.model.promotion_coupon.class',
            'Sylius\Component\Promotion\Model\RuleInterface'             => 'sylius.model.promotion_rule.class',
            'Sylius\Component\Promotion\Model\ActionInterface'           => 'sylius.model.promotion_action.class',
            'Sylius\Component\Promotion\Model\PromotionSubjectInterface' => 'sylius.model.promotion_subject.class',
        );

        $container->addCompilerPass(new ResolveDoctrineTargetEntitiesPass('sylius_promotion', $interfaces));
        $container->addCompilerPass(new RegisterRuleCheckersPass());
        $container->addCompilerPass(new RegisterPromotionActionsPass());

        $mappings = array(
            realpath(__DIR__ . '/Resources/config/doctrine/model') => 'Sylius\Component\Promotion\Model',
        );

        $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings, array('doctrine.orm.entity_manager'), 'sylius_promotion.driver.doctrine/orm'));
    }
}
