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
            $this->addForms($container, $metadata);
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

        if (!$metadata->hasParameter('validation_groups')) {
            return;
        }

        $validationGroups = $metadata->getParameter('validation_groups');

        foreach ($validationGroups as $formName => $groups) {
            $suffix = 'default' === $formName ? '' : sprintf('_%s', $formName);
            $container->setParameter(sprintf('%s.validation_groups.%s%s', $metadata->getApplicationName(), $metadata->getName(), $suffix), array_merge(['Default'], $groups));
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
                $this->getMetdataDefinition($metadata),
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

        if (in_array(TranslatableFactoryInterface::class, class_implements($factoryClass))) {
            $decoratedDefinition = new Definition(Factory::class);
            $decoratedDefinition->setArguments([$modelClass]);

            $definition->setArguments([$decoratedDefinition, new Reference('sylius.translation.locale_provider')]);

            $container->setDefinition($metadata->getServiceId('factory'), $definition);

            return;
        }

        $definition->setArguments([$modelClass]);

        $container->setDefinition($metadata->getServiceId('factory'), $definition);
    }

    /**
     * @param ContainerBuilder $container
     * @param MetadataInterface $metadata
     */
    protected function addForms(ContainerBuilder $container, MetadataInterface $metadata)
    {
        foreach ($metadata->getClass('form') as $formName => $formClass) {
            $suffix = 'default' === $formName ? '' : sprintf('_%s', $formName);
            $alias = sprintf('%s_%s%s', $metadata->getApplicationName(), $metadata->getName(), $suffix);

            $definition = new Definition($formClass);

            switch ($formName) {
                case 'choice':
                    $definition->setArguments([
                        $metadata->getClass('model'),
                        $metadata->getDriver(),
                        $alias,
                    ]);
                break;

                default:
                    $validationGroupsParameterName = sprintf('%s.validation_groups.%s%s', $metadata->getApplicationName(), $metadata->getName(), $suffix);
                    $validationGroups = new Parameter($validationGroupsParameterName);

                    if (!$container->hasParameter($validationGroupsParameterName)) {
                        $validationGroups = ['Default'];
                    }

                    $definition->setArguments([
                        $metadata->getClass('model'),
                        $validationGroups,
                    ]);
                break;
            }

            $definition->addTag('form.type', ['alias' => $alias]);

            $container->setParameter(sprintf('%s.form.type.%s%s.class', $metadata->getApplicationName(), $metadata->getName(), $suffix), $formClass);
            $container->setDefinition(
                sprintf('%s.form.type.%s%s', $metadata->getApplicationName(), $metadata->getName(), $suffix),
                $definition
            );
        }

        if (!$container->hasDefinition(sprintf('%s.form.type.%s', $metadata->getApplicationName(), $metadata->getName()))) {
            $this->addDefaultForm($container, $metadata);
        }
    }

    /**
     * @param MetadataInterface $metadata
     *
     * @return Definition
     */
    protected function getMetdataDefinition(MetadataInterface $metadata)
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
    abstract protected function addDefaultForm(ContainerBuilder $container, MetadataInterface $metadata);

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
