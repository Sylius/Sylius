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

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
abstract class AutocompleteHelper
{
    /**
     * @param Session $session
     * @param NodeElement $element
     * @param string $value
     */
    public static function chooseValue(Session $session, NodeElement $element, $value)
    {
        Assert::true(DriverHelper::supportsJavascript($session->getDriver()), 'Browser does not support Javascript.');

        static::activateAutocompleteDropdown($session, $element);

        $element->find('css', sprintf('div.item:contains("%s")', $value))->click();

        static::waitForElementToBeVisible($session, $element);
    }

    /**
     * @param Session $session
     * @param NodeElement $element
     * @param string[] $values
     */
    public static function chooseValues(Session $session, NodeElement $element, array $values)
    {
        Assert::true(DriverHelper::supportsJavascript($session->getDriver()), 'Browser does not support Javascript.');

        static::activateAutocompleteDropdown($session, $element);

        foreach ($values as $value) {
            $element->find('css', sprintf('div.item:contains("%s")', $value))->click();

            JQueryHelper::waitForAsynchronousActionsToFinish($session);
        }

        static::waitForElementToBeVisible($session, $element);
    }

    /**
     * @param Session $session
     * @param NodeElement $element
     */
    private static function activateAutocompleteDropdown(Session $session, NodeElement $element)
    {
        JQueryHelper::waitForAsynchronousActionsToFinish($session);

        $element->find('xpath', 'i')->click();

        JQueryHelper::waitForAsynchronousActionsToFinish($session);

        static::waitForElementToBeVisible($session, $element);
    }

    /**
     * @param Session $session
     * @param NodeElement $element
     */
    private static function waitForElementToBeVisible(Session $session, NodeElement $element)
    {
        $session->wait(5000, sprintf(
            '$(document.evaluate(%s, document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue).dropdown("is visible")',
            json_encode($element->getXpath())
        ));
    }
}
