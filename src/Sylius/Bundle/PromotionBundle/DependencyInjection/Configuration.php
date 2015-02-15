<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle.
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_promotion');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')->isRequired()->cannotBeEmpty()->end()
            ->end()
        ;

        $this->addClassesSection($rootNode);
        $this->addValidationGroupsSection($rootNode);

        return $treeBuilder;
    }

    private function addValidationGroupsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('validation_groups')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('promotion')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                        ->arrayNode('promotion_rule')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                        ->arrayNode('promotion_coupon')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                        ->arrayNode('promotion_action')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                        ->arrayNode('promotion_rule_item_total_configuration')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                        ->arrayNode('promotion_rule_item_count_configuration')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                        ->arrayNode('promotion_rule_user_loyalty_configuration')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                        ->arrayNode('promotion_rule_shipping_country_configuration')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                        ->arrayNode('promotion_rule_taxonomy_configuration')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                        ->arrayNode('promotion_rule_contains_product_configuration')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                        ->arrayNode('promotion_rule_nth_order_configuration')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                        ->arrayNode('promotion_rule_user_group_configuration')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                        ->arrayNode('promotion_action_fixed_discount_configuration')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                        ->arrayNode('promotion_action_percentage_discount_configuration')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                        ->arrayNode('promotion_action_add_product_configuration')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                        ->arrayNode('promotion_coupon_generate_instruction')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                        ->arrayNode('promotion_action_shipping_discount_configuration')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addClassesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('classes')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('promotion')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\Component\Promotion\Model\Promotion')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end()
                                ->scalarNode('repository')->end()
                                ->scalarNode('form')->defaultValue('Sylius\Bundle\PromotionBundle\Form\Type\PromotionType')->end()
                            ->end()
                        ->end()
                        ->arrayNode('promotion_rule')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\Component\Promotion\Model\Rule')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end()
                                ->scalarNode('repository')->end()
                                ->scalarNode('form')->defaultValue('Sylius\Bundle\PromotionBundle\Form\Type\RuleType')->end()
                            ->end()
                        ->end()
                        ->arrayNode('promotion_action')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\Component\Promotion\Model\Action')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end()
                                ->scalarNode('repository')->end()
                                ->scalarNode('form')->defaultValue('Sylius\Bundle\PromotionBundle\Form\Type\ActionType')->end()
                            ->end()
                        ->end()
                        ->arrayNode('promotion_coupon')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\Component\Promotion\Model\Coupon')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\Bundle\PromotionBundle\Controller\CouponController')->end()
                                ->scalarNode('repository')->end()
                                ->scalarNode('form')->defaultValue('Sylius\Bundle\PromotionBundle\Form\Type\CouponType')->end()
                            ->end()
                        ->end()
                        ->arrayNode('promotion_subject')
                            ->isRequired()
                            ->children()
                                ->scalarNode('model')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end() // @todo: Remove.
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
