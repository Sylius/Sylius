<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Sylius\Component\Resource\Metadata\RegistryInterface;

/**
 * @author Ivan Molchanov <ivan.molchanov@opensoftdev.ru>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class LoadORMMetadataSubscriber implements EventSubscriber
{
    /**
     * @var RegistryInterface
     */
    private $resourceRegistry;

    /**
     * @param RegistryInterface $resourceRegistry
     */
    public function __construct(RegistryInterface $resourceRegistry)
    {
        $this->resourceRegistry = $resourceRegistry;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'loadClassMetadata',
        ];
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        /** @var ClassMetadata $metadata */
        $metadata = $eventArgs->getClassMetadata();

        $this->convertToEntityIfNeeded($metadata);

        if (!$metadata->isMappedSuperclass) {
            $this->setAssociationMappings($metadata, $eventArgs->getEntityManager()->getConfiguration());
        } else {
            $this->unsetAssociationMappings($metadata);
        }
    }

    /**
     * @param ClassMetadataInfo $metadata
     */
    private function convertToEntityIfNeeded(ClassMetadataInfo $metadata)
    {
        foreach ($this->resourceRegistry->getAll() as $alias => $resourceMetadata) {
            if ($metadata->getName() !== $resourceMetadata->getClass('model')) {
                continue;
            }

            if ($resourceMetadata->hasClass('repository')) {
                $metadata->setCustomRepositoryClass($resourceMetadata->getClass('repository'));
            }

            $metadata->isMappedSuperclass = false;
        }
    }

    /**
     * @param ClassMetadataInfo $metadata
     * @param $configuration
     */
    private function setAssociationMappings(ClassMetadataInfo $metadata, $configuration)
    {
        foreach (class_parents($metadata->getName()) as $parent) {
            $parentMetadata = new ClassMetadata(
                $parent,
                $configuration->getNamingStrategy()
            );
            if (in_array($parent, $configuration->getMetadataDriverImpl()->getAllClassNames())) {
                $configuration->getMetadataDriverImpl()->loadMetadataForClass($parent, $parentMetadata);
                if ($parentMetadata->isMappedSuperclass) {
                    foreach ($parentMetadata->getAssociationMappings() as $key => $value) {
                        if ($this->hasRelation($value['type'])) {
                            $metadata->associationMappings[$key] = $value;
                        }
                    }
                }
            }
        }
    }

    /**
     * @param ClassMetadataInfo $metadata
     */
    private function unsetAssociationMappings(ClassMetadataInfo $metadata)
    {
        foreach ($metadata->getAssociationMappings() as $key => $value) {
            if ($this->hasRelation($value['type'])) {
                unset($metadata->associationMappings[$key]);
            }
        }
    }

    /**
     * @param $type
     *
     * @return bool
     */
    private function hasRelation($type)
    {
        return in_array(
            $type,
            [
                ClassMetadataInfo::MANY_TO_MANY,
                ClassMetadataInfo::ONE_TO_MANY,
                ClassMetadataInfo::ONE_TO_ONE,
            ],
            true
        );
    }
}
