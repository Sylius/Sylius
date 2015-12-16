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

/**
 * Doctrine listener used to manipulate mappings.
 *
 * @author Ivan Molchanov <ivan.molchanov@opensoftdev.ru>
 */
class LoadORMMetadataSubscriber implements EventSubscriber
{
    /**
     * @var array
     */
    protected $resources;

    /**
     * @param array $resources
     */
    public function __construct($resources)
    {
        $this->resources = $resources;
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

        $this->process($metadata);

        if (!$metadata->isMappedSuperclass) {
            $this->setAssociationMappings($metadata, $eventArgs->getEntityManager()->getConfiguration());
        } else {
            $this->unsetAssociationMappings($metadata);
        }
    }

    private function process(ClassMetadataInfo $metadata)
    {
        foreach ($this->resources as $application => $resources) {
            foreach ($resources as $resource => $resourceParameters) {
                $classes = $resourceParameters['classes'];

                if (isset($classes['model']) && $classes['model'] === $metadata->getName()) {
                    $metadata->isMappedSuperclass = false;

                    if (isset($classes['repository'])) {
                        $metadata->setCustomRepositoryClass($classes['repository']);
                    }
                }

                if (isset($resourceParameters['translation'])) {
                    $translationClasses = $resourceParameters['translation']['classes'];

                    if (isset($translationClasses['model']) && $translationClasses['model'] === $metadata->getName()) {
                        $metadata->isMappedSuperclass = false;

                        if (isset($translationClasses['repository'])) {
                            $metadata->setCustomRepositoryClass($translationClasses['repository']);
                        }
                    }

                }
            }
        }
    }

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

    private function unsetAssociationMappings(ClassMetadataInfo $metadata)
    {
        foreach ($metadata->getAssociationMappings() as $key => $value) {
            if ($this->hasRelation($value['type'])) {
                unset($metadata->associationMappings[$key]);
            }
        }
    }

    private function hasRelation($type)
    {
        return in_array(
            $type,
            array(
                ClassMetadataInfo::MANY_TO_MANY,
                ClassMetadataInfo::ONE_TO_MANY,
                ClassMetadataInfo::ONE_TO_ONE,
            ),
            true
        );
    }
}
