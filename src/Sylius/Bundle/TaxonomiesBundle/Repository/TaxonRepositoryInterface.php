<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxonomiesBundle\Repository;

use Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface;
use Sylius\Bundle\TaxonomiesBundle\Model\TaxonomyInterface;

interface TaxonRepositoryInterface
{
    /**
     * Get all taxons that belong to given taxonomy.
     *
     * @param $taxonomy TaxonomyInterface
     *
     * @return TaxonInterface[]
     */
    public function getTaxonsAsList(TaxonomyInterface $taxonomy);
}
