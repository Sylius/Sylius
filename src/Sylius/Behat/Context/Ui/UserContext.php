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

use Sylius\Behat\Context\FeatureContext;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class UserContext extends FeatureContext
{
    /**
     * @Given /^I log in as "([^"]*)" with "([^"]*)" password$/
     */
    public function iLogInAs($login, $password)
    {
        $loginPage = $this->getPage('User\LoginPage');
        $loginPage->open();

        $loginPage->logIn($login, $password);
    }
}
