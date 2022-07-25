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

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use DMore\ChromeDriver\ChromeDriver;

abstract class SlugGenerationHelper
{
    public static function waitForSlugGeneration(Session $session, NodeElement $element): void
    {
        JQueryHelper::waitForAsynchronousActionsToFinish($session);

        static::isElementReadonly($session, $element);

        JQueryHelper::waitForAsynchronousActionsToFinish($session);
    }

    public static function enableSlugModification(Session $session, NodeElement $element): void
    {
        JQueryHelper::waitForAsynchronousActionsToFinish($session);

        static::isElementReadonly($session, $element);

        $element->click();

        static::isElementNotReadonly($session, $element);

        JQueryHelper::waitForAsynchronousActionsToFinish($session);
    }

    public static function isSlugReadonly(Session $session, NodeElement $element): bool
    {
        if (!$session->getDriver() instanceof Selenium2Driver && !$session->getDriver() instanceof ChromeDriver) {
            return $element->hasAttribute('readonly');
        }

        JQueryHelper::waitForAsynchronousActionsToFinish($session);

        return static::isElementReadonly($session, $element);
    }

    private static function isElementReadonly(Session $session, NodeElement $element): bool
    {
        return $session->wait(1000, sprintf(
            'undefined != $(document.evaluate("%s", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue).attr("readonly")',
            str_replace('"', '\"', $element->getXpath()),
        ));
    }

    private static function isElementNotReadonly(Session $session, NodeElement $element): bool
    {
        return $session->wait(1000, sprintf(
            'undefined == $(document.evaluate("%s", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue).attr("readonly")',
            str_replace('"', '\"', $element->getXpath()),
        ));
    }
}
