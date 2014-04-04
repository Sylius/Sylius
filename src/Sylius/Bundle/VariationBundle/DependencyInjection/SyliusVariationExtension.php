<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\VariationBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\AbstractResourceExtension;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Sylius product catalog system container extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusVariationExtension extends AbstractResourceExtension
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
        $variables = array();

        foreach ($config['classes'] as $variable => $parameters) {
            $variables[$variable] = $parameters;
            unset($parameters['variable']);

            foreach ($parameters as $resource => $classes) {
                $convertedConfig[$variable.'_'.$resource] = $classes;
            }

            $this->createvariableServices($container, $config['driver'], $variable, $convertedConfig);

            if (!isset($config['validation_groups'][$variable]['variant'])) {
                $config['validation_groups'][$variable]['variant'] = array('sylius');
            }
            if (!isset($config['validation_groups'][$variable]['option'])) {
                $config['validation_groups'][$variable]['option'] = array('sylius');
            }
            if (!isset($config['validation_groups'][$variable]['option_value'])) {
                $config['validation_groups'][$variable]['option_value'] = array('sylius');
            }
        }

        $container->setParameter('sylius.variation.variables', $variables);

        $config['classes'] = $convertedConfig;
        $convertedConfig = array();

        foreach ($config['validation_groups'] as $variable => $parameters) {
            foreach ($parameters as $resource => $validationGroups) {
                $convertedConfig[$variable.'_'.$resource] = $validationGroups;
            }
        }

        $config['validation_groups'] = $convertedConfig;

        return $config;
    }

    /**
     * Create services for every variable object.
     *
     * @param ContainerBuilder $container
     * @param string           $driver
     * @param string           $variable
     * @param array            $config
     */
    private function createVariableServices(ContainerBuilder $container, $driver, $variable, array $config)
    {
        $variantAlias = $variable.'_variant';
        $optionAlias = $variable.'_option';
        $optionValueAlias = $variable.'_option_value';

        $variantClasses = $config[$variantAlias];
        $optionClasses = $config[$optionAlias];
        $optionValueClasses = $config[$optionValueAlias];

        $variantFormType = new Definition($variantClasses['form']);
        $variantFormType
            ->setArguments(array($variable, $variantClasses['model'], '%sylius.validation_group.'.$variantAlias.'%'))
            ->addTag('form.type', array('alias' => 'sylius_'.$variantAlias))
        ;

        $container->setDefinition('sylius.form.type.'.$variantAlias, $variantFormType);

        $variantChoiceFormType = new Definition('Sylius\Bundle\VariationBundle\Form\Type\VariantChoiceType');
        $variantChoiceFormType
            ->setArguments(array($variable))
            ->addTag('form.type', array('alias' => sprintf('sylius_%s_choice', $variantAlias)))
        ;

        $container->setDefinition('sylius.form.type.'.$variantAlias.'_choice', $variantChoiceFormType);

        $variantMatchFormType = new Definition('Sylius\Bundle\VariationBundle\Form\Type\VariantMatchType');
        $variantMatchFormType
            ->setArguments(array($variable, $variantClasses['model'], '%sylius.validation_group.'.$variantAlias.'%'))
            ->addTag('form.type', array('alias' => sprintf('sylius_%s_match', $variantAlias)))
        ;

        $container->setDefinition('sylius.form.type.'.$variantAlias.'_match', $variantMatchFormType);

        $optionFormType = new Definition($optionClasses['form']);
        $optionFormType
            ->setArguments(array($variable, $optionClasses['model'], '%sylius.validation_group.'.$optionAlias.'%'))
            ->addTag('form.type', array('alias' => 'sylius_'.$optionAlias))
        ;

        $container->setDefinition('sylius.form.type.'.$optionAlias, $optionFormType);

        $choiceTypeClasses = array(
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM => 'Sylius\Bundle\VariationBundle\Form\Type\OptionEntityChoiceType'
        );

        $optionChoiceFormType = new Definition($choiceTypeClasses[$driver]);
        $optionChoiceFormType
            ->setArguments(array($variable, $optionClasses['model']))
            ->addTag('form.type', array('alias' => 'sylius_'.$optionAlias.'_choice'))
        ;

        $container->setDefinition('sylius.form.type.'.$optionAlias.'_choice', $optionChoiceFormType);

        $optionValueFormType = new Definition($optionValueClasses['form']);
        $optionValueFormType
            ->setArguments(array($variable, $optionValueClasses['model'], '%sylius.validation_group.'.$optionValueAlias.'%'))
            ->addTag('form.type', array('alias' => 'sylius_'.$optionValueAlias))
        ;

        $container->setDefinition('sylius.form.type.'.$optionValueAlias, $optionValueFormType);

        $optionValueChoiceFormType = new Definition('Sylius\Bundle\VariationBundle\Form\Type\OptionValueChoiceType');
        $optionValueChoiceFormType
            ->setArguments(array($variable))
            ->addTag('form.type', array('alias' => 'sylius_'.$optionValueAlias.'_choice'))
        ;

        $container->setDefinition('sylius.form.type.'.$optionValueAlias.'_choice', $optionValueChoiceFormType);

        $optionValueCollectionFormType = new Definition('Sylius\Bundle\VariationBundle\Form\Type\OptionValueCollectionType');
        $optionValueCollectionFormType
            ->setArguments(array($variable))
            ->addTag('form.type', array('alias' => 'sylius_'.$optionValueAlias.'_collection'))
        ;

        $container->setDefinition('sylius.form.type.'.$optionValueAlias.'_collection', $optionValueCollectionFormType);
    }
}
