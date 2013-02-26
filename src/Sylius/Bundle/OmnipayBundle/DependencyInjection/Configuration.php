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
    public static $gateways = array(
        'authorizenet',
        'cardsafe',
        'dummy',
        'gocardless',
        'paypal',
        'payflow',
        'paymentexpress',
        'pin',
        'stripe',
        'twocheckout',
        'worldpay',
    );

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $builder  = new TreeBuilder();
        $rootNode = $builder->root('sylius_payments');

        $rootNode
            ->children()
                ->arrayNode('gateways')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('type')
                                ->validate()
                                    ->ifTrue(function($type) {
                                        if (empty($type)) {
                                            return true;
                                        }

                                        if (!in_array(strtolower($type), self::$gateways)) {
                                            return true;
                                        }

                                        return false;
                                    })
                                    ->thenInvalid('Unknown payment gateway selected "%s".')
                                ->end()
                                ->validate()
                                    ->ifTrue(function($type) {
                                        return true; // fake =)
                                    })
                                    ->then(function($type) {
                                        return $this->translateTypeName($type);
                                    })
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

    private function translateTypeName($type)
    {
        switch ($type) {
            case 'authorizenet':
                $class = 'AuthorizeNet';
                break;
            case 'cardsafe':
                $class = 'CardSafe';
                break;
            case 'dummy':
                $class = 'Dummy';
                break;
            case 'gocardless':
                $class = 'GoCardless';
                break;
            case 'paypal':
                $class = 'PayPal';
                break;
            case 'payflow':
                $class = 'Payflow';
                break;
            case 'paymentexpress':
                $class = 'PaymentExpress';
                break;
            case 'pin':
                $class = 'Pin';
                break;
            case 'stripe':
                $class = 'Stripe';
                break;
            case 'twocheckout':
                $class = 'TwoCheckout';
                break;
            case 'worldpay':
                $class = 'WorldPay';
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Unknown payment gateway selected "%s".', $type));
        }

        return $class;
    }
}
