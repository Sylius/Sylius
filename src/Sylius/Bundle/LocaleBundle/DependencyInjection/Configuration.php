<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\LocaleBundle\DependencyInjection;

use Sylius\Bundle\LocaleBundle\Controller\LocaleController;
use Sylius\Bundle\LocaleBundle\Form\Type\LocaleChoiceType;
use Sylius\Bundle\LocaleBundle\Form\Type\LocaleType;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Configuration\ResourceConfigurationBuilder;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Configuration\SyliusResource;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Locale\Model\Locale;
use Sylius\Component\Locale\Model\LocaleInterface;
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
        $rootNode = $treeBuilder->root('sylius_locale');

        $rootNode
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
                ->scalarNode('storage')->defaultValue('sylius.storage.session')->end()
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

        $localeResource = new SyliusResource('locale', Locale::class, LocaleInterface::class);
        $localeResource->useController(LocaleController::class);
        $localeResource->useDefaultRepository();
        $localeResource->useDefaultFactory();
        $localeResource->addForm('default', LocaleType::class, ['sylius']);
        $localeResource->addForm('choice', LocaleChoiceType::class);

        $resourceConfigurationGenerator->addSyliusResource($resourcesDefinition, $localeResource);
    }
}
