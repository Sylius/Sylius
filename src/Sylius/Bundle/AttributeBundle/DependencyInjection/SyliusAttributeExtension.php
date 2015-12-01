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

        foreach ($config['resources'] as $resource => $parameters) {
            $formDefinition = $container->getDefinition('sylius.form.type.'.$resource);
            $formDefinition->addArgument($parameters['subject']);

            if (isset($parameters['translation'])) {
                $formTranslationDefinition = $container->getDefinition('sylius.form.type.'.$resource.'_translation');
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

        foreach ($config['resources'] as $resource => $parameters) {
            $subjects[$resource] = $parameters;
            unset($parameters['subject']);

            foreach ($parameters as $parameter => $classes) {
                $convertedConfig[$resource.'_'.$parameter] = $classes;
                $convertedConfig[$resource.'_'.$parameter]['subject'] = $resource;

                if (!isset($classes['validation_groups'])) {
                    $classes['validation_groups']['default'] = array('sylius');
                }
            }
        }

        $container->setParameter('sylius.attribute.subjects', $subjects);

        $config['resources'] = $convertedConfig;

        return parent::process($config, $container);
    }
}
