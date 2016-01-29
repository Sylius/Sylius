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
class CustomerShowPage extends SymfonyPage
{
    /**
     * @return string
     */
    public function getRouteName()
    {
        return 'sylius_backend_customer_show';
    }

    public function isThisCustomerRegistered($email)
    {
        $username = $this->getDocument()->find('css', 'table > tbody > tr > #username')->getText();

        if ($email === $username) {
            return true;
        }

        return false;
    }
}
