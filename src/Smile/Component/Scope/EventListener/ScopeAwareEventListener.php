<?php

namespace Smile\Component\Scope\EventListener;


use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Metadata\MetadataFactory;

class ScopeAwareEventListener implements EventSubscriber
{
    /**
     * @var MetadataFactory
     */
    private $metadataFactory;

    /**
     * @var ScopeAwareMetadata[]|ScopedValueMetadata[]
     */
    private $cache = array();

    public function __construct(MetadataFactory $factory)
    {
        $this->metadataFactory = $factory;
    }

    public function getSubscribedEvents()
    {
        return array(
            Events::loadClassMetadata,
        );
    }

    /**
     * Add mapping to scope aware / scoped value entities
     *
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();
        $reflectionClass = $classMetadata->getReflectionClass();

        if (!$reflectionClass || $reflectionClass->isAbstract()) {
            return;
        }

        if ($reflectionClass->implementsInterface('Smile\Component\Scope\ScopeAwareInterface')) {
            $this->mapScopeAware($classMetadata);
        }

        if ($reflectionClass->implementsInterface('Smile\Component\Scope\ScopedValueInterface')) {
            $this->mapScopedValue($classMetadata);
        }
    }

    /**
     * @param string $className
     * @return ScopeAwareMetadata|ScopedValueMetadata
     */
    public function getScopeAwareMetadata($className)
    {
        if (array_key_exists($className, $this->cache)) {
            return $this->cache[$className];
        }

        if ($metadata = $this->metadataFactory->getMetadataForClass($className)) {
            $metadata->validate();
        }

        $this->cache[$className] = $metadata;

        return $metadata;
    }

    /**
     * Add mapping data to scope aware entity
     *
     * @param ClassMetadata $mapping
     * @return void
     */
    public function mapScopeAware(ClassMetadata $mapping)
    {
        $metadata = $this->getScopeAwareMetadata($mapping->name);
        if (!$mapping->hasAssociation($metadata->scopedValues->name)) {
            $targetMetadata = $this->getScopeAwareMetadata($metadata->targetEntity);

            $mapping->mapOneToMany(
                array(
                    'fieldName' => $metadata->scopedValues->name,
                    'targetEntity' => $metadata->targetEntity,
                    'mappedBy' => $targetMetadata->scopeAware->name,
                    'fetch' => ClassMetadataInfo::FETCH_EXTRA_LAZY,
                    'indexBy' => $targetMetadata->scope->name,
                    'cascade' => array('persist', 'merge', 'remove'),
                    'orphanRemoval' => true,
                )
            );
        }
    }

    /**
     * Add mapping data to scoped values
     *
     * @param ClassMetadata $mapping
     * @return void
     */
    public function mapScopedValue(ClassMetadata $mapping)
    {
        $metadata = $this->getScopeAwareMetadata($mapping->name);

        // Map scope aware relation
        if (!$mapping->hasAssociation($metadata->scopeAware->name)) {
            $targetMetadata = $this->getScopeAwareMetadata($metadata->targetEntity);

            $mapping->mapManyToOne(
                array(
                    'fieldName' => $metadata->scopeAware->name,
                    'targetEntity' => $metadata->targetEntity,
                    'inversedBy' => $targetMetadata->scopedValues->name,
                    'joinColumns' => array(
                        array(
                            'name' => 'scope_aware_id',
                            'referencedColumnName' => 'id',
                            'onDelete' => 'CASCADE',
                            'nullable' => false,
                        )
                    ),
                )
            );
        }

        // Map scope field
        if (!$mapping->hasField($metadata->scope->name)) {
            $mapping->mapField(
                array(
                    'fieldName' => $metadata->scope->name,
                    'type' => 'string',
                )
            );
        }

        // Map unique index
        $columns = array(
            $mapping->getSingleAssociationJoinColumnName($metadata->scopeAware->name),
            $metadata->scope->name,
        );

        if (!$this->hasUniqueConstraint($mapping, $columns)) {
            $constraints = isset($mapping->table['uniqueConstraints']) ? $mapping->table['uniqueConstraints'] : array();
            $constraints[$mapping->getTableName() . '_unique_scope'] = array(
                'columns' => $columns,
            );

            $mapping->setPrimaryTable(
                array(
                    'uniqueConstraints' => $constraints,
                )
            );
        }
    }

    /**
     * Check if a unique constraint has been defined
     *
     * @param ClassMetadata $mapping
     * @param array         $columns
     * @return bool
     */
    private function hasUniqueConstraint(ClassMetadata $mapping, array $columns)
    {
        if (isset($mapping->table['uniqueConstraints'])) {
            foreach ($mapping->table['uniqueConstraints'] as $constraint) {
                if (!array_diff($constraint['columns'], $columns)) {
                    return true;
                }
            }
        }

        return false;
    }
}