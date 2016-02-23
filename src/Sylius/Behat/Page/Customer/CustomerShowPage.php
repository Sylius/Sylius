<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Customer;

use Sylius\Behat\Page\ElementNotFoundException;
use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class CustomerShowPage extends SymfonyPage
{
    /**
     * @return string
     */
    public function getRouteName()
    {
        return 'sylius_backend_customer_show';
    }

    /**
     * Checks if the customer on whose page we are currently on is registered,
     * if not throws an exception.
     *
     * @return bool
     */
    public function isRegistered()
    {
        $username = $this->getDocument()->find('css', '#username')->getText();

        return '' != $username;
    }

    /**
     * Deletes the user on whose show page we are currently on.
     *
     * @throws \Exception
     */
    public function deleteAccount()
    {
        $deleteButton = $this->getDocument()->find('css', '.delete-action-form');

        if (null === $deleteButton) {
            throw new ElementNotFoundException('Element not found.');
        }

        $deleteButton->press();

        $confirmationModal = $this->getDocument()->find('css', '#confirmation-modal-confirm');
        $this->waitForModalToAppear($confirmationModal);
        $confirmationModal->find('css', 'a:contains("Delete")')->press();
    }
}
