<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Behat;

use Behat\Mink\Exception\ExpectationException;
use Behat\MinkExtension\Context\RawMinkContext;

/**
 * OAuth context.
 *
 * @author Fabian Kiss <fabian.kiss@ymc.ch>
 */
class OAuthContext extends RawMinkContext
{
    /**
     * @Given /^I am not logged in$/
     */
    public function iAmNotLoggedIn()
    {
        $this->getSession()->restart();
    }

    /**
     * @Then /^I should see the connect with "([^""]*)" button$/
     */
    public function iShouldSeeTheConnectWithButton($connect)
    {
        $this->assertSession()->elementExists('css', sprintf('.oauth-login-%s', strtolower($connect)));
    }
}
