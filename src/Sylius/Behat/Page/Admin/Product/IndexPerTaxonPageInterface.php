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
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
interface IndexPerTaxonPageInterface extends CrudIndexPageInterface
{
    /**
     * @param array $productNames
     *
     * @return bool
     */
    public function hasProductsInOrder(array $productNames);

    /**
     * @param string $productName
     *
     * @param int $position
     */
    public function setPositionOfProduct($productName, $position);

    public function savePositions();
}
