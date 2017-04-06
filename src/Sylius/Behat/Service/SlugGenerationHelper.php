<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Service;

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use Webmozart\Assert\Assert;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
abstract class SlugGenerationHelper
{
    /**
     * @param Session $session
     * @param NodeElement $element
     */
    public static function waitForSlugGeneration(Session $session, NodeElement $element)
    {
        Assert::isInstanceOf($session->getDriver(), Selenium2Driver::class);

        JQueryHelper::waitForAsynchronousActionsToFinish($session);
        static::isElementReadonly($session, $element);
        JQueryHelper::waitForAsynchronousActionsToFinish($session);
    }

    /**
     * @param Session $session
     * @param NodeElement $element
     */
    public static function enableSlugModification(Session $session, NodeElement $element)
    {
        Assert::isInstanceOf($session->getDriver(), Selenium2Driver::class);

        JQueryHelper::waitForAsynchronousActionsToFinish($session);
        static::waitForElementToBeClickable($session, $element);

        $element->click();

        JQueryHelper::waitForAsynchronousActionsToFinish($session);
    }

    /**
     * @param Session $session
     * @param NodeElement $element
     *
     * @return bool
     */
    public static function isSlugReadonly(Session $session, NodeElement $element)
    {
        if (!$session->getDriver() instanceof Selenium2Driver) {
            return $element->hasAttribute('readonly');
        }

        JQueryHelper::waitForAsynchronousActionsToFinish($session);

        return static::isElementReadonly($session, $element);
    }

    /**
     * @param Session $session
     * @param NodeElement $element
     */
    private static function waitForElementToBeClickable(Session $session, NodeElement $element)
    {
        $session->wait(5000, sprintf(
            'false === $(document.evaluate("%s", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue).hasClass("loading")',
            $element->getParent()->getParent()->getXpath()
        ));
    }

    /**
     * @param Session $session
     * @param NodeElement $element
     *
     * @return bool
     */
    private static function isElementReadonly(Session $session, NodeElement $element)
    {
        return $session->wait(5000, sprintf(
            'undefined != $(document.evaluate("%s", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue).attr("readonly")',
            $element->getXpath()
        ));
    }
}
