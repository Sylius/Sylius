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

namespace Sylius\Behat\Context\Domain;

use Behat\Behat\Context\Context;

final class NotificationContext implements Context
{
    /**
     * @Then I should be notified that it has been successfully deleted
     */
    public function iShouldBeNotified()
    {
        // Not applicable in the domain scope
    }
}
