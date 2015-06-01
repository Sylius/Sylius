<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_web');

        $this->addTemplatesSection($rootNode);

        return $treeBuilder;
    }

    /**
     * Adds `templates` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addTemplatesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('templates')
                    ->addDefaultsIfNotSet()
                    ->info('Templates used in the web bundle')
                    ->children()

                        // Frontend Templates
                        ->scalarNode('frontend_homepage')->defaultValue('SyliusWebBundle:Frontend/Homepage:main.html.twig')->end()
                        ->scalarNode('frontend_cart_summary')->defaultValue('SyliusWebBundle:Frontend/Cart:summary.html.twig')->end()
                        ->scalarNode('frontend_account')->defaultValue('SyliusWebBundle:Frontend/Account:show.html.twig')->end()
                        ->scalarNode('frontend_address_index')->defaultValue('SyliusWebBundle:Frontend/Account:Address/index.html.twig')->end()
                        ->scalarNode('frontend_address_create')->defaultValue('SyliusWebBundle:Frontend/Account:Address/create.html.twig')->end()
                        ->scalarNode('frontend_address_update')->defaultValue('SyliusWebBundle:Frontend/Account:Address/update.html.twig')->end()
                        ->scalarNode('frontend_order_index')->defaultValue('SyliusWebBundle:Frontend/Account:Order/index.html.twig')->end()
                        ->scalarNode('frontend_order_show')->defaultValue('SyliusWebBundle:Frontend/Account:Order/show.html.twig')->end()
                        ->scalarNode('frontend_order_invoice')->defaultValue('SyliusWebBundle:Frontend/Account:Order/invoice.html.twig')->end()

                        // Backend Templates
                        ->scalarNode('backend_dashboard')->defaultValue('SyliusWebBundle:Backend/Dashboard:main.html.twig')->end()
                        ->scalarNode('backend_login')->defaultValue('SyliusWebBundle:Backend/Security:login.html.twig')->end()

                    ->end()
                ->end()
            ->end()
        ;
    }
}
