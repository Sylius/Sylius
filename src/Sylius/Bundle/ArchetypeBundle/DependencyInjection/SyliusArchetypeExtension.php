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

use Sylius\Bundle\ResourceBundle\DependencyInjection\AbstractResourceExtension;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
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
        $this->configure(
            $config,
            new Configuration(),
            $container,
            self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS | self::CONFIGURE_VALIDATORS
        );
    }

    /**
     * {@inheritdoc}
     */
    public function process(array $config, ContainerBuilder $container)
    {
        $subjects = array();
        $convertedConfig = array();

        foreach ($config['classes'] as $subject => $parameters) {
            $subjects[$subject] = $parameters;
            unset($parameters['subject'], $parameters['attribute'], $parameters['option']);

            foreach ($parameters as $resource => $classes) {
                $convertedConfig[$subject.'_'.$resource] = $classes;
            }

            $this->createSubjectServices($container, $config['driver'], $subject, $convertedConfig);

            if (!isset($config['validation_groups'][$subject]['archetype'])) {
                $config['validation_groups'][$subject]['archetype'] = array('sylius');
            }
        }

        $container->setParameter('sylius.archetype.subjects', $subjects);

        $config['classes'] = $convertedConfig;
        $config['validation_groups'] = $this->buildValidationConfig($config);

        return $config;
    }

    /**
     * Create services for every subject.
     *
     * @param ContainerBuilder $container
     * @param string           $driver
     * @param string           $subject
     * @param array            $config
     */
    private function createSubjectServices(ContainerBuilder $container, $driver, $subject, array $config)
    {
        $archetypeAlias = $subject.'_archetype';

        $archetypeClasses = $config[$archetypeAlias];

        $archetypeFormType = new Definition($archetypeClasses['form']);
        $archetypeFormType
            ->setArguments(array($archetypeClasses['model'], '%sylius.validation_group.'.$archetypeAlias.'%', $subject))
            ->addTag('form.type', array('alias' => 'sylius_'.$archetypeAlias))
        ;

        $container->setDefinition('sylius.form.type.'.$archetypeAlias, $archetypeFormType);

        $choiceTypeClasses = array(
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM => 'Sylius\Bundle\ArchetypeBundle\Form\Type\ArchetypeEntityChoiceType'
        );

        $archetypeChoiceFormType = new Definition($choiceTypeClasses[$driver]);
        $archetypeChoiceFormType
            ->setArguments(array($subject, $archetypeClasses['model']))
            ->addTag('form.type', array('alias' => 'sylius_'.$archetypeAlias.'_choice'))
        ;

        $container->setDefinition('sylius.form.type.'.$archetypeAlias.'_choice', $archetypeChoiceFormType);

        $builder = new Definition('Sylius\Component\Archetype\Builder\ArchetypeBuilder');
        $builder
            ->setArguments(array(new Reference(sprintf('sylius.repository.%s_attribute_value', $subject))))
        ;

        $container->setDefinition('sylius.builder.'.$archetypeAlias, $builder);
    }

    /**
     * @param array $config
     * @return array
     */
    private function buildValidationConfig(array $config)
    {
        $validationConfig = array();
        foreach ($config['validation_groups'] as $subject => $parameters) {
            foreach ($parameters as $resource => $validationGroups) {
                $validationConfig[$subject . '_' . $resource] = $validationGroups;
            }
        }
        return $validationConfig;
    }
}
