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

namespace Sylius\Bundle\AttributeBundle\Doctrine\ORM\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

final class LoadMetadataSubscriber implements EventSubscriber
{
    /**
     * @var array
     */
    private $subjects;

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
    public function getSubscribedEvents(): array
    {
        return [
            'loadClassMetadata',
        ];
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $metadata = $eventArgs->getClassMetadata();
        $metadataFactory = $eventArgs->getEntityManager()->getMetadataFactory();



        foreach ($this->subjects as $subject => $class) {

            if ($class['attribute_value']['classes']['model'] === $metadata->getName()) {
                $this->mapSubjectOnAttributeValue($subject, $class['subject'], $metadata, $metadataFactory);
                $this->mapAttributeOnAttributeValue($class['attribute']['classes']['model'], $metadata, $metadataFactory);
                $this->mapSelectOptionsOnAttributeValue($subject, $class['attribute_select_option']['classes']['model'], $metadata);
            }

            $this->mapSelectOptionRelations($class, $metadata, $metadataFactory);
        }
    }


    private function mapSelectOptionRelations($class,
                                              ClassMetadataInfo $metadata,
                                              ClassMetadataFactory $metadataFactory)
    {

        if($class['attribute']['classes']['model'] === $metadata->getName())
        {
            $metadata->mapOneToMany([
                'fieldName'    => 'selectOptions',
                'targetEntity' => $class['attribute_select_option']['classes']['model'],
                'mappedBy'     => 'attribute',
                'fetch'        => 'EAGER',
                'cascade'      => ['persist', 'remove'],
                'orphanRemoval'=> true
            ]);
        }



        if($class['attribute_select_option']['classes']['model'] === $metadata->getName())
        {
            $metadata->mapManyToOne([
                'fieldName'    => 'attribute',
                'targetEntity' => $class['attribute']['classes']['model'],
                'inversedBy'   => 'selectOptions',
                'joinColumns' => [[
                    'name'                 => 'attribute_id',
                    'referencedColumnName' => 'id',
                    'nullable'             => false,
                    'onDelete'             => 'CASCADE',
                ]],
            ]);
        }

    }


    /**
     * @param string $subject
     * @param string $subjectClass
     * @param ClassMetadataInfo $metadata
     * @param ClassMetadataFactory $metadataFactory
     */
    private function mapSubjectOnAttributeValue(
        string $subject,
        string $subjectClass,
        ClassMetadataInfo $metadata,
        ClassMetadataFactory $metadataFactory
    ): void {
        $targetEntityMetadata = $metadataFactory->getMetadataFor($subjectClass);
        $subjectMapping = [
            'fieldName' => 'subject',
            'targetEntity' => $subjectClass,
            'inversedBy' => 'attributes',
            'joinColumns' => [[
                'name' => $subject . '_id',
                'referencedColumnName' => $targetEntityMetadata->fieldMappings['id']['columnName'],
                'nullable' => false,
                'onDelete' => 'CASCADE',
            ]],
        ];

        $this->mapManyToOne($metadata, $subjectMapping);
    }



    /**
     * @param string $attributeClass
     * @param ClassMetadataInfo $metadata
     * @param ClassMetadataFactory $metadataFactory
     */
    private function mapAttributeOnAttributeValue(
        string $attributeClass,
        ClassMetadataInfo $metadata,
        ClassMetadataFactory $metadataFactory
    ): void {
        $attributeMetadata = $metadataFactory->getMetadataFor($attributeClass);
        $attributeMapping = [
            'fieldName' => 'attribute',
            'targetEntity' => $attributeClass,
            'joinColumns' => [[
                'name' => 'attribute_id',
                'referencedColumnName' => $attributeMetadata->fieldMappings['id']['columnName'],
                'nullable' => false,
                'onDelete' => 'CASCADE',
            ]]
        ];

        $this->mapManyToOne($metadata, $attributeMapping);
    }

    /**
     * @param string $subjectClass
     * @param ClassMetadataInfo $metadata
     */
    private function mapSelectOptionsOnAttributeValue(
        string $subject,
        string $targetClass,
        ClassMetadataInfo $metadata
    ): void {

        $subjectMapping = [
            'fieldName'    => 'selectOptions',
            'targetEntity' => $targetClass,
            'fetch'        => 'EAGER',
            'cascade'      => ['persist', 'remove'],
            'joinTable'    => [
                "name" => 'sylius_' . $subject ."_attribute_value_attribute_select_option"
            ]
        ];

        $metadata->mapManyToMany($subjectMapping);
    }


    /**
     * @param ClassMetadataInfo|ClassMetadata $metadata
     * @param array $subjectMapping
     */
    private function mapManyToOne(ClassMetadataInfo $metadata, array $subjectMapping): void
    {
        $metadata->mapManyToOne($subjectMapping);
    }
}
