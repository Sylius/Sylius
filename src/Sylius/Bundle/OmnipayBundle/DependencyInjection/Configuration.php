<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OmnipayBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Omnipay\Common\GatewayFactory;
use Omnipay\Common\CreditCard;

/**
 * This class contains the configuration information for the bundle.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Dylan Johnson <eponymi.dev@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $rootNode = $builder->root('sylius_omnipay');

        $gateways = GatewayFactory::find();
        $ccTypes = array_keys(new CreditCard()->getSupportedBrands());

        $rootNode
                ->children()
                    ->arrayNode('gateways')
                        ->useAttributeAsKey('name')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('type')
                                    ->validate()
                                        ->ifTrue(function($type) use ($gateways){
                                                    if (empty($type)) {
                                                        return true;
                                                    }

                                                    if (!in_array($type, $gateways)) {
                                                        return true;
                                                    }

                                                    return false;
                                                })
                                        ->thenInvalid(sprintf('Unknown payment gateway selected. Valid gateways are: %s.',  implode(", ",$gateways)))
                                    ->end()
                                ->end()
                                ->scalarNode('label')->cannotBeEmpty()->end()
                                ->booleanNode('mode')->defaultFalse()->end()
                                ->booleanNode('active')->defaultTrue()->end()
                                ->arrayNode('cc_types')
            						->prototype('scalar')
            							->validate()
                                        	->ifTrue(function($ccType) use ($brands){
                                                    if (empty($ccType)) {
                                                        return true;
                                                    }

                                                    if (!in_array($ccType, $ccTypes)) {
                                                        return true;
                                                    }

                                                    return false;
                                                })
                                        	->thenInvalid(sprintf('Unknown credit card type selected. Valid credit card types are: %s.',  implode(", ",$ccTypes)))
                                    	->end()
            						->end()
        						->end()
                                ->arrayNode('options')
                                    ->prototype('scalar')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
        ;

        return $builder;
    }
}
