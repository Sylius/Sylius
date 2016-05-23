<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Domain;

use Behat\Behat\Context\Context;

/**
 * @author Łukasz Chruściel<lukasz.chrusciel@lakion.com>
 */
final class SecurityContext implements Context
{
    /**
     * @Given I am logged in as an administrator
     */
    public function iAmLoggedInAsAnAdministrator()
    {
        // Not applicable in the domain scope
    }
}
