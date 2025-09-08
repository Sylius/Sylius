<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Service;

use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Session;

abstract class JQueryHelper
{
    public static function waitForAsynchronousActionsToFinish(Session $session): void
    {
        $session->wait(1000, "!document.querySelector('[data-live-loading=true]')");
    }

    public static function waitForFormToStopLoading(DocumentElement $document, int $timeout = 10): void
    {
        if (DriverHelper::isJavascript($document->getSession()->getDriver())) {
            $document->getSession()->wait(500, "document.readyState === 'complete' && !document.querySelector('[data-live-loading=true]')");
        }
    }
}
