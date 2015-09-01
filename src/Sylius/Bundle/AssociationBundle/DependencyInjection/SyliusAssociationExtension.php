<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AssociationBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class SyliusAssociationExtension extends AbstractResourceExtension
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

            if (!isset($config['validation_groups'][$subject]['association'])) {
                $config['validation_groups'][$subject]['association'] = array('sylius');
            }
            if (!isset($config['validation_groups'][$subject]['association_type'])) {
                $config['validation_groups'][$subject]['association_type'] = array('sylius');
            }
        }

        $container->setParameter('sylius.association.subjects', $subjects);

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
