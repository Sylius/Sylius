<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\Doctrine;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\AbstractDriver;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

abstract class AbstractDoctrineDriver extends AbstractDriver
{
    protected function getClassMetadataDefinition(MetadataInterface $metadata): Definition
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
    protected function addManager(ContainerBuilder $container, MetadataInterface $metadata): void
    {
        $container->setAlias(
            $metadata->getServiceId('manager'),
            new Alias($this->getManagerServiceId($metadata), true)
        );

        if (method_exists($container, 'registerAliasForArgument')) {
            $container->registerAliasForArgument(
                $metadata->getServiceId('manager'),
                ObjectManager::class,
                $metadata->getHumanizedName() . ' manager'
            );
        }
    }

    /**
     * Return the configured object managre name, or NULL if the default
     * manager should be used.
     */
    protected function getObjectManagerName(MetadataInterface $metadata): ?string
    {
        $objectManagerName = null;

        if ($metadata->hasParameter('options') && isset($metadata->getParameter('options')['object_manager'])) {
            $objectManagerName = $metadata->getParameter('options')['object_manager'];
        }

        return $objectManagerName;
    }

    abstract protected function getManagerServiceId(MetadataInterface $metadata): string;

    abstract protected function getClassMetadataClassname(): string;
}
