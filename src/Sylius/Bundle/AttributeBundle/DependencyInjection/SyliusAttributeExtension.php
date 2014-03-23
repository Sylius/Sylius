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

use Sylius\Bundle\ResourceBundle\DependencyInjection\AbstractResourceExtension;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Sylius attributes system extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusAttributeExtension extends AbstractResourceExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $this->configure($config, new Configuration(), $container, self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS | self::CONFIGURE_VALIDATORS);
    }

    /**
     * {@inheritdoc}
     */
    public function process(array $config, ContainerBuilder $container)
    {
        $convertedConfig = array();
        $subjects = array();

        foreach ($config['classes'] as $subject => $parameters) {
            $subjects[] = $parameters;
            unset($parameters['subject']);

            foreach ($parameters as $resource => $classes) {
                $convertedConfig[$subject.'_'.$resource] = $classes;
            }

            $this->createSubjectServices($container, $config['driver'], $subject, $convertedConfig);

            if (!isset($config['validation_groups'][$subject]['attribute'])) {
                $config['validation_groups'][$subject]['attribute'] = array('sylius');
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
        $attributeAlias = $subject.'_'.'attribute';
        $attributeValueAlias = $subject.'_'.'attribute_value';

        $attributeClasses = $config[$attributeAlias];
        $attributeValueClasses = $config[$attributeValueAlias];

        $attributeFormType = new Definition($attributeClasses['form']);
        $attributeFormType
            ->setArguments(array($subject, $attributeClasses['model'], '%sylius.validation_group.'.$attributeAlias.'%'))
            ->addTag('form.type', array('alias' => 'sylius_'.$attributeAlias))
        ;

        $container->setDefinition('sylius.form.type.'.$attributeAlias, $attributeFormType);

        $choiceTypeClasses = array(
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM => 'Sylius\Bundle\AttributeBundle\Form\Type\AttributeEntityChoiceType'
        );

        $attributeChoiceFormType = new Definition($choiceTypeClasses[$driver]);
        $attributeChoiceFormType
            ->setArguments(array($subject, $attributeClasses['model']))
            ->addTag('form.type', array('alias' => 'sylius_'.$attributeAlias.'_choice'))
        ;

        $container->setDefinition('sylius.form.type.'.$attributeAlias.'_choice', $attributeChoiceFormType);

        $attributeValueFormType = new Definition($attributeValueClasses['form']);
        $attributeValueFormType
            ->setArguments(array($subject, $attributeValueClasses['model'], '%sylius.validation_group.'.$attributeValueAlias.'%'))
            ->addTag('form.type', array('alias' => 'sylius_'.$attributeValueAlias))
        ;

        $container->setDefinition('sylius.form.type.'.$attributeValueAlias, $attributeValueFormType);
    }
}
