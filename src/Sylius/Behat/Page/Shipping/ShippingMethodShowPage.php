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
class ShippingMethodShowPage extends SymfonyPage
{
    /**
     * @return string
     */
    public function getRouteName()
    {
        return 'sylius_backend_shipping_method_show';
    }

    public function deleteMethod()
    {
        $this->getDocument()->pressButton('Delete');

        $modal = $this->getDocument()->find('css', '#confirmation-modal');
        $confirmButton = $modal->find('css', 'a:contains(Delete)');

        $this->waitForModalToAppear(10, $modal);

        $confirmButton->press();
    }

    /**
     * @param string $message
     *
     * @return bool
     */
    public function flashContainsMessage($message)
    {
        return false !== strpos($this->getDocument()->find('css', '#flashes > div')->getText(), $message);
    }
}
