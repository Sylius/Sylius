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

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
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
        $config = $this->configure(
            $config,
            new Configuration(),
            $container,
            self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS | self::CONFIGURE_VALIDATORS | self::CONFIGURE_TRANSLATIONS | self::CONFIGURE_FORMS
        );

        foreach ($config['classes'] as $name => $parameters) {
            $formDefinition = $container->getDefinition('sylius.form.type.'.$name);
            $formDefinition->addArgument($parameters['subject']);

            if (isset($parameters['translation'])) {
                $formTranslationDefinition = $container->getDefinition('sylius.form.type.'.$name.'_translation');
                $formTranslationDefinition->addArgument($parameters['subject']);
            }
        }
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
                $convertedConfig[$subject.'_'.$resource]['subject'] = $subject;
            }

            $this->createSubjectServices($container, $subject);

            if (!isset($config['validation_groups'][$subject]['archetype'])) {
                $config['validation_groups'][$subject]['archetype'] = array('sylius');
            }
            if (!isset($config['validation_groups'][$subject]['archetype_translation'])) {
                $config['validation_groups'][$subject]['archetype_translation'] = array('sylius');
            }
        }

        $container->setParameter('sylius.archetype.subjects', $subjects);

        $config['classes'] = $convertedConfig;
        $config['validation_groups'] = $this->buildValidationConfig($config);

        return parent::process($config, $container);
    }

    /**
     * Create services for every subject.
     *
     * @param ContainerBuilder $container
     * @param string           $subject
     */
    private function createSubjectServices(ContainerBuilder $container, $subject)
    {
        $builderDefintion = new Definition('Sylius\Component\Archetype\Builder\ArchetypeBuilder');
        $builderDefintion
            ->setArguments(array(new Reference(sprintf('sylius.repository.%s_attribute_value', $subject))))
        ;

        $container->setDefinition('sylius.builder.'.$subject.'_archetype', $builderDefintion);
    }

    /**
     * @param array $config
     *
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
