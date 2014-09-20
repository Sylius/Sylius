<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Aleksey Bannov <a.s.bannov@gmail.com>
 */
abstract class AbstractResourceConfiguration implements ConfigurationInterface
{
    const DEFAULT_KEY = 'default';

    /**
     * @param ArrayNodeDefinition $node
     * @param null                $driver
     * @param null                $objectManager
     * @param array               $validationGroups
     *
     * @return $this
     */
    protected function addDefaults(
        ArrayNodeDefinition $node,
        $driver = null,
        $objectManager = null,
        array $validationGroups = array()
    ) {
        $node->append($this->createDriverNode($driver));
        $node->append($this->createObjectManagerNode($objectManager));
        $node->append($this->createTemplatesNode());
        $this->addValidationGroupsSection($node, $validationGroups);

        return $this;
    }

    /**
     * @param array $resources
     *
     * @return ArrayNodeDefinition
     */
    protected function createResourcesSection(array $resources = array())
    {
        $builder = new TreeBuilder();
        $node = $builder->root('classes');
        $node->addDefaultsIfNotSet();
        $resourceNodes = $node->children();
        foreach ($resources as $resource => $defaults){
            $resourceNode = $resourceNodes
                ->arrayNode($resource)
                ->addDefaultsIfNotSet()
            ;
            $this->addClassesSection($resourceNode, $defaults);
        }
        return $node;
    }

    /**
     * @param ArrayNodeDefinition $node
     * @param array               $defaults
     *
     * @return ArrayNodeDefinition
     */
    protected function addClassesSection(ArrayNodeDefinition $node, array $defaults = array())
    {
        $node
            ->children()
                ->scalarNode('model')
                    ->cannotBeEmpty()
                    ->defaultValue(isset($defaults['model']) ? $defaults['model'] : null)
                ->end()
                ->scalarNode('controller')
                    ->defaultValue(
                        isset($defaults['controller']) ? $defaults['controller'] : '%sylius.default.controller.class%'
                    )
                ->end()
                ->scalarNode('repository')
                    ->defaultValue(isset($defaults['repository']) ? $defaults['repository'] : null)
                ->end()
                ->scalarNode('interface')
                    ->defaultValue(isset($defaults['interface']) ? $defaults['interface'] : null)
                ->end()
                ->arrayNode('translatable_fields')
                    ->prototype('scalar')->end()
                    ->defaultValue(isset($defaults['translatable_fields']) ? $defaults['translatable_fields'] : array())
                ->end()
                ->append($this->createFormsNode(isset($defaults['form']) ? $defaults['form'] : null))
                ->end()
            ->end()
        ;

        return $node;
    }

    /**
     * @param string $default
     *
     * @return ScalarNodeDefinition
     */
    protected function createDriverNode($default = null)
    {
        $builder = new TreeBuilder();
        $node = $builder->root('driver', 'enum');

        if ($default){
            $node->defaultValue($default);
        }
        $node
            ->values(array(
                    SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
                    SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM,
                    SyliusResourceBundle::DRIVER_DOCTRINE_PHPCR_ODM,
                ))
            ->cannotBeEmpty()
            ->info(sprintf(
                'Database driver (%s, %s or %s)',
                SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
                SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM,
                SyliusResourceBundle::DRIVER_DOCTRINE_PHPCR_ODM
            ))
            ->end()
        ;

        return $node;
    }

    /**
     * @param string $default
     *
     * @return ScalarNodeDefinition
     */
    protected function createObjectManagerNode($default = 'default')
    {
        $builder = new TreeBuilder();
        $node = $builder->root('object_manager', 'scalar');

        if ($default){
            $node->defaultValue($default);
        }
        $node
            ->cannotBeEmpty()
            ->info('Name of object Manager')
            ->end();

        return $node;
    }

    /**
     * @param string $default
     *
     * @return ScalarNodeDefinition
     */
    protected function createTemplateNode($default = null)
    {
        $builder = new TreeBuilder();
        $node = $builder->root('templates', 'scalar');

        if ($default){
            $node->defaultValue($default);
        }
        $node
            ->info('Template namespace used by each resource')
            ->cannotBeEmpty()
        ->end();

        return $node;
    }

    /**
     * @return ArrayNodeDefinition
     */
    protected function createTemplatesNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('templates');
        $node
            ->useAttributeAsKey('name')
                ->prototype('scalar')->end()
            ->end();

        return $node;
    }

    /**
     * @param array  $default
     *
     * @return ArrayNodeDefinition
     */
    protected function createValidationGroupNode(array $default = array())
    {
        $builder = new TreeBuilder();
        $node = $builder->root('validation_group');
        $node
            ->info('Validation groups used by the form component')
            ->prototype('scalar')->defaultValue($default)->end()
        ;

        return $node;
    }

    /**
     * @param ArrayNodeDefinition $node
     * @param array               $validationGroups
     */
    protected function addValidationGroupsSection(ArrayNodeDefinition $node, array $validationGroups = array())
    {
        $child = $node
            ->children()
                ->arrayNode('validation_groups')
                    ->addDefaultsIfNotSet()
                    ->children();
                        foreach ($validationGroups as $name=>$groups){
                            $child
                                ->arrayNode($name)
                                ->prototype('scalar')->end()
                                ->defaultValue($groups)
                                ->end();
                        }
                        $child
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    protected function addTemplatesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('templates')
                    ->useAttributeAsKey('name')
                    ->prototype('scalar')
                ->end()
            ->end();
    }

    /**
     * @param array|string $classes
     *
     * @return ArrayNodeDefinition
     */
    protected function createFormsNode($classes)
    {
        $builder = new TreeBuilder();
        $node = $builder->root('form');

        if (is_string($classes)) {
            $classes = array(self::DEFAULT_KEY => $classes);
        }
        if (!isset($classes['choice'])) {
            $classes['choice'] = '%sylius.form.type.resource_choice.class%';
        }
        $node
            ->info('')
            ->defaultValue($classes)
            ->useAttributeAsKey('name')
            ->prototype('scalar')->end()
            ->beforeNormalization()
                ->ifString()
                ->then(function ($v) {
                        return array(
                            AbstractResourceConfiguration::DEFAULT_KEY => $v
                        );
                    })
            ->end()
        ;

        return $node;
    }
}
