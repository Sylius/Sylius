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

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface UpdateSimpleProductPageInterface extends BaseUpdatePageInterface
{
    /**
     * @return bool
     */
    public function isCodeDisabled();
    
    /**
     * @param int $price
     */
    public function specifyPrice($price);

    /**
     * @param string $name
     * @param string $localeCode
     */
    public function nameItIn($name, $localeCode);
}
