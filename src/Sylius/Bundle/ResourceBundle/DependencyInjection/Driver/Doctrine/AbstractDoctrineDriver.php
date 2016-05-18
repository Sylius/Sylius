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

use Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\AbstractDriver;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Arnaud Langlade <aRn0D.dev@gmail.com>
 */
abstract class AbstractDoctrineDriver extends AbstractDriver
{
    /**
     * @param MetadataInterface $metadata
     *
     * @return Definition
     */
    protected function getClassMetadataDefinition(MetadataInterface $metadata)
    {
        $definition = new Definition($this->getClassMetadataClassname());
        $definition
            ->setFactory([new Reference($this->getManagerServiceId($metadata)), 'getClassMetadata'])
            ->setArguments([$metadata->getClass('model')])
            ->setPublic(false)
        ;

        return $definition;
    }

    /**
     * {@inheritdoc}
     */
    protected function addManager(ContainerBuilder $container, MetadataInterface $metadata)
    {
        $container->setAlias(
            $metadata->getServiceId('manager'),
            new Alias($this->getManagerServiceId($metadata))
        );
    }

    /**
     * Return the configured object managre name, or NULL if the default
     * manager should be used.
     *
     * @param MetadataInterface $metadata
     *
     * @return string|null
     */
    protected function getObjectManagerName(MetadataInterface $metadata)
    {
        $objectManagerName = null;

        if ($metadata->hasParameter('options') && isset($metadata->getParameter('options')['object_manager'])) {
            $objectManagerName = $metadata->getParameter('options')['object_manager'];
        }

        return $objectManagerName;
    }

    /**
     * @param MetadataInterface $metadata
     *
     * @return string
     */
    abstract protected function getManagerServiceId(MetadataInterface $metadata);

    /**
     * @return string
     */
    abstract protected function getClassMetadataClassname();
}
