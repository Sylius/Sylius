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
use Omnipay\Common\GatewayFactory as OmnipayGatewayFactory;
use Omnipay\Common\Helper as OmnipayHelper;

/**
 * This class contains the configuration information for the bundle.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class Configuration implements ConfigurationInterface
{

    /**
     * List of known gateways.
     *
     * @var array
     */
    private $gateways;

    public function __construct()
    {
        $this->gateways = OmnipayGatewayFactory::find();
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $rootNode = $builder->root('sylius_omnipay');

        $gateways = $this->getGateways();

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

    public function getGateways()
    {
        return $this->gateways;
    }

}
