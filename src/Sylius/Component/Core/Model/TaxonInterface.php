<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Taxonomy\Model\TaxonInterface as VariableTaxonInterface;

/**
 * Taxon interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface TaxonInterface extends VariableTaxonInterface
{
    /**
     * Get products.
     *
     * @return Collection|ProductInterface[]
     */
    public function getProducts();

    /**
     * Set products.
     *
     * @param ProductInterface[] $products
     */
    public function setProducts($products);
}
