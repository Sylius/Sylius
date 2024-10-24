<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Element\Shop\Account\RegisterElementInterface;
use Sylius\Behat\Page\Shop\Account\LoginPageInterface;
use Sylius\Behat\Page\Shop\Account\RegisterPageInterface;

final class AuthorizationContext implements Context
{
    public function __construct(
        private LoginPageInterface $loginPage,
        private RegisterPageInterface $registerPage,
        private RegisterElementInterface $registerElement,
    ) {
    }

    /**
     * @When I sign in with email :email and password :password
     * @When I sign in again with email :email and password :password in the previous session
     */
    public function iSignInWithEmailAndPassword(string $email, string $password): void
    {
        $this->loginPage->open();
        $this->loginPage->specifyUsername($email);
        $this->loginPage->specifyPassword($password);
        $this->loginPage->logIn();
    }

    /**
     * @When I register with email :email and password :password
     */
    public function iRegisterWithEmailAndPassword(string $email, string $password): void
    {
        $this->registerPage->open();
        $this->registerElement->specifyEmail($email);
        $this->registerElement->specifyPassword($password);
        $this->registerElement->verifyPassword($password);
        $this->registerElement->specifyFirstName('Carrot');
        $this->registerElement->specifyLastName('Ironfoundersson');
        $this->registerElement->register();
    }
}
