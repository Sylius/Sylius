<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Checkout;

use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CheckoutSecurityStep extends SymfonyPage
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_checkout_security';
    }

    /**
     * @param string $login
     * @param string $password
     */
    public function logInAsExistingUser($login, $password)
    {
        $document = $this->getDocument();

        $emailField = $document->find('css', '.checkout-existing-customer input#username');
        $passwordField = $document->find('css', '.checkout-existing-customer input#password');

        $emailField->setValue($login);
        $passwordField->setValue($password);

        $document->pressButton('Login');
    }

    /**
     * @param string $email
     */
    public function proceedAsGuest($email)
    {
        $document = $this->getDocument();

        $document->find('css', '#sylius_customer_guest input#sylius_customer_guest_email')->setValue($email);
        $document->pressButton('Proceed with your order');
    }
}
