<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ApiBundle\Serializer\Exclusion;

use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Context;

class SparseFieldsetsExclusionStrategy implements ExclusionStrategyInterface
{
    private $fields;

    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * {@inheritDoc}
     */
    public function shouldSkipClass(ClassMetadata $metadata, Context $navigatorContext)
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function shouldSkipProperty(PropertyMetadata $property, Context $navigatorContext)
    {
        $name = $property->serializedName ?: $property->name;

        if ($property->inline || null === $name) {
            return false;
        }

        $fields = $this->getFields($property, $navigatorContext);
        if (null === $fields) {
            return false;
        }

        return !in_array($name, $fields);
    }

    private function getFields(PropertyMetadata $property, Context $navigatorContext)
    {
        $key = $navigatorContext->getMetadataFactory()->getMetadataForClass($property->class)->xmlRootName;
        if (null === $key || !isset($this->fields[$key])) {
            return null;
        }

        return $this->fields[$key];
    }
}
