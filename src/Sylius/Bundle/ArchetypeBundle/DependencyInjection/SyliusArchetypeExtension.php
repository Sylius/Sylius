<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ArchetypeBundle\DependencyInjection;

use Sylius\Bundle\ArchetypeBundle\Doctrine\ORM\ArchetypeRepository;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Archetype\Builder\ArchetypeBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Archetype extension.
 *
 * @author Adam Elsodaney <adam.elso@gmail.com>
 */
class SyliusArchetypeExtension extends AbstractResourceExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration($config, $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if (SyliusResourceBundle::DRIVER_DOCTRINE_ORM === $config['driver']) {
            $config = $this->defineDefaultOrmRepository($config);
        }

        $this->registerResources('sylius', $config['driver'], $this->resolveResources($config['resources'], $container), $container);

        foreach ($config['resources'] as $subjectName => $subjectConfig) {
            foreach ($subjectConfig as $resourceName => $resourceConfig) {
                if (!is_array($resourceConfig)) {
                    continue;
                }

                $formDefinition = $container->getDefinition('sylius.form.type.'.$subjectName.'_'.$resourceName);
                $formDefinition->addArgument($subjectName);

                if (isset($resourceConfig['translation'])) {
                    $formTranslationDefinition = $container->getDefinition('sylius.form.type.'.$subjectName.'_'.$resourceName.'_translation');
                    $formTranslationDefinition->addArgument($subjectName);
                }
            }
        }

        $configFiles = [
            'services.xml',
        ];

        foreach ($configFiles as $configFile) {
            $loader->load($configFile);
        }
    }

    /**
     * Resolve resources for every subject.
     *
     * @param array $resources
     * @param ContainerBuilder $container
     *
     * @return array
     */
    private function resolveResources(array $resources, ContainerBuilder $container)
    {
        $subjects = [];

        foreach ($resources as $subject => $parameters) {
            $subjects[$subject] = $parameters;
        }

        $container->setParameter('sylius.archetype.subjects', $subjects);

        $resolvedResources = [];

        foreach ($resources as $subjectName => $subjectConfig) {
            $this->createPrototypeBuilder($container, $subjectName);

            foreach ($subjectConfig as $resourceName => $resourceConfig) {
                if (is_array($resourceConfig)) {
                    $resolvedResources[$subjectName.'_'.$resourceName] = $resourceConfig;
                }
            }
        }

        return $resolvedResources;
    }

    /**
     * Create prototype builder for subject.
     *
     * @param ContainerBuilder $container
     * @param string $subjectName
     */
    private function createPrototypeBuilder(ContainerBuilder $container, $subjectName)
    {
        $builderDefinition = new Definition(ArchetypeBuilder::class);
        $builderDefinition
            ->setArguments([new Reference(sprintf('sylius.factory.%s_attribute_value', $subjectName))])
        ;

        $container->setDefinition('sylius.builder.'.$subjectName.'_archetype', $builderDefinition);
    }

    /**
     * @param array $config
     *
     * @return array
     */
    private function defineDefaultOrmRepository(array $config)
    {
        foreach ($config['resources'] as $subjectName => $subjectConfig) {
            if (!isset($subjectConfig['archetype']['classes']['repository'])) {
                $config['resources'][$subjectName]['archetype']['classes']['repository'] = ArchetypeRepository::class;
            }
        }

        return $config;
    }
}
