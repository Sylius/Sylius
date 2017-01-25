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

use Sylius\Component\Resource\Factory\Factory;
use Sylius\Component\Resource\Factory\TranslatableFactoryInterface;
use Sylius\Component\Resource\Metadata\Metadata;
use Sylius\Component\Resource\Metadata\MetadataInterface;
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
    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, MetadataInterface $metadata)
    {
        $this->setClassesParameters($container, $metadata);

        if ($metadata->hasClass('controller')) {
            $this->addController($container, $metadata);
        }

        $this->addManager($container, $metadata);
        $this->addRepository($container, $metadata);

        if ($metadata->hasClass('factory')) {
            $this->addFactory($container, $metadata);
        }

        if ($metadata->hasClass('form')) {
            $container->setParameter(
                sprintf('%s.form.type.%s.validation_groups', $metadata->getApplicationName(), $metadata->getName()),
                $metadata->getParameter('validation_groups')
            );
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param MetadataInterface $metadata
     */
    protected function setClassesParameters(ContainerBuilder $container, MetadataInterface $metadata)
    {
        if ($metadata->hasClass('model')) {
            $container->setParameter(sprintf('%s.model.%s.class', $metadata->getApplicationName(), $metadata->getName()), $metadata->getClass('model'));
        }
        if ($metadata->hasClass('controller')) {
            $container->setParameter(sprintf('%s.controller.%s.class', $metadata->getApplicationName(), $metadata->getName()), $metadata->getClass('controller'));
        }
        if ($metadata->hasClass('factory')) {
            $container->setParameter(sprintf('%s.factory.%s.class', $metadata->getApplicationName(), $metadata->getName()), $metadata->getClass('factory'));
        }
        if ($metadata->hasClass('repository')) {
            $container->setParameter(sprintf('%s.repository.%s.class', $metadata->getApplicationName(), $metadata->getName()), $metadata->getClass('repository'));
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param MetadataInterface $metadata
     */
    protected function addController(ContainerBuilder $container, MetadataInterface $metadata)
    {
        $definition = new Definition($metadata->getClass('controller'));
        $definition
            ->setArguments([
                $this->getMetadataDefinition($metadata),
                new Reference('sylius.resource_controller.request_configuration_factory'),
                new Reference('sylius.resource_controller.view_handler'),
                new Reference($metadata->getServiceId('repository')),
                new Reference($metadata->getServiceId('factory')),
                new Reference('sylius.resource_controller.new_resource_factory'),
                new Reference($metadata->getServiceId('manager')),
                new Reference('sylius.resource_controller.single_resource_provider'),
                new Reference('sylius.resource_controller.resources_collection_provider'),
                new Reference('sylius.resource_controller.form_factory'),
                new Reference('sylius.resource_controller.redirect_handler'),
                new Reference('sylius.resource_controller.flash_helper'),
                new Reference('sylius.resource_controller.authorization_checker'),
                new Reference('sylius.resource_controller.event_dispatcher'),
                new Reference('sylius.resource_controller.state_machine'),
            ])
            ->addMethodCall('setContainer', [new Reference('service_container')])
        ;

        $container->setDefinition($metadata->getServiceId('controller'), $definition);
    }

    /**
     * @param ContainerBuilder $container
     * @param MetadataInterface $metadata
     */
    protected function addFactory(ContainerBuilder $container, MetadataInterface $metadata)
    {
        $factoryClass = $metadata->getClass('factory');
        $modelClass = $metadata->getClass('model');

        $definition = new Definition($factoryClass);

        $definitionArgs = [$modelClass];
        if (in_array(TranslatableFactoryInterface::class, class_implements($factoryClass))) {
            $decoratedDefinition = new Definition(Factory::class);
            $decoratedDefinition->setArguments($definitionArgs);

            $definitionArgs = [$decoratedDefinition, new Reference('sylius.translation_locale_provider')];
        }

        $definition->setArguments($definitionArgs);

        $container->setDefinition($metadata->getServiceId('factory'), $definition);
    }

    /**
     * @param MetadataInterface $metadata
     *
     * @return Definition
     */
    protected function getMetadataDefinition(MetadataInterface $metadata)
    {
        $definition = new Definition(Metadata::class);
        $definition
            ->setFactory([new Reference('sylius.resource_registry'), 'get'])
            ->setArguments([$metadata->getAlias()])
        ;

        return $definition;
    }

    /**
     * @param ContainerBuilder $container
     * @param MetadataInterface $metadata
     */
    abstract protected function addManager(ContainerBuilder $container, MetadataInterface $metadata);

    /**
     * @param ContainerBuilder $container
     * @param MetadataInterface $metadata
     */
    abstract protected function addRepository(ContainerBuilder $container, MetadataInterface $metadata);
}
