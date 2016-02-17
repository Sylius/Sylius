<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\User\LoginPage;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class UserContext implements Context
{
    /**
     * @var LoginPage
     */
    private $loginPage;

    /**
     * @param LoginPage $loginPage
     */
    public function __construct(LoginPage $loginPage)
    {
        $this->loginPage = $loginPage;
    }

    /**
     * @Given /^I log in as "([^"]*)" with "([^"]*)" password$/
     */
    public function iLogInAs($login, $password)
    {
        $this->loginPage->open();
        $this->loginPage->logIn($login, $password);
    }
}
