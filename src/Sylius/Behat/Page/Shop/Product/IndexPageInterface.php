<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Product;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
interface IndexPageInterface
{
    /**
     * @param string $resourceName
     * 
     * @return bool
     */
    public function isResourceOnPage($resourceName);

    /**
     * @return bool
     */
    public function isEmpty();
}
