<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Extension\SemanticUiExtension\ServiceContainer\Driver;

use Behat\Mink\Driver\CoreDriver;
use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\Mink\Session;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class SemanticUiDriver extends CoreDriver implements DriverInterface
{
    /**
     * @var Selenium2Driver
     */
    private $selenium2Driver;

    /**
     * @param Selenium2Driver $selenium2Driver
     */
    public function __construct(Selenium2Driver $selenium2Driver)
    {
        $this->selenium2Driver = $selenium2Driver;
    }

    /**
     * Sets driver's current session.
     *
     * @param Session $session
     */
    public function setSession(Session $session)
    {
        $this->selenium2Driver->setSession($session);
    }

    /**
     * Starts driver.
     *
     * Once started, the driver should be ready to visit a page.
     *
     * Calling any action before visiting a page is an undefined behavior.
     * The only supported method calls on a fresh driver are
     * - visit()
     * - setRequestHeader()
     * - setBasicAuth()
     * - reset()
     * - stop()
     *
     * Calling start on a started driver is an undefined behavior. Driver
     * implementations are free to handle it silently or to fail with an
     * exception.
     *
     * @throws DriverException When the driver cannot be started
     */
    public function start()
    {
        $this->selenium2Driver->start();
    }

    /**
     * Checks whether driver is started.
     *
     * @return Boolean
     */
    public function isStarted()
    {
        return $this->selenium2Driver->isStarted();
    }

    /**
     * Stops driver.
     *
     * Once stopped, the driver should be started again before using it again.
     *
     * Calling any action on a stopped driver is an undefined behavior.
     * The only supported method call after stopping a driver is starting it again.
     *
     * Calling stop on a stopped driver is an undefined behavior. Driver
     * implementations are free to handle it silently or to fail with an
     * exception.
     *
     * @throws DriverException When the driver cannot be closed
     */
    public function stop()
    {
        $this->selenium2Driver->stop();
    }

    /**
     * Resets driver state.
     *
     * This should reset cookies, request headers and basic authentication.
     * When possible, the history should be reset as well, but this is not enforced
     * as some implementations may not be able to reset it without restarting the
     * driver entirely. Consumers requiring a clean history should restart the driver
     * to enforce it.
     *
     * Once reset, the driver should be ready to visit a page.
     * Calling any action before visiting a page is an undefined behavior.
     * The only supported method calls on a fresh driver are
     * - visit()
     * - setRequestHeader()
     * - setBasicAuth()
     * - reset()
     * - stop()
     *
     * Calling reset on a stopped driver is an undefined behavior.
     */
    public function reset()
    {
        $this->selenium2Driver->reset();
    }

    /**
     * Visit specified URL.
     *
     * @param string $url url of the page
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function visit($url)
    {
        $this->selenium2Driver->visit($url);
    }

    /**
     * Returns current URL address.
     *
     * @return string
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getCurrentUrl()
    {
        return $this->selenium2Driver->getCurrentUrl();
    }

    /**
     * Reloads current page.
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function reload()
    {
        $this->selenium2Driver->reload();
    }

    /**
     * Moves browser forward 1 page.
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function forward()
    {
        $this->selenium2Driver->forward();
    }

    /**
     * Moves browser backward 1 page.
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function back()
    {
        $this->selenium2Driver->back();
    }

    /**
     * Sets HTTP Basic authentication parameters.
     *
     * @param string|Boolean $user user name or false to disable authentication
     * @param string $password password
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function setBasicAuth($user, $password)
    {
        $this->selenium2Driver->setBasicAuth($user, $password);
    }

    /**
     * Switches to specific browser window.
     *
     * @param string $name window name (null for switching back to main window)
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function switchToWindow($name = null)
    {
        $this->selenium2Driver->switchToWindow($name);
    }

    /**
     * Switches to specific iFrame.
     *
     * @param string $name iframe name (null for switching back)
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function switchToIFrame($name = null)
    {
        $this->selenium2Driver->switchToIFrame($name);
    }

    /**
     * Sets specific request header on client.
     *
     * @param string $name
     * @param string $value
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function setRequestHeader($name, $value)
    {
        $this->selenium2Driver->setRequestHeader($name, $value);
    }

    /**
     * Returns last response headers.
     *
     * @return array
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getResponseHeaders()
    {
        return $this->selenium2Driver->getResponseHeaders();
    }

    /**
     * Sets cookie.
     *
     * @param string $name
     * @param string $value
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function setCookie($name, $value = null)
    {
        $this->selenium2Driver->setCookie($name, $value);
    }

    /**
     * Returns cookie by name.
     *
     * @param string $name
     *
     * @return string|null
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getCookie($name)
    {
        return $this->selenium2Driver->getCookie($name);
    }

    /**
     * Returns last response status code.
     *
     * @return int
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getStatusCode()
    {
        return $this->selenium2Driver->getStatusCode();
    }

    /**
     * Returns last response content.
     *
     * @return string
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getContent()
    {
        return $this->selenium2Driver->getContent();
    }

    /**
     * Capture a screenshot of the current window.
     *
     * @return string screenshot of MIME type image/* depending
     *                on driver (e.g., image/png, image/jpeg)
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getScreenshot()
    {
        return $this->selenium2Driver->getScreenshot();
    }

    /**
     * Return the names of all open windows.
     *
     * @return array array of all open windows
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getWindowNames()
    {
        return $this->selenium2Driver->getWindowNames();
    }

    /**
     * Return the name of the currently active window.
     *
     * @return string the name of the current window
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getWindowName()
    {
        return $this->selenium2Driver->getWindowName();
    }

    /**
     * Finds elements with specified XPath query.
     *
     * @param string $xpath
     *
     * @return NodeElement[]
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function find($xpath)
    {
        return $this->selenium2Driver->find($xpath);
    }

    /**
     * Returns element's tag name by it's XPath query.
     *
     * @param string $xpath
     *
     * @return string
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getTagName($xpath)
    {
        return $this->selenium2Driver->getTagName($xpath);
    }

    /**
     * Returns element's text by it's XPath query.
     *
     * @param string $xpath
     *
     * @return string
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getText($xpath)
    {
        return $this->selenium2Driver->getText($xpath);
    }

    /**
     * Returns element's inner html by it's XPath query.
     *
     * @param string $xpath
     *
     * @return string
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getHtml($xpath)
    {
        return $this->selenium2Driver->getHtml($xpath);
    }

    /**
     * Returns element's outer html by it's XPath query.
     *
     * @param string $xpath
     *
     * @return string
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getOuterHtml($xpath)
    {
        return $this->selenium2Driver->getOuterHtml($xpath);
    }

    /**
     * Returns element's attribute by it's XPath query.
     *
     * @param string $xpath
     * @param string $name
     *
     * @return string|null
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function getAttribute($xpath, $name)
    {
        return $this->selenium2Driver->getAttribute($xpath, $name);
    }

    /**
     * Returns element's value by it's XPath query.
     *
     * @param string $xpath
     *
     * @return string|bool|array
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     *
     * @see \Behat\Mink\Element\NodeElement::getValue
     */
    public function getValue($xpath)
    {
        return $this->selenium2Driver->getValue($xpath);
    }

    /**
     * Sets element's value by it's XPath query.
     *
     * @param string $xpath
     * @param string|bool|array $value
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     *
     * @see \Behat\Mink\Element\NodeElement::setValue
     */
    public function setValue($xpath, $value)
    {
        $this->selenium2Driver->setValue($xpath, $value);
    }

    /**
     * Checks checkbox by it's XPath query.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     *
     * @see \Behat\Mink\Element\NodeElement::check
     */
    public function check($xpath)
    {
        $this->selenium2Driver->check($xpath);
    }

    /**
     * Unchecks checkbox by it's XPath query.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     *
     * @see \Behat\Mink\Element\NodeElement::uncheck
     */
    public function uncheck($xpath)
    {
        $this->selenium2Driver->uncheck($xpath);
    }

    /**
     * Checks whether checkbox or radio button located by it's XPath query is checked.
     *
     * @param string $xpath
     *
     * @return Boolean
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     *
     * @see \Behat\Mink\Element\NodeElement::isChecked
     */
    public function isChecked($xpath)
    {
        return $this->selenium2Driver->isChecked($xpath);
    }

    /**
     * Selects option from select field or value in radio group located by it's XPath query.
     *
     * @param string $xpath
     * @param string $value
     * @param Boolean $multiple
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     *
     * @see \Behat\Mink\Element\NodeElement::selectOption
     */
    public function selectOption($xpath, $value, $multiple = false)
    {
        $this->selenium2Driver->selectOption($xpath, $value, $multiple);
    }

    /**
     * Checks whether select option, located by it's XPath query, is selected.
     *
     * @param string $xpath
     *
     * @return Boolean
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     *
     * @see \Behat\Mink\Element\NodeElement::isSelected
     */
    public function isSelected($xpath)
    {
        return $this->selenium2Driver->isSelected($xpath);
    }

    /**
     * Clicks button or link located by it's XPath query.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function click($xpath)
    {
        $this->selenium2Driver->click($xpath);
    }

    /**
     * Double-clicks button or link located by it's XPath query.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function doubleClick($xpath)
    {
        $this->selenium2Driver->doubleClick($xpath);
    }

    /**
     * Right-clicks button or link located by it's XPath query.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function rightClick($xpath)
    {
        $this->selenium2Driver->rightClick($xpath);
    }

    /**
     * Attaches file path to file field located by it's XPath query.
     *
     * @param string $xpath
     * @param string $path
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     *
     * @see \Behat\Mink\Element\NodeElement::attachFile
     */
    public function attachFile($xpath, $path)
    {
        $this->selenium2Driver->attachFile($xpath, $path);
    }

    /**
     * Checks whether element visible located by it's XPath query.
     *
     * @param string $xpath
     *
     * @return Boolean
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function isVisible($xpath)
    {
        return $this->selenium2Driver->isVisible($xpath);
    }

    /**
     * Simulates a mouse over on the element.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function mouseOver($xpath)
    {
        $this->selenium2Driver->mouseOver($xpath);
    }

    /**
     * Brings focus to element.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function focus($xpath)
    {
        $this->selenium2Driver->focus($xpath);
    }

    /**
     * Removes focus from element.
     *
     * @param string $xpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function blur($xpath)
    {
        $this->selenium2Driver->blur($xpath);
    }

    /**
     * Presses specific keyboard key.
     *
     * @param string $xpath
     * @param string|int $char could be either char ('b') or char-code (98)
     * @param string $modifier keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function keyPress($xpath, $char, $modifier = null)
    {
        $this->selenium2Driver->keyPress($xpath, $char, $modifier);
    }

    /**
     * Pressed down specific keyboard key.
     *
     * @param string $xpath
     * @param string|int $char could be either char ('b') or char-code (98)
     * @param string $modifier keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function keyDown($xpath, $char, $modifier = null)
    {
        $this->selenium2Driver->keyDown($xpath, $char, $modifier);
    }

    /**
     * Pressed up specific keyboard key.
     *
     * @param string $xpath
     * @param string|int $char could be either char ('b') or char-code (98)
     * @param string $modifier keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function keyUp($xpath, $char, $modifier = null)
    {
        $this->selenium2Driver->keyUp($xpath, $char, $modifier);
    }

    /**
     * Drag one element onto another.
     *
     * @param string $sourceXpath
     * @param string $destinationXpath
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function dragTo($sourceXpath, $destinationXpath)
    {
        $this->selenium2Driver->dragTo($sourceXpath, $destinationXpath);
    }

    /**
     * Executes JS script.
     *
     * @param string $script
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function executeScript($script)
    {
        $this->selenium2Driver->executeScript($script);
    }

    /**
     * Evaluates JS script.
     *
     * The "return" keyword is optional in the script passed as argument. Driver implementations
     * must accept the expression both with and without the keyword.
     *
     * @param string $script
     *
     * @return mixed
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function evaluateScript($script)
    {
        $this->selenium2Driver->evaluateScript($script);
    }

    /**
     * Waits some time or until JS condition turns true.
     *
     * @param int $timeout timeout in milliseconds
     * @param string $condition JS condition
     *
     * @return bool
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function wait($timeout, $condition)
    {
        $this->selenium2Driver->wait($timeout, $condition);
    }

    /**
     * Set the dimensions of the window.
     *
     * @param int $width set the window width, measured in pixels
     * @param int $height set the window height, measured in pixels
     * @param string $name window name (null for the main window)
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function resizeWindow($width, $height, $name = null)
    {
        $this->selenium2Driver->resizeWindow($width, $height, $name);
    }

    /**
     * Maximizes the window if it is not maximized already.
     *
     * @param string $name window name (null for the main window)
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     */
    public function maximizeWindow($name = null)
    {
        $this->selenium2Driver->maximizeWindow($name);
    }

    /**
     * Submits the form.
     *
     * @param string $xpath Xpath.
     *
     * @throws UnsupportedDriverActionException When operation not supported by the driver
     * @throws DriverException                  When the operation cannot be done
     *
     * @see \Behat\Mink\Element\NodeElement::submitForm
     */
    public function submitForm($xpath)
    {
        $this->submitForm($xpath);
    }
}
