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

use Sylius\Behat\SymfonyPageObjectExtension\PageObject\SymfonyPage;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class CustomersIndexPage extends SymfonyPage
{
    /**
     * @return string
     */
    public function getRouteName()
    {
        return 'sylius_backend_customer_index';
    }

    /**
     * @param $email
     */
    public function deleteUser($email)
    {
        $this->getDocument()->find('css', 'table > tbody > tr:contains("'.$email.'")')->pressButton('Delete');

        $this->getSession()->wait(10);

        $confirmationModal = $this->getDocument()->find('css', '#confirmation-modal-confirm');
        $confirmationModal->find('css', 'a:contains("Delete")')->press();
    }
}
