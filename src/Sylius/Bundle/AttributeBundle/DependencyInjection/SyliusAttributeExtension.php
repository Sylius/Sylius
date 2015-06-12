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

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Attribute extension.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusAttributeExtension extends AbstractResourceExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->configure(
            $config, new Configuration(),
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
            unset($parameters['subject']);

            foreach ($parameters as $resource => $classes) {
                $convertedConfig[$subject.'_'.$resource] = $classes;
                $convertedConfig[$subject.'_'.$resource]['subject'] = $subject;
            }

            if (!isset($config['validation_groups'][$subject]['attribute'])) {
                $config['validation_groups'][$subject]['attribute'] = array('sylius');
            }
            if (!isset($config['validation_groups'][$subject]['attribute_translation'])) {
                $config['validation_groups'][$subject]['attribute_translation'] = array('sylius');
            }
            if (!isset($config['validation_groups'][$subject]['attribute_value'])) {
                $config['validation_groups'][$subject]['attribute_value'] = array('sylius');
            }
        }

        $container->setParameter('sylius.attribute.subjects', $subjects);

        $config['classes'] = $convertedConfig;
        $convertedConfig = array();

        foreach ($config['validation_groups'] as $subject => $parameters) {
            foreach ($parameters as $resource => $validationGroups) {
                $convertedConfig[$subject.'_'.$resource] = $validationGroups;
            }
        }

        $config['validation_groups'] = $convertedConfig;

        return parent::process($config, $container);
    }
}
