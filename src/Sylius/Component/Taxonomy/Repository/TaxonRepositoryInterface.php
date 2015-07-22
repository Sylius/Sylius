<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Taxonomy\Repository;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Model\TaxonomyInterface;

interface TaxonRepositoryInterface
{
    /**
     * Get all taxons that belong to given taxonomy.
     *
     * @param $taxonomy TaxonomyInterface
     *
     * @return Collection|TaxonInterface[]
     */
    public function getTaxonsAsList(TaxonomyInterface $taxonomy);
}
