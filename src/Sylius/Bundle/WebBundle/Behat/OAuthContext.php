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
     * @Then /^I should be on the (.+) website$/
     */
    public function iShouldBeOnTheWebsite($domain)
    {
        if (!$this->currentUrlContains($domain)) {
            throw new ExpectationException(sprintf('Current URL should contain "%s".', $domain), $this->getSession());
        }
    }

    /**
     * @Then /^I should see the .+ login form$/
     */
    public function iShouldSeeTheLoginForm()
    {
        $loginForm = $this->getLoginForm();

        // Re-set default session
        $this->getMink()->setDefaultSessionName('symfony2');
    }

    protected function currentUrlContains($domain)
    {
        $currentUrl = $this->getSession()->getCurrentUrl();
        return strpos($currentUrl, $domain) !== false;
    }

    protected function getLoginForm()
    {
        return $this->assertSession()->elementExists('xpath',
            '//form//input[@type="email"]' .
            '/ancestor::form//input[@type="password"]' .
            '/ancestor::form//*[@type="submit"]' .
            '/ancestor::form'
        );
    }
}
