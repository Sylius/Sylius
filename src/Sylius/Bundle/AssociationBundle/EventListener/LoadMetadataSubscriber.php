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
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
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
        return [
            'loadClassMetadata',
        ];
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $metadata = $eventArgs->getClassMetadata();
        $metadataFactory = $eventArgs->getEntityManager()->getMetadataFactory();

        foreach ($this->subjects as $subject => $class) {
            if ($class['association']['classes']['model'] !== $metadata->getName()) {
                continue;
            }

            $associationEntity = $class['subject'];
            $associationEntityMetadata = $metadataFactory->getMetadataFor($associationEntity);

            $associationTypeModel = $class['association_type']['classes']['model'];
            $associationTypeMetadata = $metadataFactory->getMetadataFor($associationTypeModel);

            $metadata->mapManyToOne($this->createSubjectMapping($associationEntity, $subject, $associationEntityMetadata));
            $metadata->mapManyToMany($this->createAssociationMapping($associationEntity, $subject, $associationEntityMetadata));
            $metadata->mapManyToOne($this->createAssociationTypeMapping($associationTypeModel, $associationTypeMetadata));
        }
    }

    /**
     * @param string $associationEntity
     * @param string $subject
     * @param ClassMetadata $associationEntityMetadata
     *
     * @return array
     */
    private function createSubjectMapping($associationEntity, $subject, ClassMetadata $associationEntityMetadata)
    {
        return [
            'fieldName' => 'owner',
            'targetEntity' => $associationEntity,
            'inversedBy' => 'associations',
            'joinColumns' => [[
                'name' => $subject.'_id',
                'referencedColumnName' => $associationEntityMetadata->fieldMappings['id']['columnName'],
                'nullable' => false,
                'onDelete' => 'CASCADE',
            ]],
        ];
    }

    /**
     * @param string $associationEntity
     * @param string $subject
     * @param ClassMetadata $associationEntityMetadata
     *
     * @return array
     */
    private function createAssociationMapping($associationEntity, $subject, ClassMetadata $associationEntityMetadata)
    {
        return [
            'fieldName' => 'associatedObjects',
            'targetEntity' => $associationEntity,
            'joinTable' => [
                'name' => sprintf('sylius_%s_association_%s', $subject, $subject),
                'joinColumns' => [[
                    'name' => 'association_id',
                    'referencedColumnName' => 'id',
                    'nullable' => false,
                    'unique' => false,
                    'onDelete' => 'CASCADE',
                ]],
                'inverseJoinColumns' => [[
                    'name' => $subject.'_id',
                    'referencedColumnName' => $associationEntityMetadata->fieldMappings['id']['columnName'],
                    'nullable' => false,
                    'unique' => false,
                    'onDelete' => 'CASCADE',
                ]],
            ],
        ];
    }

    /**
     * @param string $associationModel
     * @param ClassMetadata $associationMetadata
     *
     * @return array
     */
    private function createAssociationTypeMapping($associationModel, ClassMetadata $associationMetadata)
    {
        return [
            'fieldName' => 'type',
            'targetEntity' => $associationModel,
            'joinColumns' => [[
                'name' => 'association_type_id',
                'referencedColumnName' => $associationMetadata->fieldMappings['id']['columnName'],
                'nullable' => false,
                'onDelete' => 'CASCADE',
            ]],
        ];
    }
}
