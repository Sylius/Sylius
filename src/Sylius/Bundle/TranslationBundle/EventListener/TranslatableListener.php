<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TranslationBundle\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Query;
use Prezent\Doctrine\Translatable\Mapping\TranslatableMetadata;
use Prezent\Doctrine\Translatable\Mapping\TranslationMetadata;
use Prezent\Doctrine\Translatable\TranslatableInterface;
use Prezent\Doctrine\Translatable\TranslationInterface;
use Prezent\Doctrine\Translatable\EventListener\TranslatableListener as BaseTranslatableListener;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class TranslatableListener extends BaseTranslatableListener
{
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();
        $reflClass     = $classMetadata->reflClass;

        if (!$reflClass || $reflClass->isAbstract()) {
            return;
        }

        if ($reflClass->implementsInterface('Prezent\Doctrine\Translatable\TranslatableInterface')) {
            $this->mapTranslatable($classMetadata);
        }

        if ($reflClass->implementsInterface('Prezent\Doctrine\Translatable\TranslationInterface')) {
            $this->mapTranslation($classMetadata);
        }
    }

    /**
     * Add mapping data to a translatable entity
     *
     * @param ClassMetadata $mapping
     *
     * @return void
     */
    private function mapTranslatable(ClassMetadata $mapping)
    {
        $metadata = $this->getTranslatableMetadata($mapping->name);

        // LoadORMMetadataSubscriber moves associations from child entities to parent entities
        // they have to be removed before the remapping is done
        if ($mapping->hasAssociation($metadata->translations->name)) {
            unset($mapping->associationMappings[$metadata->translations->name]);
        }

        $targetMetadata = $this->getTranslatableMetadata($metadata->targetEntity);

        $mapping->mapOneToMany(array(
            'fieldName'     => $metadata->translations->name,
            'targetEntity'  => $metadata->targetEntity,
            'mappedBy'      => $targetMetadata->translatable->name,
            'fetch'         => ClassMetadataInfo::FETCH_EXTRA_LAZY,
            'indexBy'       => $targetMetadata->locale->name,
            'cascade'       => array('persist', 'merge', 'remove'),
            'orphanRemoval' => true,
        ));
    }

    /**
     * Add mapping data to a translation entity
     *
     * @param ClassMetadata $mapping
     *
     * @return void
     */
    private function mapTranslation(ClassMetadata $mapping)
    {
        $metadata = $this->getTranslatableMetadata($mapping->name);

        // LoadORMMetadataSubscriber moves associations from child entities to parent entities
        // this association has to be removed before it's remapped
        if ($mapping->hasAssociation($metadata->translatable->name)) {
            unset($mapping->associationMappings[$metadata->translatable->name]);
        }

        // Map translatable relation
        $targetMetadata = $this->getTranslatableMetadata($metadata->targetEntity);

        $mapping->mapManyToOne(array(
            'fieldName'    => $metadata->translatable->name,
            'targetEntity' => $metadata->targetEntity,
            'inversedBy'   => $targetMetadata->translations->name,
            'joinColumns'  => array(array(
                'name'                 => 'translatable_id',
                'referencedColumnName' => 'id',
                'onDelete'             => 'CASCADE',
                'nullable'             => false,
            )),
        ));

        // Map locale field
        if (!$mapping->hasField($metadata->locale->name)) {
            $mapping->mapField(array(
                'fieldName' => $metadata->locale->name,
                'type' => 'string',
            ));
        }

        // Map unique index
        $columns = array(
            $mapping->getSingleAssociationJoinColumnName($metadata->translatable->name),
            $metadata->locale->name,
        );

        if (!$this->hasUniqueConstraint($mapping, $columns)) {
            $constraints                                           = isset($mapping->table['uniqueConstraints']) ? $mapping->table['uniqueConstraints'] : array();
            $constraints[$mapping->getTableName() . '_uniq_trans'] = array(
                'columns' => $columns,
            );

            $mapping->setPrimaryTable(array(
                'uniqueConstraints' => $constraints,
            ));
        }
    }

    /**
     * Check if an unique constraint has been defined
     *
     * @param ClassMetadata $mapping
     * @param array         $columns
     *
     * @return bool
     */
    private function hasUniqueConstraint(ClassMetadata $mapping, array $columns)
    {
        if (!isset($mapping->table['uniqueConstraints'])) {
            return false;
        }

        foreach ($mapping->table['uniqueConstraints'] as $constraint) {
            if (!array_diff($constraint['columns'], $columns)) {
                return true;
            }
        }

        return false;
    }
}
