<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\RbacBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle.
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_rbac');

        $rootNode
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
                ->scalarNode('authorization_checker')->defaultValue('sylius.authorization_checker.default')->end()
                ->scalarNode('identity_provider')->defaultValue('sylius.authorization_identity_provider.security')->end()
                ->scalarNode('permission_map')->defaultValue('sylius.permission_map.cached')->end()
                ->arrayNode('security_roles')
                    ->useAttributeAsKey('id')
                    ->prototype('scalar')
                    ->defaultValue(array())
                ->end()
            ->end()
        ;

        $this->addValidationGroupsSection($rootNode);
        $this->addClassesSection($rootNode);
        $this->addRolesSection($rootNode);
        $this->addPermissionsSection($rootNode);

        return $treeBuilder;
    }

    /**
     * Adds `validation_groups` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addValidationGroupsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('validation_groups')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('role')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                        ->arrayNode('permission')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Adds `classes` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addClassesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('classes')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('role')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\Component\Rbac\Model\Role')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end()
                                ->scalarNode('repository')->end()
                                ->arrayNode('form')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('default')->defaultValue('Sylius\Bundle\RbacBundle\Form\Type\RoleType')->end()
                                        ->scalarNode('choice')->defaultValue('%sylius.form.type.resource_choice.class%')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('permission')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\Component\Rbac\Model\Permission')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end()
                                ->scalarNode('repository')->end()
                                ->arrayNode('form')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('default')->defaultValue('Sylius\Bundle\RbacBundle\Form\Type\PermissionType')->end()
                                        ->scalarNode('choice')->defaultValue('%sylius.form.type.resource_choice.class%')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Adds `roles` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addRolesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('roles')
                    ->useAttributeAsKey('id')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('name')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('description')->end()
                            ->arrayNode('permissions')
                                ->prototype('scalar')->end()
                            ->end()
                            ->arrayNode('security_roles')
                                ->prototype('scalar')->end()
                                ->defaultValue(array())
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('roles_hierarchy')
                    ->useAttributeAsKey('id')
                    ->prototype('array')
                        ->beforeNormalization()->ifString()->then(function ($v) { return array('value' => $v); })->end()
                        ->beforeNormalization()
                            ->ifTrue(function ($v) { return is_array($v) && isset($v['value']); })
                            ->then(function ($v) { return preg_split('/\s*,\s*/', $v['value']); })
                        ->end()
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Adds `permissions` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addPermissionsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('permissions')
                    ->useAttributeAsKey('id')
                    ->prototype('scalar')->end()
                    ->defaultValue(array())
                ->end()
                ->arrayNode('permissions_hierarchy')
                    ->useAttributeAsKey('id')
                    ->prototype('array')
                        ->performNoDeepMerging()
                        ->beforeNormalization()->ifString()->then(function ($v) { return array('value' => $v); })->end()
                        ->beforeNormalization()
                            ->ifTrue(function ($v) { return is_array($v) && isset($v['value']); })
                            ->then(function ($v) { return preg_split('/\s*,\s*/', $v['value']); })
                        ->end()
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
