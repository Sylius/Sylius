<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Behat;

use Behat\MinkExtension\Context\RawMinkContext;

/**
 * @author Fabian Kiss <fabian.kiss@ymc.ch>
 */
class OAuthContext extends RawMinkContext
{
    /**
     * @Then /^I should see the connect with "([^""]*)" button$/
     */
    public function iShouldSeeTheConnectWithButton($connect)
    {
        $this->assertSession()->elementExists('css', sprintf('.oauth-login-%s', strtolower($connect)));
    }
}
