<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\Doctrine;

use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Repository\TranslatableRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;
use Sylius\Bundle\ResourceBundle\Form\Type\DefaultResourceType;
use Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\Form\Builder\DefaultFormBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\EventListener\NameResolverListener;
use Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\EventListener\DefaultParentListener;
use Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\EventListener\NameFilterListener;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Arnaud Langlade <aRn0D.dev@gmail.com>
 */
class DoctrinePHPCRDriver extends AbstractDoctrineDriver
{
    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, MetadataInterface $metadata)
    {
        parent::load($container, $metadata);
        $this->addResourceListeners($container, $metadata);
    }

    /**
     * @param ContainerBuilder $container
     * @param MetadataInterface $metadata
     */
    protected function addResourceListeners(ContainerBuilder $container, MetadataInterface $metadata)
    {
        $options = array_merge(
            [
                'default_parent_path' => null,
                'autocreate' => false,
                'force' => false,
                'name_filter' => true,
                'name_resolver' => true,
            ],
            $metadata->getParameter('options')
        );

        $createEventName = sprintf('%s.%s.pre_%s', $metadata->getApplicationName(), $metadata->getName(), 'create');
        $updateEventName = sprintf('%s.%s.pre_%s', $metadata->getApplicationName(), $metadata->getName(), 'update');

        if ($options['default_parent_path']) {
            $defaultPath = new Definition(DefaultParentListener::class);
            $defaultPath->setArguments([
                new Reference($metadata->getServiceId('manager')),
                $options['default_parent_path'],
                $options['autocreate'],
                $options['force']
            ]);
            $defaultPath->addTag('kernel.event_listener', [
                'event' => $createEventName,
                'method' => 'onPreCreate'
            ]);

            $container->setDefinition(
                sprintf(
                    '%s.resource.%s.doctrine.odm.phpcr.event_listener.default_path',
                    $metadata->getApplicationName(),
                    $metadata->getName()
                ),
                $defaultPath
            );
        }

        if ($options['name_filter']) {
            $nameFilter = new Definition(NameFilterListener::class);
            $nameFilter->setArguments([
                new Reference($metadata->getServiceId('manager'))
            ]);
            $nameFilter->addTag('kernel.event_listener', [
                'event' => $createEventName,
                'method' => 'onEvent'
            ]);
            $nameFilter->addTag('kernel.event_listener', [
                'event' => $updateEventName,
                'method' => 'onEvent'
            ]);

            $container->setDefinition(
                sprintf(
                    '%s.resource.%s.doctrine.odm.phpcr.event_listener.name_filter',
                    $metadata->getApplicationName(),
                    $metadata->getName()
                ),
                $nameFilter
            );
        }

        if ($options['name_resolver']) {
            $nameResolver = new Definition(NameResolverListener::class);
            $nameResolver->setArguments([
                new Reference($metadata->getServiceId('manager'))
            ]);
            $nameResolver->addTag('kernel.event_listener', [
                'event' => $createEventName,
                'method' => 'onEvent'
            ]);
            $nameResolver->addTag('kernel.event_listener', [
                'event' => $updateEventName,
                'method' => 'onEvent'
            ]);

            $container->setDefinition(
                sprintf(
                    '%s.resource.%s.doctrine.odm.phpcr.event_listener.name_resolver',
                    $metadata->getApplicationName(),
                    $metadata->getName()
                ),
                $nameResolver
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return SyliusResourceBundle::DRIVER_DOCTRINE_PHPCR_ODM;
    }

    /**
     * {@inheritdoc}
     */
    protected function addRepository(ContainerBuilder $container, MetadataInterface $metadata)
    {
        $repositoryClass = new Parameter('sylius.phpcr_odm.repository.class');

        if ($metadata->hasClass('repository')) {
            $repositoryClass = $metadata->getClass('repository');
        }

        $definition = new Definition($repositoryClass);
        $definition->setArguments([
            new Reference($metadata->getServiceId('manager')),
            $this->getClassMetadataDefinition($metadata),
        ]);

        if ($metadata->hasParameter('translation')) {
            $translationConfig = $metadata->getParameter('translation');

            if (in_array(TranslatableRepositoryInterface::class, class_implements($repositoryClass))) {
                if (isset($translationConfig['fields'])) {
                    $definition->addMethodCall('setTranslatableFields', [$translationConfig['fields']]);
                }
            }
        }

        $container->setDefinition($metadata->getServiceId('repository'), $definition);
    }

    /**
     * {@inheritdoc}
     */
    protected function addDefaultForm(ContainerBuilder $container, MetadataInterface $metadata)
    {
        $builderDefinition = new Definition(DefaultFormBuilder::class);
        $builderDefinition->setArguments([
            new Reference($metadata->getServiceId('manager'))
        ]);

        $definition = new Definition(DefaultResourceType::class);
        $definition
            ->setArguments([
                $this->getMetdataDefinition($metadata),
                $builderDefinition,
            ])
            ->addTag('form.type', [
                'alias' => sprintf('%s_%s', $metadata->getApplicationName(), $metadata->getName())
            ])
        ;

        $container->setDefinition(sprintf(
            '%s.form.type.%s',
            $metadata->getApplicationName(),
            $metadata->getName()
        ), $definition);
    }

    /**
     * {@inheritdoc}
     */
    protected function getManagerServiceId(MetadataInterface $metadata)
    {
        if ($objectManagerName = $this->getObjectManagerName($metadata)) {
            return sprintf('doctrine_phpcr.odm.%s_document_manager', $objectManagerName);
        }

        return 'doctrine_phpcr.odm.document_manager';
    }

    /**
     * {@inheritdoc}
     */
    protected function getClassMetadataClassname()
    {
        return 'Doctrine\\ODM\\PHPCR\\Mapping\\ClassMetadata';
    }
}
