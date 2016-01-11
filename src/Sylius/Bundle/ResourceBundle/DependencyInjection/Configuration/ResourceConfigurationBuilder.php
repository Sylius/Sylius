<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Configuration;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ResourceConfigurationBuilder implements ResourceConfigurationBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function initResourcesConfiguration(ArrayNodeDefinition $bundleRootDefinition)
    {
        $resourcesDefinition = $bundleRootDefinition
            ->children()
            ->arrayNode('resources')
            ->fixXmlConfig('resource')
        ;

        return $resourcesDefinition;
    }

    /**
     * {@inheritdoc}
     */
    public function addSyliusResource(ArrayNodeDefinition $resourcesDefinition, SyliusResource $syliusResource)
    {
        $resourceDefinition = $resourcesDefinition->children()->arrayNode($syliusResource->getName())->addDefaultsIfNotSet();

        $this->addOptionsDefinition($resourceDefinition, $syliusResource);
        $this->addClassesDefinition($resourceDefinition, $syliusResource);
        $this->addValidationGroupsDefinition($resourceDefinition, $syliusResource);
        $this->addTranslationResourceDefinition($resourceDefinition, $syliusResource);

        return $resourceDefinition;
    }

    /**
     * @param ArrayNodeDefinition $resourceDefinition
     * @param AbstractSyliusResource $syliusResource
     */
    private function addOptionsDefinition(ArrayNodeDefinition $resourceDefinition, AbstractSyliusResource $syliusResource)
    {
        $optionsDefinition = $resourceDefinition->children()->variableNode('options');

        $options = $syliusResource->getOptions();
        if (0 === count($options)) {
            return;
        }

        $optionsDefinition->defaultValue($options);
    }

    /**
     * @param ArrayNodeDefinition $resourceDefinition
     * @param AbstractSyliusResource $syliusResource
     */
    private function addClassesDefinition(ArrayNodeDefinition $resourceDefinition, AbstractSyliusResource $syliusResource)
    {
        $classesDefinition = $resourceDefinition->children()->arrayNode('classes')->addDefaultsIfNotSet();

        $this->addModelDefinition($classesDefinition, $syliusResource);
        $this->addInterfaceDefinition($classesDefinition, $syliusResource);
        $this->addControllerDefinition($classesDefinition, $syliusResource);
        $this->addRepositoryDefinition($classesDefinition, $syliusResource);
        $this->addFactoryDefinition($classesDefinition, $syliusResource);
        $this->addFormsDefinitions($classesDefinition, $syliusResource);
    }

    /**
     * @param ArrayNodeDefinition $resourceDefinition
     * @param AbstractSyliusResource $syliusResource
     */
    private function addValidationGroupsDefinition(ArrayNodeDefinition $resourceDefinition, AbstractSyliusResource $syliusResource)
    {
        if (0 === count($syliusResource->getValidationGroups())) {
            return;
        }

        $validationGroupsNodeBuilder = $resourceDefinition
            ->children()
            ->arrayNode('validation_groups')
            ->addDefaultsIfNotSet()
            ->children()
        ;

        foreach ($syliusResource->getValidationGroups() as $name => $validationGroups) {
            $validationGroupsDefinition = $validationGroupsNodeBuilder->arrayNode($name);
            $validationGroupsDefinition->defaultValue($validationGroups);
            $validationGroupsDefinition->prototype('scalar');
        }
    }

    /**
     * @param ArrayNodeDefinition $resourceDefinition
     * @param SyliusResource $syliusResource
     */
    private function addTranslationResourceDefinition(ArrayNodeDefinition $resourceDefinition, SyliusResource $syliusResource)
    {
        if (null === $syliusResource->getTranslationResource()) {
            return;
        }

        $syliusTranslationResource = $syliusResource->getTranslationResource();
        $translationDefinition = $resourceDefinition->children()->arrayNode('translation')->addDefaultsIfNotSet();

        $this->addOptionsDefinition($translationDefinition, $syliusTranslationResource);
        $this->addClassesDefinition($translationDefinition, $syliusTranslationResource);
        $this->addValidationGroupsDefinition($translationDefinition, $syliusTranslationResource);
        $this->addTranslatableFieldsDefinition($translationDefinition, $syliusTranslationResource);
    }

    /**
     * @param ArrayNodeDefinition $translationResourceDefinition
     * @param SyliusTranslationResource $syliusTranslationResource
     */
    private function addTranslatableFieldsDefinition(ArrayNodeDefinition $translationResourceDefinition, SyliusTranslationResource $syliusTranslationResource)
    {
        if (0 === count($syliusTranslationResource->getTranslatableFields())) {
            return;
        }

        $translatableFieldsDefinition = $translationResourceDefinition->children()->arrayNode('fields');
        $translatableFieldsDefinition->defaultValue($syliusTranslationResource->getTranslatableFields());
        $translatableFieldsDefinition->prototype('scalar');
    }

    /**
     * @param ArrayNodeDefinition $classesDefinition
     * @param AbstractSyliusResource $syliusResource
     */
    private function addModelDefinition(ArrayNodeDefinition $classesDefinition, AbstractSyliusResource $syliusResource)
    {
        $modelDefinition = $classesDefinition->children()->scalarNode('model')->cannotBeEmpty();

        if (null === $syliusResource->getModelClass()) {
            $modelDefinition->isRequired();
            return;
        }

        $modelDefinition->defaultValue($syliusResource->getModelClass());
    }

    /**
     * @param ArrayNodeDefinition $classesDefinition
     * @param AbstractSyliusResource $syliusResource
     */
    private function addInterfaceDefinition(ArrayNodeDefinition $classesDefinition, AbstractSyliusResource $syliusResource)
    {
        $interfaceDefinition = $classesDefinition->children()->scalarNode('interface')->cannotBeEmpty();

        if (null === $syliusResource->getInterfaceClass()) {
            return;
        }

        $interfaceDefinition->defaultValue($syliusResource->getInterfaceClass());
    }

    /**
     * @param ArrayNodeDefinition $classesDefinition
     * @param AbstractSyliusResource $syliusResource
     */
    private function addControllerDefinition(ArrayNodeDefinition $classesDefinition, AbstractSyliusResource $syliusResource)
    {
        if (null === $syliusResource->getControllerClass()) {
            return;
        }

        $classesDefinition
            ->children()
            ->scalarNode('controller')
            ->defaultValue($syliusResource->getControllerClass())
            ->cannotBeEmpty()
        ;
    }

    /**
     * @param ArrayNodeDefinition $classesDefinition
     * @param AbstractSyliusResource $syliusResource
     */
    private function addRepositoryDefinition(ArrayNodeDefinition $classesDefinition, AbstractSyliusResource $syliusResource)
    {
        if (null === $syliusResource->getRepositoryClass()) {
            return;
        }

        $classesDefinition
            ->children()
            ->scalarNode('repository')
            ->defaultValue($syliusResource->getRepositoryClass())
            ->cannotBeEmpty()
        ;
    }

    /**
     * @param ArrayNodeDefinition $classesDefinition
     * @param AbstractSyliusResource $syliusResource
     */
    private function addFactoryDefinition(ArrayNodeDefinition $classesDefinition, AbstractSyliusResource $syliusResource)
    {
        if (null === $syliusResource->getFactoryClass()) {
            return;
        }

        $classesDefinition
            ->children()
            ->scalarNode('factory')
            ->defaultValue($syliusResource->getFactoryClass())
            ->cannotBeEmpty()
        ;
    }

    /**
     * @param ArrayNodeDefinition $classesDefinition
     * @param AbstractSyliusResource $syliusResource
     */
    private function addFormsDefinitions(ArrayNodeDefinition $classesDefinition, AbstractSyliusResource $syliusResource)
    {
        if (0 === count($syliusResource->getFormsClasses())) {
            return;
        }

        $formsNodeBuilder = $classesDefinition
            ->children()
            ->arrayNode('form') // TODO: Rename to forms?
            ->addDefaultsIfNotSet()
            ->children()
        ;

        foreach ($syliusResource->getFormsClasses() as $name => $formClass) {
            $formsNodeBuilder
                ->scalarNode($name)
                ->defaultValue($formClass)
                ->cannotBeEmpty()
            ;
        }
    }
}
