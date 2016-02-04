<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shipping;

use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ShippingMethodIndexPage extends SymfonyPage
{
    /**
     * @return string
     */
    public function getRouteName()
    {
        return 'sylius_backend_shipping_method_index';
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function listContainsShippingMethod($name)
    {
        return null === $this->getDocument()->find('css', sprintf('tbody tr:contains("%s")', $name)) ? false : true;
    }
}
