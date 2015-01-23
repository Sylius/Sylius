<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Doctrine listener used to manipulate mappings.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
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
            'loadClassMetadata'
        );
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $metadata = $eventArgs->getClassMetadata();

        foreach ($this->subjects as $subject => $class) {
            if ($class['attribute_value']['model'] === $metadata->getName()) {
                $subjectMapping = array(
                    'fieldName'     => 'subject',
                    'targetEntity'  => $class['subject'],
                    'inversedBy'    => 'attributes',
                    'joinColumns'   => array(array(
                        'name'                 => $subject.'_id',
                        'referencedColumnName' => 'id',
                        'nullable'             => false,
                        'onDelete'             => 'CASCADE'
                    ))
                );

                $this->mapManyToOne($metadata, $subjectMapping);

                $attributeMapping = array(
                    'fieldName'     => 'attribute',
                    'targetEntity'  => $class['attribute']['model'],
                    'joinColumns'   => array(array(
                        'name'                 => 'attribute_id',
                        'referencedColumnName' => 'id',
                        'nullable'             => false,
                        'onDelete'             => 'CASCADE'
                    ))
                );

                $this->mapManyToOne($metadata, $attributeMapping);
            }

            if ($class['attribute']['model'] === $metadata->getName()) {
                $groupMapping = array(
                    'fieldName'     => 'group',
                    'targetEntity'  => $class['attribute_group']['model'],
                    'inversedBy'    => 'attributes',
                    'joinColumns'   => array(array(
                        'name'                 => 'group_id',
                        'referencedColumnName' => 'id',
                        'nullable'             => true,
                        'onDelete'             => 'CASCADE'
                    ))
                );

                $this->mapManyToOne($metadata, $groupMapping);
            }

            if ($class['attribute_group']['model'] === $metadata->getName()) {
                $attributesMapping = array(
                    'fieldName'     => 'attributes',
                    'targetEntity'  => $class['attribute']['model'],
                    'mappedBy'      => 'group',
                );

                $metadata->mapOneToMany($attributesMapping);
            }
        }
    }

    /**
     * @param ClassMetadataInfo|ClassMetadata $metadata
     * @param array                           $subjectMapping
     */
    private function mapManyToOne(ClassMetadataInfo $metadata, array $subjectMapping)
    {
        $metadata->mapManyToOne($subjectMapping);
    }
}
