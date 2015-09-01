<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AssociationBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class LoadMetadataSubscriber implements EventSubscriber
{
    /**
     * @var array
     */
    protected $subjects;

    /**
     * Constructor.
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
        $metadataFactory = $eventArgs->getEntityManager()->getMetadataFactory();

        foreach ($this->subjects as $subject => $class) {
            if ($class['association']['model'] !== $metadata->getName()) {
                continue;
            }

            $associationEntity = $class['subject'];
            $associationEntityMetadata = $metadataFactory->getMetadataFor($associationEntity);
            $subjectMapping = array(
                'fieldName'     => 'owner',
                'targetEntity'  => $associationEntity,
                'inversedBy'    => 'associations',
                'joinColumns'   => array(array(
                    'name'                 => $subject.'_id',
                    'referencedColumnName' => $associationEntityMetadata->fieldMappings['id']['columnName'],
                    'nullable'             => false,
                    'onDelete'             => 'CASCADE',
                )),
            );

            $metadata->mapManyToOne($subjectMapping);

            $associationMapping = array(
                'fieldName'     => 'associatedObjects',
                'targetEntity'  => $associationEntity,
                'joinColumns'   => array(array(
                    'name'                 => $subject.'_id',
                    'referencedColumnName' => $associationEntityMetadata->fieldMappings['id']['columnName'],
                    'nullable'             => false,
                    'onDelete'             => 'CASCADE',
                )),
            );

            $metadata->mapManyToMany($associationMapping);

            $associationModel = $class['association_type']['model'];
            $associationMetadata = $metadataFactory->getMetadataFor($associationModel);
            $associationTypeMapping = array(
                'fieldName'     => 'type',
                'targetEntity'  => $associationModel,
                'joinColumns'   => array(array(
                    'name'                 => 'association_type_id',
                    'referencedColumnName' => $associationMetadata->fieldMappings['id']['columnName'],
                    'nullable'             => false,
                    'onDelete'             => 'CASCADE',
                )),
            );

            $metadata->mapManyToOne($associationTypeMapping);
        }
    }
}
