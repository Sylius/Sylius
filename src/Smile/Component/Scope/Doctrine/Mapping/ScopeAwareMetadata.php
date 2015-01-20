<?php

namespace Smile\Component\Scope\Doctrine\Mapping;


use Metadata\MergeableClassMetadata;
use Metadata\MergeableInterface;

/**
 * Class metadata for store aware entities
 *
 * @see MergeableClassMetadata
 */
class ScopeAwareMetadata extends MergeableClassMetadata
{
    /**
     * @var string
     */
    public $targetEntity;

    /**
     * @var PropertyMetadata
     */
    public $scopedValues;

    /**
     * Validate the metadata
     *
     * @throws MappingException
     * @return void
     */
    public function validate()
    {
        if (!$this->targetEntity) {
            throw new MappingException(sprintf('No target entity specified for %s', $this->name));
        }

        if (!$this->scopedValues) {
            throw new MappingException(sprintf('No scoped values specified for %s', $this->name));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function merge(MergeableInterface $object)
    {
        if (!$object instanceof self) {
            throw new \InvalidArgumentException(sprintf('Object must be an isntance of %s', __CLASS__));
        }

        parent::merge($object);

        if ($object->targetEntity) {
            $this->targetEntity = $object->targetEntity;
        }

        if ($object->scopedValues) {
            $this->scopedValues = $object->scopedValues;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(
            array(
                $this->targetEntity,
                $this->scopedValues ? $this->scopedValues->name : null,
                parent::serialize(),
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($str)
    {
        list (
            $this->targetEntity,
            $scopedValues,
            $parent
            ) = unserialize($str);

        parent::unserialize($parent);

        if ($scopedValues) {
            $this->scopedValues = $this->propertyMetadata[$scopedValues];
        }
    }
}