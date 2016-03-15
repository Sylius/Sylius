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

use Sylius\Behat\Page\PageInterface;
use Sylius\Component\Core\Model\ProductInterface;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
interface IndexPageInterface extends PageInterface
{
    /**
     * @param ProductInterface $product
     *
     * @return bool
     */
    public function isThereProduct(ProductInterface $product);
}
