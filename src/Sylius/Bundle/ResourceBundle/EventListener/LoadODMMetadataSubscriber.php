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
            'loadClassMetadata',
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
            $this->setAssociationMappings($metadata, $eventArgs->getDocumentManager()->getConfiguration());
        } else {
            $this->unsetAssociationMappings($metadata);
        }
    }

    private function setCustomRepositoryClasses(ClassMetadataInfo $metadata)
    {
        foreach ($this->classes as $application => $classes) {
            foreach ($classes as $class) {
                if (isset($class['model']) && $class['model'] === $metadata->getName()) {
                    $metadata->isMappedSuperclass = false;
                    if (isset($class['repository'])) {
                        $metadata->setCustomRepositoryClass($class['repository']);
                    }
                }
            }
        }
    }

    private function setAssociationMappings(ClassMetadataInfo $metadata, $configuration)
    {
        foreach (class_parents($metadata->getName()) as $parent) {
            $parentMetadata = new ClassMetadata($parent);
            if (in_array($parent, $configuration->getMetadataDriverImpl()->getAllClassNames())) {
                $configuration->getMetadataDriverImpl()->loadMetadataForClass($parent, $parentMetadata);
                if ($parentMetadata->isMappedSuperclass) {
                    foreach ($parentMetadata->associationMappings as $key => $value) {
                        if ($this->hasRelation($value['association'])) {
                            $metadata->associationMappings[$key] = $value;
                        }
                    }
                }
            }
        }
    }

    private function unsetAssociationMappings(ClassMetadataInfo $metadata)
    {
        foreach ($metadata->associationMappings as $key => $value) {
            if ($this->hasRelation($value['association'])) {
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
