<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\User;

use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class RegisterPage extends SymfonyPage
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_user_registration';
    }

    /**
     * @param string $email
     */
    public function register($email)
    {
        $document = $this->getDocument();

        $document->fillField('First name', 'Ted');
        $document->fillField('Last name', 'Smith');
        $document->fillField('Email', $email);
        $document->fillField('Password', 'pswd');
        $document->fillField('Verification', 'pswd');

        $document->pressButton('Register');
    }

    /**
     * @return bool
     */
    public function wasRegistrationSuccessful()
    {
        $flashMessage = $this->getDocument()->find('css', '.alert-success');

        return null !== $flashMessage;
    }
}
