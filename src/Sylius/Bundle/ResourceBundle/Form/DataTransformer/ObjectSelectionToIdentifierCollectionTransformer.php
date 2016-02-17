<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class ObjectSelectionToIdentifierCollectionTransformer implements DataTransformerInterface
{
    /**
     * @var object[]
     */
    protected $objects;

    /**
     * @var bool
     */
    protected $saveObjects;

    /**
     * @param object[] $objects
     * @param bool  $saveObjects
     */
    public function __construct(array $objects, $saveObjects = true)
    {
        $this->objects = $objects;
        $this->saveObjects = $saveObjects;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return [];
        }

        if (!$value instanceof Collection) {
            throw new UnexpectedTypeException($value, Collection::class);
        }

        return $value->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (empty($value)) {
            return new ArrayCollection();
        }

        if (!is_array($value) && !$value instanceof \Traversable && !$value instanceof \ArrayAccess) {
            throw new UnexpectedTypeException($value, '\Traversable or \ArrayAccess');
        }

        return $this->createCollection($value);
    }

    /**
     * @param object[] $value
     *
     * @return ArrayCollection
     */
    private function createCollection($value)
    {
        $collection = new ArrayCollection();

        foreach ($value as $objects) {
            if (null === $objects) {
                continue;
            }

            if (is_array($objects)) {
                foreach ($objects as $object) {
                    $collection->add($this->saveObjects ? $object : $object->getId());
                }
            } else {
                $collection->add($this->saveObjects ? $objects : $objects->getId());
            }
        }

        return $collection;
    }
}
