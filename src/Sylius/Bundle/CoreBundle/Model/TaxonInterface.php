<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Model;

use Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface as VariableTaxonInterface;

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
     * @return ProductInterface[]
     */
    public function getProducts();

    /**
     * Set products.
     *
     * @param ProductInterface[] $products
     */
    public function setProducts($products);
}
