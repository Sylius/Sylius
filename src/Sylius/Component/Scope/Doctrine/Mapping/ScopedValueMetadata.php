<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Scope\Doctrine\Mapping;


use Metadata\MergeableClassMetadata;
use Metadata\MergeableInterface;

/**
 * Class metadata for scoped values entities
 * @author Matthieu Blottière <matthieu.blottiere@smile.fr>
 */
class ScopedValueMetadata extends MergeableClassMetadata
{
    /**
     * @var string
     */
    public $targetEntity;

    /**
     * @var PropertyMetadata
     */
    public $scopeAware;

    /**
     * @var PropertyMetadata
     */
    public $scope;

    /**
     * Validate metadata
     *
     * @throws MappingException
     * @return void
     */
    public function validate()
    {
        if (!$this->scopeAware) {
            throw new MappingException(sprintf('No scope aware specified for %s', $this->name));
        }

        if (!$this->targetEntity) {
            throw new MappingException(sprintf('No target entity specified for %s', $this->name));
        }

        if (!$this->scope) {
            throw new Mappingexception(sprintf('No scope specified for %s', $this->name));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function merge(MergeableInterface $object)
    {
        if (!$object instanceof self) {
            throw new \InvalidArgumentException(sprintf('Object must be an instance of %s', __CLASS__));
        }

        parent::merge($object);

        if ($object->targetEntity) {
            $this->targetEntity = $object->targetEntity;
        }

        if ($object->scopeAware) {
            $this->scopeAware = $object->scopeAware;
        }

        if ($object->scope) {
            $this->scope = $object->scope;
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
                $this->scopeAware ? $this->scopeAware : null,
                $this->scope ? $this->scope : null,
                parent::serialize(),
            )
        );
    }

    public function unserialize($str)
    {
        list(
            $this->targetEntity,
            $scopeAware,
            $scope,
            $parent
            ) = unserialize($str);

        parent::unserialize($parent);

        if ($scopeAware) {
            $this->scopeAware = $this->propertyMetadata[$scopeAware];
        }

        if ($scope) {
            $this->scope = $this->propertyMetadata[$scope];
        }
    }
}