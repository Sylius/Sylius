<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\User;

use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class LoginPage extends SymfonyPage implements LoginPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function logIn($email, $password)
    {
        $document = $this->getDocument();

        $document->fillField('Email', $email);
        $document->fillField('Password', $password);

        $document->pressButton('Login');
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_user_security_login';
    }
}
