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
        return array('loadClassMetadata');
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArguments
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArguments)
    {
        $metadata = $eventArguments->getClassMetadata();
        $metadataFactory = $eventArguments->getEntityManager()->getMetadataFactory();

        foreach ($this->subjects as $subject => $class) {
            if ($class['review']['model'] !== $metadata->getName()) {
                continue;
            }

            $reviewableEntity = $class['subject'];
            $reviewerEntity = $class['reviewer']['model'];
            $reviewableEntityMetadata = $metadataFactory->getMetadataFor($reviewableEntity);
            $reviewerEntityMetadata = $metadataFactory->getMetadataFor($reviewerEntity);

            $subjectMapping = array(
                'fieldName'    => 'reviewSubject',
                'targetEntity' => $reviewableEntity,
                'inversedBy'   => 'reviews',
                'joinColumns'  => array(
                    array(
                        'name'                 => $subject.'_id',
                        'referencedColumnName' => $reviewableEntityMetadata->fieldMappings['id']['columnName'],
                        'nullable'             => false,
                        'onDelete'             => 'CASCADE',
                    )
                )
            );
            $reviewerMapping = array(
                'fieldName'    => 'author',
                'targetEntity' => $reviewerEntity,
                'joinColumn'   => array(
                    'name'                 => 'customer_id',
                    'referencedColumnName' => $reviewerEntityMetadata->fieldMappings['id']['columnName'],
                ),
                'cascade'      => array('persist'),
            );

            $metadata->mapManyToOne($subjectMapping);
            $metadata->mapManyToOne($reviewerMapping);
        }
    }
}
