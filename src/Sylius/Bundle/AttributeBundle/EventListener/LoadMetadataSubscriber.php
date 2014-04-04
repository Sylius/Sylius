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
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
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
            if ($class['attribute_value']['model'] !== $metadata->getName()) {
                continue;
            }

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

            $metadata->mapManyToOne($subjectMapping);

            $attributeMapping = array(
                'fieldName'     => 'attribute',
                'targetEntity'  => $class['attribute']['model'],
                'inversedBy'    => 'attributes',
                'joinColumns'   => array(array(
                    'name'                 => 'attribute_id',
                    'referencedColumnName' => 'id',
                    'nullable'             => false,
                    'onDelete'             => 'CASCADE'
                ))
            );

            $metadata->mapManyToOne($attributeMapping);
        }
    }
}
