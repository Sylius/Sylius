<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\Admin\ShippingCategory;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInteface;

interface CreatePageInterface extends BaseCreatePageInteface
{
    /**
     * @param string $code
     */
    public function specifyCode($code);

    /**
     * @param string $name
     */
    public function nameIt($name);

    /**
     * @param string $description
     */
    public function specifyDescription($description);
}
