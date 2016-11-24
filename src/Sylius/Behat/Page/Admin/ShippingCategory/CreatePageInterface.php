<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\ShippingCategory;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInteface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
interface CreatePageInterface extends BaseCreatePageInteface
{
    /**
     * @param string $description
     */
    public function specifyDescription($description);
}
