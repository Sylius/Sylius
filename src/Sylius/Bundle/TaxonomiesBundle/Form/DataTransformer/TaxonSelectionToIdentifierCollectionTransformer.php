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

class TaxonSelectionToIdentifierCollectionTransformer implements DataTransformerInterface
{
    private $taxonomies;

    public function __construct(array $taxonomies)
    {
        $this->taxonomies = $taxonomies;
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

        $taxons = new ArrayCollection();

        foreach ($value as $taxonomy) {
            foreach ($taxonomy as $taxon) {
                $taxons->add($taxon->getId());
            }
        }

        return $taxons;
    }
}
