<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReviewBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

/**
 * @author Mateusz Zalewski <mateusz.p.zalewski@gmail.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class LoadMetadataSubscriber implements EventSubscriber
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
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return ['loadClassMetadata'];
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArguments
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArguments)
    {
        $metadata = $eventArguments->getClassMetadata();
        $metadataFactory = $eventArguments->getEntityManager()->getMetadataFactory();

        foreach ($this->subjects as $subject => $class) {
            if ($class['review']['classes']['model'] === $metadata->getName()) {
                $reviewableEntity = $class['subject'];
                $reviewerEntity = $class['reviewer']['classes']['model'];
                $reviewableEntityMetadata = $metadataFactory->getMetadataFor($reviewableEntity);
                $reviewerEntityMetadata = $metadataFactory->getMetadataFor($reviewerEntity);

                $subjectMapping = [
                    'fieldName' => 'reviewSubject',
                    'targetEntity' => $reviewableEntity,
                    'inversedBy' => 'reviews',
                    'joinColumns' => [
                        [
                            'name' => $subject.'_id',
                            'referencedColumnName' => $reviewableEntityMetadata->fieldMappings['id']['columnName'],
                            'nullable' => false,
                            'onDelete' => 'CASCADE',
                        ],
                    ],
                ];

                $reviewerMapping = [
                    'fieldName' => 'author',
                    'targetEntity' => $reviewerEntity,
                    'joinColumn' => [
                        'name' => 'customer_id',
                        'referencedColumnName' => $reviewerEntityMetadata->fieldMappings['id']['columnName'],
                        'nullable' => false,
                        'onDelete' => 'CASCADE',
                    ],
                    'cascade' => ['persist'],
                ];

                $metadata->mapManyToOne($subjectMapping);
                $metadata->mapManyToOne($reviewerMapping);
            }

            if ($class['subject'] === $metadata->getName()) {
                $reviewEntity = $class['review']['classes']['model'];

                $reviewsMapping = [
                    'fieldName' => 'reviews',
                    'targetEntity' => $reviewEntity,
                    'mappedBy' => 'reviewSubject',
                    'cascade' => ['all'],
                ];

                $metadata->mapOneToMany($reviewsMapping);
            }
        }
    }
}
