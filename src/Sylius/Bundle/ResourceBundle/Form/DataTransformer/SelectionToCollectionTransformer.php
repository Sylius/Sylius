<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxonomiesBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Transforms arrays of selected entities into one collection.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class SelectionToCollectionTransformer implements DataTransformerInterface
{
    /**
     * Entities map.
     *
     * @var array
     */
    protected $entities;

    /**
     * Identifier.
     *
     * @var string
     */
    protected $identifier;

    /**
     * Constructor.
     */
    public function __construct(array $entities, $identifier = 'id')
    {
        $this->entities   = $entities;
        $this->identifier = $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        $entities = array();

        foreach ($this->entities as $entity) {
            $entities[$entity->getId()] = array();
        }

        if (null === $value) {
            return $entities;
        }

        if (!$value instanceof Collection) {
            throw new UnexpectedTypeException($value, 'Doctrine\Common\Collections\Collection');
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($value as $entity) {
            $entities[$accessor->getValue($entity, $this->identifier)][] = $entity;
        }

        return $entities;
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

        $taxons = new ArrayCollection();

        foreach ($value as $taxonomy) {
            foreach ($taxonomy as $taxon) {
                $taxons->add($taxon);
            }
        }

        return $taxons;
    }
}
