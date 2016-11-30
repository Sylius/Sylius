<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Product;

use Sylius\Behat\Page\Admin\Crud\IndexPageInterface as CrudIndexPageInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface IndexPageInterface extends CrudIndexPageInterface
{
    /**
     * @param string $taxonName
     */
    public function filterByTaxon($taxonName);
}
