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
        $session->wait(1000, 'typeof jQuery !== "undefined" && 0 === jQuery.active');
    }

    public static function waitForFormToStopLoading(DocumentElement $document, int $timeout = 10): void
    {
        $form = $document->find('css', 'form');
        $document->waitFor($timeout, fn () => !$form->hasClass('loading'));
    }
}
