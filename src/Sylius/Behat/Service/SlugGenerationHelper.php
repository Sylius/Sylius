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
        Assert::true(DriverHelper::supportsJavascript($session->getDriver()), 'Browser does not support Javascript.');

        JQueryHelper::waitForAsynchronousActionsToFinish($session);

        usleep(500000); // TODO: Remove hardcoded sleep from tests
    }

    /**
     * @param Session $session
     * @param NodeElement $element
     */
    public static function enableSlugModification(Session $session, NodeElement $element)
    {
        Assert::true(DriverHelper::supportsJavascript($session->getDriver()), 'Browser does not support Javascript.');

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
        if (DriverHelper::supportsJavascript($session->getDriver())) {
            JQueryHelper::waitForAsynchronousActionsToFinish($session);
        }

        return $element->hasAttribute('readonly');
    }

    /**
     * @param Session $session
     * @param NodeElement $element
     */
    private static function waitForElementToBeClickable(Session $session, NodeElement $element)
    {
        $session->wait(5000, sprintf(
            'false === $(document.evaluate(%s, document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue).hasClass("loading")',
            json_encode($element->getParent()->getParent()->getXpath())
        ));
    }

}
