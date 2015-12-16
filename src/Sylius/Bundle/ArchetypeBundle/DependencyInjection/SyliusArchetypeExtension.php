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
            unset($parameters['subject'], $parameters['attribute'], $parameters['option']);

            foreach ($parameters as $parameter => $classes) {
                $convertedConfig[$resource.'_'.$parameter] = $classes;
                $convertedConfig[$resource.'_'.$parameter]['subject'] = $resource;

                if (!isset($classes['validation_groups'])) {
                    $classes['validation_groups']['default'] = array('sylius');
                }
            }

            $this->createSubjectServices($container, $resource);
        }

        $container->setParameter('sylius.archetype.subjects', $subjects);

        $config['resources'] = $convertedConfig;

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
            ->setArguments(array(new Reference(sprintf('sylius.factory.%s_attribute_value', $subject))))
        ;

        $container->setDefinition('sylius.builder.'.$subject.'_archetype', $builderDefintion);
    }
}
