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

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Product catalog extension.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusVariationExtension extends AbstractResourceExtension
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

        foreach ($config['resources'] as $resource => $parameters) {
            $formDefinition = $container->getDefinition('sylius.form.type.'.$resource);
            $formDefinition->addArgument($parameters['variable']);

            if (isset($parameters['translation'])) {
                $formTranslationDefinition = $container->getDefinition('sylius.form.type.'.$resource.'_translation');
                $formTranslationDefinition->addArgument($parameters['variable']);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function process(array $config, ContainerBuilder $container)
    {
        $convertedConfig = array();
        $variables = array();

        foreach ($config['resources'] as $resource => $parameters) {
            $variables[$resource] = $parameters;
            unset($parameters['variable']);

            foreach ($parameters as $parameter => $classes) {
                $convertedConfig[$resource.'_'.$parameter] = $classes;
                $convertedConfig[$resource.'_'.$parameter]['variable'] = $resource;

                if (!isset($classes['validation_groups'])) {
                    $classes['validation_groups']['default'] = array('sylius');
                }
            }

            $this->createvariableServices($container, $resource);


        }

        $container->setParameter('sylius.variation.variables', $variables);

        $config['resources'] = $convertedConfig;

        return parent::process($config, $container);
    }

    /**
     * Create services for every variable object.
     *
     * @param ContainerBuilder $container
     * @param string           $variable
     */
    private function createVariableServices(ContainerBuilder $container, $variable)
    {
        $variantAlias = $variable.'_variant';
        $optionValueAlias = $variable.'_option_value';


        $variantChoiceFormType = new Definition('Sylius\Bundle\VariationBundle\Form\Type\VariantChoiceType');
        $variantChoiceFormType
            ->setArguments(array($variable))
            ->addTag('form.type', array('alias' => sprintf('sylius_%s_choice', $variantAlias)))
        ;

        $container->setDefinition('sylius.form.type.'.$variantAlias.'_choice', $variantChoiceFormType);

        $variantMatchFormType = new Definition('Sylius\Bundle\VariationBundle\Form\Type\VariantMatchType');
        $variantMatchFormType
            ->setArguments(array($variable))
            ->addTag('form.type', array('alias' => sprintf('sylius_%s_match', $variantAlias)))
        ;

        $container->setDefinition('sylius.form.type.'.$variantAlias.'_match', $variantMatchFormType);


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
