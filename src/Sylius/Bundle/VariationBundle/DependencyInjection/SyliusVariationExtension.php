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
use Sylius\Bundle\VariationBundle\Form\Type\OptionValueChoiceType;
use Sylius\Bundle\VariationBundle\Form\Type\OptionValueCollectionType;
use Sylius\Bundle\VariationBundle\Form\Type\VariantChoiceType;
use Sylius\Bundle\VariationBundle\Form\Type\VariantMatchType;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Archetype extension.
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
        $config = $this->processConfiguration($this->getConfiguration($config, $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->registerResources('sylius', $config['driver'], $this->resolveResources($config['resources'], $container), $container);

        foreach ($config['resources'] as $variable => $parameters) {
            $this->createvariableServices($container, $variable);
        }

        foreach ($config['resources'] as $variableName => $variableConfig) {
            foreach ($variableConfig as $resourceName => $resourceConfig) {
                if (!is_array($resourceConfig)) {
                    continue;
                }

                $formDefinition = $container->getDefinition('sylius.form.type.'.$variableName.'_'.$resourceName);
                $formDefinition->addArgument($variableName);

                if (isset($resourceConfig['translation'])) {
                    $formTranslationDefinition = $container->getDefinition('sylius.form.type.'.$variableName.'_'.$resourceName.'_translation');
                    $formTranslationDefinition->addArgument($variableName);
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
        $variables = [];

        foreach ($resources as $variable => $parameters) {
            $variables[$variable] = $parameters;
        }

        $container->setParameter('sylius.variation.variables', $variables);

        $resolvedResources = [];

        foreach ($resources as $variableName => $variableConfig) {
            foreach ($variableConfig as $resourceName => $resourceConfig) {
                if (is_array($resourceConfig)) {
                    $resolvedResources[$variableName.'_'.$resourceName] = $resourceConfig;
                }
            }
        }

        return $resolvedResources;
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

        $variantChoiceFormType = new Definition(VariantChoiceType::class);
        $variantChoiceFormType
            ->setArguments([$variable])
            ->addTag('form.type', ['alias' => sprintf('sylius_%s_choice', $variantAlias)])
        ;

        $container->setDefinition('sylius.form.type.'.$variantAlias.'_choice', $variantChoiceFormType);

        $variantMatchFormType = new Definition(VariantMatchType::class);
        $variantMatchFormType
            ->setArguments([$variable])
            ->addTag('form.type', ['alias' => sprintf('sylius_%s_match', $variantAlias)])
        ;

        $container->setDefinition('sylius.form.type.'.$variantAlias.'_match', $variantMatchFormType);

        $optionValueChoiceFormType = new Definition(OptionValueChoiceType::class);
        $optionValueChoiceFormType
            ->setArguments([$variable])
            ->addTag('form.type', ['alias' => 'sylius_'.$optionValueAlias.'_choice'])
        ;

        $container->setDefinition('sylius.form.type.'.$optionValueAlias.'_choice', $optionValueChoiceFormType);

        $optionValueCollectionFormType = new Definition(OptionValueCollectionType::class);
        $optionValueCollectionFormType
            ->setArguments([$variable])
            ->addTag('form.type', ['alias' => 'sylius_'.$optionValueAlias.'_collection'])
        ;

        $container->setDefinition('sylius.form.type.'.$optionValueAlias.'_collection', $optionValueCollectionFormType);
    }
}
