<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\VariationBundle\EventListener;

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
    protected $variables;

    /**
     * @param array $variables
     */
    public function __construct(array $variables)
    {
        $this->variables = $variables;
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

        $this->mapManyToMany($metadata);

        $this->mapOneToMany($metadata);

        $this->mapManyToOne($metadata);
    }

    /**
     * @param ClassMetadata $metadata
     */
    private function mapOneToMany(ClassMetadata $metadata)
    {
        foreach ($this->variables as $class) {
            if ($class['option']['classes']['model'] !== $metadata->getName()) {
                continue;
            }

            $mapping = array(
                'fieldName' => 'values',
                'targetEntity' => $class['option_value']['classes']['model'],
                'mappedBy' => 'option',
                'cascade' => array('all'),
            );

            $metadata->mapOneToMany($mapping);
        }
    }

    /**
     * @param ClassMetadata $metadata
     */
    private function mapManyToOne(ClassMetadata $metadata)
    {
        foreach ($this->variables as $class) {
            if ($class['option_value']['classes']['model'] !== $metadata->getName()) {
                continue;
            }

            $mapping = array(
                'fieldName' => 'option',
                'targetEntity' => $class['option']['classes']['model'],
                'inversedBy' => 'values',
                'joinColumns' => array(array(
                    'name' => 'option_id',
                    'referencedColumnName' => 'id',
                    'nullable' => false,
                    'onDelete' => 'CASCADE',
                )),
            );

            $metadata->mapManyToOne($mapping);
        }
    }

    /**
     * @param ClassMetadata $metadata
     */
    private function mapManyToMany(ClassMetadata $metadata)
    {
        foreach ($this->variables as $variable => $class) {
            if ($class['variant']['classes']['model'] !== $metadata->getName()) {
                continue;
            }

            $metadata->mapManyToOne(array(
                'fieldName'    => 'object',
                'targetEntity' => $class['variable'],
                'inversedBy'   => 'variants',
                'joinColumns'  => array(array(
                    'name'                 => $variable.'_id',
                    'referencedColumnName' => 'id',
                    'nullable'             => false,
                    'onDelete'             => 'CASCADE',
                )),
            ));

            $metadata->mapManyToMany(array(
                'fieldName'    => 'options',
                'type'         => ClassMetadataInfo::MANY_TO_MANY,
                'targetEntity' => $class['option_value']['classes']['model'],
                'joinTable'    => array(
                    'name'               => sprintf('sylius_%s_variant_option_value', $variable),
                    'joinColumns'        => array(array(
                        'name'                 => 'variant_id',
                        'referencedColumnName' => 'id',
                        'unique'               => false,
                        'nullable'             => false,
                        'onDelete'             => 'CASCADE',
                    )),
                    'inverseJoinColumns' => array(array(
                        'name'                 => 'option_value_id',
                        'referencedColumnName' => 'id',
                        'unique'               => false,
                        'nullable'             => false,
                        'onDelete'             => 'CASCADE',
                    )),
                ),
            ));
        }
    }
}
