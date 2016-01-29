<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Taxonomy\Model;

use Doctrine\Common\Collections\Collection;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface TaxonomiesAwareInterface
{
    /**
     * @return Collection|TaxonomyInterface[]
     */
    public function getTaxonomies();

    /**
     * @param Collection $collection
     */
    public function setTaxonomies(Collection $collection);

    /**
     * @param TaxonomyInterface $taxonomy
     *
     * @return bool
     */
    public function hasTaxonomy(TaxonomyInterface $taxonomy);

    /**
     * @param TaxonomyInterface $taxonomy
     */
    public function addTaxonomy(TaxonomyInterface $taxonomy);

    /**
     * @param TaxonomyInterface $taxonomy
     */
    public function removeTaxonomy(TaxonomyInterface $taxonomy);
}
