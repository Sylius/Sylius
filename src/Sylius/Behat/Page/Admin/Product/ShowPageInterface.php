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

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
interface ShowPageInterface extends PageInterface
{
    public function deleteProduct();
}
