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
use Sylius\Bundle\CoreBundle\Model\TaxonInterface;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ObjectSelectionToIdentifierCollectionTransformer;
use Sylius\Component\Taxonomy\Model\TaxonomyInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * Transforms arrays of selected taxons into one collection.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
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

        /* @var $taxonomy TaxonomyInterface[] */
        foreach ($this->objects as $taxonomy) {
            /* @var $taxon TaxonInterface[] */
            foreach ($taxonomy->getTaxons() as $taxon) {
                if ($value->contains($this->saveObjects ? $taxon : $taxon->getId())) {
                    $taxons[$taxonomy->getId()][] = $taxon;
                }
            }
        }

        return $taxons;
    }
}
