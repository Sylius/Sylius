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

namespace Sylius\Bundle\ReviewBundle\Doctrine\ORM\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;

final class LoadMetadataSubscriber implements EventSubscriber
{
    /** @var array */
    private $subjects;

    public function __construct(array $subjects)
    {
        $this->subjects = $subjects;
    }

    public function getSubscribedEvents(): array
    {
        return [
            'loadClassMetadata',
        ];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArguments): void
    {
        $metadata = $eventArguments->getClassMetadata();

        $metadataFactory = $eventArguments->getEntityManager()->getMetadataFactory();

        foreach ($this->subjects as $subject => $class) {
            if ($class['review']['classes']['model'] === $metadata->getName()) {
                $reviewableEntity = $class['subject'];
                $reviewerEntity = $class['reviewer']['classes']['model'];
                $reviewableEntityMetadata = $metadataFactory->getMetadataFor($reviewableEntity);
                $reviewerEntityMetadata = $metadataFactory->getMetadataFor($reviewerEntity);

                $metadata->mapManyToOne($this->createSubjectMapping($reviewableEntity, $subject, $reviewableEntityMetadata));
                $metadata->mapManyToOne($this->createReviewerMapping($reviewerEntity, $reviewerEntityMetadata));
            }

            if ($class['subject'] === $metadata->getName()) {
                $reviewEntity = $class['review']['classes']['model'];

                $metadata->mapOneToMany($this->createReviewsMapping($reviewEntity));
            }
        }
    }

    private function createSubjectMapping(
        string $reviewableEntity,
        string $subject,
        ClassMetadata $reviewableEntityMetadata
    ): array {
        return [
            'fieldName' => 'reviewSubject',
            'targetEntity' => $reviewableEntity,
            'inversedBy' => 'reviews',
            'joinColumns' => [[
                'name' => $subject . '_id',
                'referencedColumnName' => $reviewableEntityMetadata->fieldMappings['id']['columnName'] ?? $reviewableEntityMetadata->fieldMappings['id']['fieldName'],
                'nullable' => false,
                'onDelete' => 'CASCADE',
            ]],
        ];
    }

    private function createReviewerMapping(string $reviewerEntity, ClassMetadata $reviewerEntityMetadata): array
    {
        return [
            'fieldName' => 'author',
            'targetEntity' => $reviewerEntity,
            'joinColumns' => [[
                'name' => 'author_id',
                'referencedColumnName' => $reviewerEntityMetadata->fieldMappings['id']['columnName'] ?? $reviewerEntityMetadata->fieldMappings['id']['fieldName'],
                'nullable' => false,
                'onDelete' => 'CASCADE',
            ]],
            'cascade' => ['persist'],
        ];
    }

    private function createReviewsMapping(string $reviewEntity): array
    {
        return [
            'fieldName' => 'reviews',
            'targetEntity' => $reviewEntity,
            'mappedBy' => 'reviewSubject',
            'cascade' => ['all'],
        ];
    }
}
