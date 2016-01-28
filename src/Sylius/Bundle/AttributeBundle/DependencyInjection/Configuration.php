<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\DependencyInjection;

use Sylius\Bundle\AttributeBundle\Controller\AttributeController;
use Sylius\Bundle\AttributeBundle\Form\Type\AttributeTranslationType;
use Sylius\Bundle\AttributeBundle\Form\Type\AttributeType;
use Sylius\Bundle\AttributeBundle\Form\Type\AttributeValueType;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Configuration\ResourceConfigurationBuilder;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Configuration\ResourceConfigurationBuilderInterface;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Configuration\SyliusResource;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Configuration\SyliusTranslationResource;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Bundle\TranslationBundle\Doctrine\ORM\TranslatableResourceRepository;
use Sylius\Component\Attribute\Model\Attribute;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Attribute\Model\AttributeTranslation;
use Sylius\Component\Attribute\Model\AttributeTranslationInterface;
use Sylius\Component\Translation\Factory\TranslatableFactory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle.
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_attribute');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
            ->end()
        ;

        $this->addResourcesSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $rootDefinition
     */
    private function addResourcesSection(ArrayNodeDefinition $rootDefinition)
    {
        $resourceConfigurationGenerator = new ResourceConfigurationBuilder();

        $resourcesDefinition = $resourceConfigurationGenerator->initResourcesConfiguration($rootDefinition);
        $resourcesDefinition->useAttributeAsKey('name');

        /** @var ArrayNodeDefinition $resourcesPrototypeDefinition */
        $resourcesPrototypeDefinition = $resourcesDefinition->prototype('array');
        $resourcesPrototypeDefinition->children()->scalarNode('subject')->isRequired();

        $this->addAttributeResource($resourceConfigurationGenerator, $resourcesPrototypeDefinition);
        $this->addAttributeValueResource($resourceConfigurationGenerator, $resourcesPrototypeDefinition);
    }

    /**
     * @param ResourceConfigurationBuilderInterface $resourceConfigurationGenerator
     * @param ArrayNodeDefinition $resourcesDefinition
     */
    private function addAttributeResource(
        ResourceConfigurationBuilderInterface $resourceConfigurationGenerator,
        ArrayNodeDefinition $resourcesDefinition
    ) {
        $attributeResource = new SyliusResource('attribute', Attribute::class, AttributeInterface::class);
        $attributeResource->useController(AttributeController::class);
        $attributeResource->useDefaultRepository();
        $attributeResource->useDefaultTranslatableFactory();
        $attributeResource->addForm('default', AttributeType::class, ['sylius']);
        $attributeResource->addForm('choice', ResourceChoiceType::class);

        $attributeTranslationResource = new SyliusTranslationResource(AttributeTranslation::class, AttributeTranslationInterface::class);
        $attributeTranslationResource->useDefaultController();
        $attributeTranslationResource->useDefaultRepository();
        $attributeTranslationResource->useDefaultFactory();
        $attributeTranslationResource->addForm('default', AttributeTranslationType::class, ['sylius']);
        $attributeTranslationResource->setTranslatableFields(['name']);

        $attributeResource->useTranslationResource($attributeTranslationResource);

        $resourceConfigurationGenerator->addSyliusResource($resourcesDefinition, $attributeResource);
    }

    /**
     * @param ResourceConfigurationBuilderInterface $resourceConfigurationGenerator
     * @param ArrayNodeDefinition $resourcesDefinition
     */
    private function addAttributeValueResource(
        ResourceConfigurationBuilderInterface $resourceConfigurationGenerator,
        ArrayNodeDefinition $resourcesDefinition
    ) {
        $attributeValueResource = new SyliusResource('attribute_value', null, null);
        $attributeValueResource->useDefaultController();
        $attributeValueResource->useDefaultRepository();
        $attributeValueResource->useDefaultFactory();
        $attributeValueResource->addForm('default', AttributeValueType::class, ['sylius']);

        $resourceConfigurationGenerator->addSyliusResource($resourcesDefinition, $attributeValueResource);
    }
}
