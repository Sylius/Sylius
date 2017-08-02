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

namespace Sylius\Bundle\ResourceBundle\EventListener;

use Doctrine\ODM\MongoDB\Event\LoadClassMetadataEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadataInfo;

/**
 * Doctrine listener used to manipulate mappings.
 *
 * @author Ivannis Suárez Jérez <ivannis.suarez@gmail.com>
 */
final class ODMMappedSuperClassSubscriber extends AbstractDoctrineSubscriber
{
    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata,
        ];
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $metadata = $eventArgs->getClassMetadata();

        $this->convertToDocumentIfNeeded($metadata);

        if (!$metadata->isMappedSuperclass) {
            $this->setAssociationMappings($metadata, $eventArgs->getDocumentManager()->getConfiguration());
        } else {
            $this->unsetAssociationMappings($metadata);
        }
    }

    /**
     * @param ClassMetadataInfo $metadata
     */
    private function convertToDocumentIfNeeded(ClassMetadataInfo $metadata)
    {
        if (false === $metadata->isMappedSuperclass) {
            return;
        }

        try {
            $resourceMetadata = $this->resourceRegistry->getByClass($metadata->getName());
        } catch (\InvalidArgumentException $exception) {
            return;
        }

        if ($metadata->getName() === $resourceMetadata->getClass('model')) {
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
            if (false === in_array($parent, $configuration->getMetadataDriverImpl()->getAllClassNames())) {
                continue;
            }

            $parentMetadata = new ClassMetadata(
                $parent,
                $configuration->getNamingStrategy()
            );

            // Wakeup Reflection
            $parentMetadata->wakeupReflection($this->getReflectionService());

            // Load Metadata
            $configuration->getMetadataDriverImpl()->loadMetadataForClass($parent, $parentMetadata);

            if (false === $this->isResource($parentMetadata)) {
                continue;
            }

            if ($parentMetadata->isMappedSuperclass) {
                foreach ($parentMetadata->associationMappings as $key => $value) {
                    if ($this->isRelation($value['association']) && !isset($metadata->associationMappings[$key])) {
                        $metadata->associationMappings[$key] = $value;
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
        if (false === $this->isResource($metadata)) {
            return;
        }

        foreach ($metadata->associationMappings as $key => $value) {
            if ($this->isRelation($value['association'])) {
                unset($metadata->associationMappings[$key]);
            }
        }
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    private function isRelation($type)
    {
        return in_array(
            $type,
            [
                ClassMetadataInfo::REFERENCE_ONE,
                ClassMetadataInfo::REFERENCE_MANY,
                ClassMetadataInfo::EMBED_ONE,
                ClassMetadataInfo::EMBED_MANY,
            ],
            true
        );
    }
}
