<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AttributeBundle\Doctrine\ORM\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

final class LoadMetadataSubscriber implements EventSubscriber
{
    public function __construct(private array $subjects)
    {
    }

    public function getSubscribedEvents(): array
    {
        return [
            'loadClassMetadata',
        ];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $metadata = $eventArgs->getClassMetadata();
        $metadataFactory = $eventArgs->getEntityManager()->getMetadataFactory();

        foreach ($this->subjects as $subject => $class) {
            if ($class['attribute_value']['classes']['model'] === $metadata->getName()) {
                $this->mapSubjectOnAttributeValue($subject, $class['subject'], $metadata, $metadataFactory);
                $this->mapAttributeOnAttributeValue($class['attribute']['classes']['model'], $metadata, $metadataFactory);
            }
        }
    }

    private function mapSubjectOnAttributeValue(
        string $subject,
        string $subjectClass,
        ClassMetadataInfo $metadata,
        ClassMetadataFactory $metadataFactory,
    ): void {
        if ($metadata->hasAssociation('subject')) {
            return;
        }

        /** @var ClassMetadataInfo $targetEntityMetadata */
        $targetEntityMetadata = $metadataFactory->getMetadataFor($subjectClass);
        $subjectMapping = [
            'fieldName' => 'subject',
            'targetEntity' => $subjectClass,
            'inversedBy' => 'attributes',
            'joinColumns' => [[
                'name' => $subject . '_id',
                'referencedColumnName' => $targetEntityMetadata->fieldMappings['id']['columnName'] ?? $targetEntityMetadata->fieldMappings['id']['fieldName'],
                'nullable' => false,
                'onDelete' => 'CASCADE',
            ]],
        ];

        $metadata->mapManyToOne($subjectMapping);
    }

    private function mapAttributeOnAttributeValue(
        string $attributeClass,
        ClassMetadataInfo $metadata,
        ClassMetadataFactory $metadataFactory,
    ): void {
        if ($metadata->hasAssociation('attribute')) {
            return;
        }

        /** @var ClassMetadataInfo $attributeMetadata */
        $attributeMetadata = $metadataFactory->getMetadataFor($attributeClass);
        $attributeMapping = [
            'fieldName' => 'attribute',
            'targetEntity' => $attributeClass,
            'joinColumns' => [[
                'name' => 'attribute_id',
                'referencedColumnName' => $attributeMetadata->fieldMappings['id']['columnName'] ?? $attributeMetadata->fieldMappings['id']['fieldName'],
                'nullable' => false,
            ]],
        ];

        $metadata->mapManyToOne($attributeMapping);
    }
}
