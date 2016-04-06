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

use Sylius\Behat\Page\SymfonyPage;
use Webmozart\Assert\Assert;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ShowPage extends SymfonyPage implements ShowPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function pressDelete()
    {
        $this->getDocument()->pressButton('Delete');

        $modal = $this->getDocument()->find('css', '#confirmation-modal');

        Assert::notNull($modal, 'Confirmation modal not found!');
        
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

    /**
     * {@inheritdoc}
     */
    protected function getRouteName()
    {
        return 'sylius_backend_shipping_method_show';
    }
}
