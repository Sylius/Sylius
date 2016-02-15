<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\ShippingMethod;

use Sylius\Behat\Page\PageInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
interface IndexPageInterface extends PageInterface
{
    /**
     * @param string $name
     *
     * @return bool
     */
    public function isThereShippingMethodNamed($name);
}
