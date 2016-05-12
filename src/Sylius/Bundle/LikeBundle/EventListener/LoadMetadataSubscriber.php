<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\LikeBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

/**
 * @author Loïc Frémont <loic@mobizel.com>
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
        return [
            'loadClassMetadata',
        ];
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArguments
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArguments)
    {
        $metadata = $eventArguments->getClassMetadata();
        $metadataFactory = $eventArguments->getEntityManager()->getMetadataFactory();

        foreach ($this->subjects as $subject => $class) {
            if ($class['like']['classes']['model'] === $metadata->getName()) {
                $reviewableEntity = $class['subject'];
                $likerEntity = $class['liker']['classes']['model'];
                $likableEntityMetadata = $metadataFactory->getMetadataFor($reviewableEntity);
                $reviewerEntityMetadata = $metadataFactory->getMetadataFor($likerEntity);

                $metadata->mapManyToOne($this->createSubjectMapping($reviewableEntity, $subject, $likableEntityMetadata));
                $metadata->mapManyToOne($this->createLikerMapping($likerEntity, $reviewerEntityMetadata));
            }

            if ($class['subject'] === $metadata->getName()) {
                $reviewEntity = $class['like']['classes']['model'];

                $metadata->mapOneToMany($this->createLikesMapping($reviewEntity));
            }
        }
    }

    /**
     * @param string $reviewableEntity
     * @param string $subject
     * @param ClassMetadata $likableEntityMetadata
     *
     * @return array
     */
    private function createSubjectMapping($reviewableEntity, $subject, ClassMetadata $likableEntityMetadata)
    {
        return [
            'fieldName' => 'likeSubject',
            'targetEntity' => $reviewableEntity,
            'inversedBy' => 'likes',
            'joinColumns' => [
                [
                    'name' => $subject.'_id',
                    'referencedColumnName' => $likableEntityMetadata->fieldMappings['id']['columnName'],
                    'nullable' => false,
                    'onDelete' => 'CASCADE',
                ],
            ],
        ];
    }

    /**
     * @param string $likerEntity
     * @param ClassMetadata $likerEntityMetadata
     *
     * @return array
     */
    private function createLikerMapping($likerEntity, ClassMetadata $likerEntityMetadata)
    {
        return [
            'fieldName' => 'author',
            'targetEntity' => $likerEntity,
            'joinColumn' => [
                'name' => 'author_id',
                'referencedColumnName' => $likerEntityMetadata->fieldMappings['id']['columnName'],
                'nullable' => false,
                'onDelete' => 'CASCADE',
            ],
            'cascade' => ['persist'],
        ];
    }

    /**
     * @param string $reviewEntity
     *
     * @return array
     */
    private function createLikesMapping($reviewEntity)
    {
        return [
            'fieldName' => 'likes',
            'targetEntity' => $reviewEntity,
            'mappedBy' => 'likeSubject',
            'cascade' => ['all'],
        ];
    }
}
