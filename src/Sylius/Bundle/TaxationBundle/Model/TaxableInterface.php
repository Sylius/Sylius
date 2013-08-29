<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxationBundle\Model;

/**
 * Taxable interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface TaxableInterface
{
    /**
     * Get the tax category for taxable item.
     *
     * @return TaxCategoryInterface
     */
    public function getTaxCategory();
}
