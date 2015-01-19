<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Driver;

use Sylius\Component\Resource\Metadata\ResourceMetadataInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Arnaud Langlade <aRn0D.dev@gmail.com>
 */
abstract class AbstractDriver implements DriverInterface
{
    const DEFAULT_KEY = 'default';

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, ResourceMetadataInterface $metadata)
    {
        $this->mapParameters($metadata, $container);

        if ($metadata->hasClass('controller')) {
            $container->setDefinition(
                $metadata->getServiceId('controller'),
                $this->getControllerDefinition($metadata, $container)
            );
        }

        $container->setDefinition(
            $metadata->getServiceId('manager'),
            $this->getManagerDefinition($metadata, $container)
        );
        $container->setDefinition(
            $metadata->getServiceId('repository'),
            $this->getRepositoryDefinition($metadata, $container)
        );
        $container->setDefinition(
            $metadata->getServiceId('factory'),
            $this->getFactoryDefinition($metadata, $container)
        );
        $container->setDefinition(
            $metadata->getServiceId('event_dispatcher'),
            $this->getEventDispatcherDefinition($metadata, $container)
        );

        $this->registerForms($metadata, $container);
    }

    /**
     * @param ResourceMetadataInterface $metadata
     *
     * @return Definition
     */
    protected function getControllerDefinition(ResourceMetadataInterface $metadata, ContainerBuilder $container)
    {
        $definition = new Definition($metadata->getClass('controller'));
        $definition
            ->addMethodCall('setContainer', array(new Reference('service_container')))
            ->setArguments(array(
                $this->getMetadataDefinition($metadata, $container),
                new Reference('sylius.controller.configuration_factory'),
                new Reference($metadata->getServiceId('manager')),
                new Reference($metadata->getServiceId('repository')),
                new Reference($metadata->getServiceId('factory')),
                new Reference($metadata->getServiceId('event_dispatcher')),
                new Reference('sylius.controller.form_factory'),
                new Reference('sm.factory'),
                new Reference('fos_rest.view_handler'),
                new Reference('sylius.controller.redirect_handler'),
                new Reference('sylius.controller.parameters_parser'),
            ))
        ;

        return $definition;
    }

    /**
     * @param ResourceMetadataInterface $metadata
     *
     * @return Definition
     */
    protected function getMetadataDefinition(ResourceMetadataInterface $metadata, ContainerBuilder $container)
    {
        $definition = new Definition('Sylius\Component\Resource\Metadata\ResourceMetadata');
        $definition->setFactoryService('sylius.registry.resource');
        $definition->setFactoryMethod('get');
        $definition->setArguments(array($metadata->getAlias()));

        return $definition;
    }

    /**
     * @param ResourceMetadataInterface $metadata
     *
     * @return Definition
     */
    protected function getManagerDefinition(ResourceMetadataInterface $metadata, ContainerBuilder $container)
    {
        $definition = new Definition('Sylius\Bundle\ResourceBundle\Doctrine\ResourceManager');
        $definition->setArguments(array(new Reference($this->getObjectManagerId($metadata))));

        return $definition;
    }

    /**
     * @param ResourceMetadataInterface $metadata
     *
     * @return Definition
     */
    protected function getEventDispatcherDefinition(ResourceMetadataInterface $metadata, ContainerBuilder $container)
    {
        $definition = new Definition('Sylius\Component\Resource\EventDispatcher\ResourceEventDispatcher');
        $definition
            ->setArguments(array(
                $this->getMetadataDefinition($metadata, $container),
                new Reference('event_dispatcher')
            ))
        ;

        return $definition;
    }

    /**
     * Get the respository service definition.
     *
     * @param ResourceMetadataInterface $metadata
     *
     * @return Definition
     */
    protected function getRepositoryDefinition(ResourceMetadataInterface $metadata, ContainerBuilder $container)
    {
        $definition = new Definition($metadata->getClass('repository'));
        $reflection = new \ReflectionClass($definition->getClass());

        $translatableRepositoryInterface = 'Sylius\Component\Translation\Repository\TranslatableResourceRepositoryInterface';

        if (interface_exists($translatableRepositoryInterface) && $reflection->implementsInterface($translatableRepositoryInterface)) {
            $definition->addMethodCall('setLocaleProvider', array(new Reference('sylius.translation.locale_provider')));
            $definition->addMethodCall('setTranslatableFields', array($metadata->getClass('translation.mapping.fields')));
        }
    }

    /**
     * Get the factory service definition.
     *
     * @param ResourceMetadataInterface $metadata
     *
     * @return Definition
     */
    protected function getFactoryDefinition(ResourceMetadataInterface $metadata, ContainerBuilder $container)
    {
        $defaultFactoryClass = $metadata->isTranslatable() ? 'Sylius\Component\Translation\Factory\TranslatableResourceFactory' : 'Sylius\Component\Resource\Factory\ResourceFactory';
        $factoryClass = $metadata->hasClass('factory') ? $metadata->getClass('factory') : $defaultFactoryClass;

        $definition = new Definition($factoryClass);
        $reflection = new \ReflectionClass($factoryClass);

        $definition->setArguments(array($metadata->getClass('model')));

        if ($metadata->isTranslatable()) {
            $definition->addArgument(new Reference('sylius.translation.locale_provider'));
        }

        return $definition;
    }

    /**
     * @param ResourceMetadataInterface $metadata
     * @param ContainerBuilder $container
     */
    protected function mapParameters(ResourceMetadataInterface $metadata, ContainerBuilder $container)
    {
        $container->setParameter(sprintf('%s.model.%s.class', $metadata->getApplicationName(), $metadata->getResourceName()), $metadata->getClass('model'));

        if (!$metadata->hasParameter('validation_groups')) {
            return;
        }

        $validationGroups = $metadata->getParameter('validation_groups');

        foreach ($validationGroups as $formName => $groups) {
            $suffix = ($formName === self::DEFAULT_KEY ? '' : sprintf('_%s', $formName));

            $container->setParameter(sprintf('%s.validation_groups.%s%s', $metadata->getApplicationName(), $metadata->getResourceName(), $suffix), $groups);
        }
    }

    /**
     * @param ResourceMetadataInterface $metadata
     * @param ContainerBuilder $container
     */
    protected function registerForms(ResourceMetadataInterface $metadata, ContainerBuilder $container)
    {
        $parameters = $metadata->getParameters();
        $classes = $parameters['classes'];

        if (!isset($classes['form'])) {
            return;
        }

        //if ($this->isTranslationSupported() && isset($serviceClasses['translation'])) {
            //$this->registerFormTypes(array('classes' => array(sprintf('%s_translation', $model) => $serviceClasses['translation'])), $container);
        //}

        foreach ($classes['form'] as $formName => $class) {
            $suffix = ($formName === self::DEFAULT_KEY ? '' : sprintf('_%s', $formName));

            $alias = sprintf('%s_%s%s', $metadata->getApplicationName(), $metadata->getResourceName(), $suffix);
            $definition = new Definition($class);

            if ('choice' === $formName) {
                $definition->setArguments(array(
                    $classes['model'],
                    $metadata->getDriver(),
                    $alias,
                ));
            } else {
                $definition->setArguments(array(
                    $metadata->getClass('model'),
                    new Parameter(sprintf('%s.validation_groups.%s%s', $metadata->getApplicationName(), $metadata->getResourceName(), $suffix)),
                ));
            }

            $definition->addTag('form.type', array('alias' => $alias));

            $formId = sprintf('%s.form.type.%s%s', $metadata->getApplicationName(), $metadata->getResourceName(), $suffix);

            $container->setParameter($formId.'.class', $class);
            $container->setDefinition($formId, $definition);
        }
    }

    /**
     * Get the object manager service id.
     *
     * @param ResourceMetadataInterface $metadata
     *
     * @return string
     */
    abstract protected function getObjectManagerId(ResourceMetadataInterface $metadata);
}
