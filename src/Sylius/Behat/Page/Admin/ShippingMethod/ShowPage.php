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

use Sylius\Behat\Page\ElementNotFoundException;
use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ShowPage extends SymfonyPage implements ShowPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_backend_shipping_method_show';
    }

    /**
     * {@inheritdoc}
     */
    public function pressDelete()
    {
        $this->getDocument()->pressButton('Delete');

        $modal = $this->getDocument()->find('css', $modalName = '#confirmation-modal');
        if (null === $modal) {
            throw new ElementNotFoundException(sprintf('The element "%s" was not found on page.', $modalName));
        }

        $confirmButton = $modal->find('css', 'a:contains(Delete)');

        $this->waitForModalToAppear($modal);

        $confirmButton->press();
    }

    /**
     * {@inheritdoc}
     */
    public function flashContainsMessage($message)
    {
        return false !== strpos($this->getDocument()->find('css', '#flashes > div')->getText(), $message);
    }
}
