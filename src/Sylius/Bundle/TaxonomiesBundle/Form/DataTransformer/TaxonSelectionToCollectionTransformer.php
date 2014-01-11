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

/**
 * Transforms arrays of selected taxons into one collection.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class TaxonSelectionToCollectionTransformer implements DataTransformerInterface
{
    /**
     * Taxons map.
     *
     * @var array
     */
    private $taxonomies;

    /**
     * Constructor.
     */
    public function __construct(array $taxonomies)
    {
        $this->taxonomies = $taxonomies;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        $taxons = array();

        foreach ($this->taxonomies as $taxonomy) {
            $taxons[$taxonomy->getId()] = array();
        }

        if (null === $value) {
            return $taxons;
        }

        if (!$value instanceof Collection) {
            throw new UnexpectedTypeException($value, 'Doctrine\Common\Collections\Collection');
        }

        foreach ($value as $taxon) {
            $taxons[$taxon->getTaxonomy()->getId()][] = $taxon;
        }

        return $taxons;
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
