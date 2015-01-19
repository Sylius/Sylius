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

class ResourceSelectionToIdentifierCollectionTransformer implements DataTransformerInterface
{
    /**
     * @var ResourceInterface[]
     */
    protected $resources;

    /**
     * @var boolean
     */
    protected $useIdentifiers;

    /**
     * @param resource[] $resources
     * @param boolean  $useIdentifiers
     */
    public function __construct(array $resources, $useIdentifiers = true)
    {
        $this->resources = $resources;
        $this->useIdentifiers = $useIdentifiers;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return array();
        }

        if (!$value instanceof Collection) {
            throw new UnexpectedTypeException($value, 'Doctrine\Common\Collections\Collection');
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
     * @param resource[] $value
     *
     * @return ArrayCollection
     */
    private function createCollection($value)
    {
        $collection = new ArrayCollection();

        foreach ($value as $resources) {
            if (null === $resources) {
                continue;
            }

            if (is_array($resources)) {
                foreach ($resources as $resource) {
                    $collection->add($this->useIdentifiers ? $resource : $resource->getId());
                }
            } else {
                $collection->add($this->useIdentifiers ? $resources : $resources->getId());
            }
        }

        return $collection;
    }
}
