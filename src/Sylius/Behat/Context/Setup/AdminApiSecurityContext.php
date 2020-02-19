<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;

final class AdminApiSecurityContext implements Context
{
    /**
     * @Given I am logged in as an administrator
     */
    public function iAmLoggedInAsAnAdministrator(): void
    {
        // intentionally left blank
        // implementation of admin authorization in new API is required
    }
}
