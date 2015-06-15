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
            if ($class['attribute_value']['model'] !== $metadata->getName()) {
                continue;
            }

            $targetEntity = $class['subject'];
            $targetEntityMetadata = $metadataFactory->getMetadataFor($targetEntity);
            $subjectMapping = array(
                'fieldName'     => 'subject',
                'targetEntity'  => $targetEntity,
                'inversedBy'    => 'attributes',
                'joinColumns'   => array(array(
                    'name'                 => $subject.'_id',
                    'referencedColumnName' => $targetEntityMetadata->fieldMappings['id']['columnName'],
                    'nullable'             => false,
                    'onDelete'             => 'CASCADE',
                )),
            );

            $this->mapManyToOne($metadata, $subjectMapping);

            $attributeModel = $class['attribute']['model'];
            $attributeMetadata = $metadataFactory->getMetadataFor($attributeModel);
            $attributeMapping = array(
                'fieldName'     => 'attribute',
                'targetEntity'  => $attributeModel,
                'joinColumns'   => array(array(
                    'name'                 => 'attribute_id',
                    'referencedColumnName' => $attributeMetadata->fieldMappings['id']['columnName'],
                    'nullable'             => false,
                    'onDelete'             => 'CASCADE',
                )),
            );

            $this->mapManyToOne($metadata, $attributeMapping);
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
