<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxonomyBundle\Form\DataTransformer;

use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ObjectSelectionToIdentifierCollectionTransformer;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Model\TaxonomyInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * Transforms arrays of selected taxons into one collection.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Patrick Berenschot <p.berenschot@taka-a-byte.eu>
 */
class TaxonSelectionToCollectionTransformer extends ObjectSelectionToIdentifierCollectionTransformer
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        $taxons = array();
        foreach ($this->objects as $taxonomy) {
            $taxons[$taxonomy->getId()] = array();
        }

        if (null === $value) {
            return $taxons;
        }

        if (!$value instanceof Collection) {
            throw new UnexpectedTypeException($value, 'Doctrine\Common\Collections\Collection');
        }

        return $this->processObjects($value, $taxons);
    }

    private function processObjects(Collection $value, array $taxons)
    {
        /* @var $taxonomy TaxonomyInterface */
        foreach ($this->objects as $taxonomy) {
            /* @var $taxon TaxonInterface */
            foreach ($taxonomy->getTaxons() as $taxon) {
                $this->addChildren($taxonomy, $value, $taxon->getChildren(), $taxons);

                if ($value->contains($this->saveObjects ? $taxon : $taxon->getId())) {
                    $taxons[$taxonomy->getId()][] = $taxon;
                }
            }
        }

        return $taxons;
    }

    /**
     * Add taxon childs to the taxons array recursively.
     *
     * @param TaxonomyInterface $taxonomy
     * @param Collection        $value
     * @param \Traversable      $children
     * @param array             $taxons
     */
    private function addChildren(TaxonomyInterface $taxonomy, Collection $value, $children, array &$taxons)
    {
        if (!is_array($children) && !$children instanceof \Traversable) {
            throw new \InvalidArgumentException('Expecting array or Traversable!');
        }

        /* @var $children TaxonInterface[] */
        foreach ($children as $child) {
            if ($value->contains($this->saveObjects ? $child : $child->getId())) {
                $taxons[$taxonomy->getId()][] = $child;
            }

            if (!$child->getChildren()->isEmpty()) {
                $this->addChildren($taxonomy, $value, $child->getChildren(), $taxons);
            }
        }
    }
}
