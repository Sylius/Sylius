<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ArchetypeBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Doctrine listener used to manipulate mappings.
 *
 * @author Adam Elsodaney <adam.elso@gmail.com>
 */
class LoadMetadataSubscriber implements EventSubscriber
{
    /**
     * @var array
     */
    protected $subjects;

    /**
     * Constructor
     *
     * @param array $subjects
     */
    public function __construct(array $subjects)
    {
        $this->subjects = $subjects;
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
        $metadata = $eventArgs->getClassMetadata();

        foreach ($this->subjects as $subject => $class) {
            if ($class['archetype']['model'] !== $metadata->getName()) {
                continue;
            }

            $this->mapVariations($metadata, $class, $subject);
            $this->mapAttributes($metadata, $class, $subject);
            $this->mapParent($metadata, $class);
        }
    }


    /**
     * @param ClassMetadataInfo|ClassMetadata $metadata
     * @param array                           $class
     * @param string                          $subject
     */
    private function mapAttributes(ClassMetadataInfo $metadata, array $class, $subject)
    {
        $attributeMapping = array(
            'fieldName'    => 'attributes',
            'type'         => ClassMetadataInfo::MANY_TO_MANY,
            'targetEntity' => $class['attribute'],
            'joinTable'    => array(
                'name' => sprintf('sylius_%s_archetype_attribute', $subject),
                'joinColumns'   => array(array(
                    'name'                 => 'archetype_id',
                    'referencedColumnName' => 'id',
                    'nullable'             => false,
                    'unique'               => false,
                )),
                'inverseJoinColumns'   => array(array(
                    'name'                 => 'attribute_id',
                    'referencedColumnName' => 'id',
                    'nullable'             => false,
                    'unique'               => false,
                ))
            ),
        );

        $metadata->mapManyToMany($attributeMapping);
    }

    /**
     * @param ClassMetadataInfo|ClassMetadata $metadata
     * @param array                           $class
     * @param string                          $subject
     */
    private function mapVariations(ClassMetadataInfo $metadata, array $class, $subject)
    {
        $optionMapping = array(
            'fieldName'    => 'options',
            'type'         => ClassMetadataInfo::MANY_TO_MANY,
            'targetEntity' => $class['option'],
            'joinTable'    => array(
                'name' => sprintf('sylius_%s_archetype_option', $subject),
                'joinColumns'   => array(array(
                    'name'                 => sprintf('%s_archetype_id', $subject),
                    'referencedColumnName' => 'id',
                    'nullable'             => false,
                    'unique'               => false,
                )),
                'inverseJoinColumns'   => array(array(
                    'name'                 => 'option_id',
                    'referencedColumnName' => 'id',
                    'nullable'             => false,
                    'unique'               => false,
                ))
            ),
        );

        $metadata->mapManyToMany($optionMapping);
    }

    /**
     * @param ClassMetadataInfo|ClassMetadata $metadata
     * @param array                           $class
     */
    private function mapParent(ClassMetadataInfo $metadata, array $class)
    {
        $parentMapping = array(
            'fieldName'    => 'parent',
            'type'         => ClassMetadataInfo::MANY_TO_ONE,
            'targetEntity' => $class['archetype']['model'],
            'joinColumn'   => array(
                'name'                 => 'parent_id',
                'referencedColumnName' => 'id',
                'nullable'             => true,
                'onDelete'             => 'SET NULL'
            ),
        );

        $metadata->mapManyToOne($parentMapping);
    }
}
