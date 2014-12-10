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
use Doctrine\ODM\MongoDB\Event\LoadClassMetadataEventArgs;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadataInfo;

/**
 * Doctrine listener used to manipulate mappings.
 *
 * @author Ivannis Suárez Jérez <ivannis.suarez@gmail.com>
 */
class LoadODMMetadataSubscriber implements EventSubscriber
{
    /**
     * @var array
     */
    protected $classes;

    /**
     * @var array
     */
    private $savedAssociations = array();

    /**
     * Constructor
     *
     * @param array $classes
     */
    public function __construct(array $classes)
    {
        $this->classes = $classes;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'loadClassMetadata'
        );
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        /** @var ClassMetadata $metadata */
        $metadata = $eventArgs->getClassMetadata();

        $this->setCustomRepositoryClasses($metadata);

        if (!$metadata->isMappedSuperclass) {
            $this->setAssociationMappings($metadata);
        } else {
            $this->unsetAssociationMappings($metadata);
        }
    }

    private function setCustomRepositoryClasses(ClassMetadataInfo $metadata)
    {
        foreach ($this->classes as $class) {
            if (array_key_exists('model', $class) && $class['model'] === $metadata->getName()) {
                $metadata->isMappedSuperclass = false;
                if (array_key_exists('repository', $class)) {
                    $metadata->setCustomRepositoryClass($class['repository']);
                }
            }
        }
    }

    private function setAssociationMappings(ClassMetadataInfo $metadata)
    {
        foreach (class_parents($metadata->getName()) as $parent) {
            if (in_array($parent, array_keys($this->savedAssociations))) {
                foreach ($this->savedAssociations[$parent] as $key => $mapping) {
                    $metadata->associationMappings[$key] = $mapping;
                }
            }
        }
    }

    private function unsetAssociationMappings(ClassMetadataInfo $metadata)
    {
        foreach ($metadata->associationMappings as $key => $value) {
            if ($this->hasRelation($value['association'])) {
                if (!isset($this->savedAssociations[$metadata->getName()])) {
                    $this->savedAssociations[$metadata->getName()] = array();
                }
                $this->savedAssociations[$metadata->getName()][$key] = $metadata->associationMappings[$key];
                unset($metadata->associationMappings[$key]);
            }
        }
    }

    private function hasRelation($type)
    {
        return in_array(
            $type,
            array(
                ClassMetadataInfo::REFERENCE_ONE,
                ClassMetadataInfo::REFERENCE_MANY,
                ClassMetadataInfo::EMBED_ONE,
                ClassMetadataInfo::EMBED_MANY,
            ),
            true
        );
    }
}
