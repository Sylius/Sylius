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

namespace Sylius\Behat\Service;

use Behat\Mink\Session;

abstract class JQueryHelper
{
    /**
     * @param Session $session
     */
    public static function waitForAsynchronousActionsToFinish(Session $session)
    {
        $session->wait(5000, '0 === jQuery.active');
    }
}
