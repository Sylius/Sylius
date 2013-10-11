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
        $this->getSession('selenium2')->restart();
    }

    /**
     * @When /^I allow the use of my (.+) account \(if I am still on the (.+) website\)$/
     */
    public function iAllowTheUseOfMyAccount($providerName, $domain)
    {
        if ($this->currentUrlContains($domain)) {
            $submitButtons = $this->getSession('selenium2')->getPage()->findAll('xpath', '//form//button[@type="submit"]');
            if (count($submitButtons) != 3) {
                throw new ExpectationException('Page should contain a form with 3 buttons.', $this->getSession('selenium2'));
            }

            if ($providerName == 'Google') {
                $submitButtons[0]->click();
            } else {
                $submitButtons[1]->click();
            }
        }
    }

    /**
     * @Then /^I should be on the (.+) website$/
     * @Then /^I should still be on the (.+) website$/
     */
    public function iShouldBeOnTheWebsite($domain)
    {
        if (!$this->currentUrlContains($domain)) {
            throw new ExpectationException(sprintf('Current URL should contain "%s".', $domain), $this->getSession('selenium2'));
        }
    }

    /**
     * @Then /^I should not be on the (.+) website anymore$/
     */
    public function iShouldNotBeOnTheWebsiteAnymore($domain)
    {
        if ($this->currentUrlContains($domain)) {
            throw new ExpectationException(sprintf('Current URL should not contain "%s".', $domain), $this->getSession('selenium2'));
        }

        // Re-set default session
        $currentUrl = $this->getSession()->getCurrentUrl();
        $this->getMink()->setDefaultSessionName('symfony2');
        $this->getSession()->visit($currentUrl);
    }

    /**
     * @Then /^I should see the .+ login form$/
     */
    public function iShouldSeeTheLoginForm()
    {
        $loginForm = $this->getLoginForm();
    }

    protected function currentUrlContains($domain)
    {
        $currentUrl = $this->getSession('selenium2')->getCurrentUrl();
        return strpos($currentUrl, $domain) !== false;
    }

    protected function getLoginForm()
    {
        return $this->assertSession('selenium2')->elementExists('xpath',
            '//form//input[@type="email"]' .
            '/ancestor::form//input[@type="password"]' .
            '/ancestor::form//*[@type="submit"]' .
            '/ancestor::form'
        );
    }
}
