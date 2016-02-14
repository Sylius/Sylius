<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\DriverException;
use Behat\Mink\Selector\SelectorsHandler;
use Behat\Mink\Session;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
abstract class Page
{
    /**
     * @var array
     */
    protected $elements = [];

    /**
     * @var Session
     */
    private $session;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var DocumentElement|null
     */
    private $document;

    /**
     * @param Session $session
     * @param array $parameters
     */
    public function __construct(Session $session, array $parameters = [])
    {
        $this->session = $session;
        $this->parameters = $parameters;
    }

    /**
     * @param array $urlParameters
     *
     * @throws UnexpectedPageException If page is not opened successfully
     */
    public function open(array $urlParameters = [])
    {
        $this->tryToOpen($urlParameters);
        $this->verify($urlParameters);
    }

    /**
     * @param array $urlParameters
     */
    public function tryToOpen(array $urlParameters = [])
    {
        $this->getDriver()->visit($this->getUrl($urlParameters));
    }

    /**
     * @param array $urlParameters
     *
     * @throws UnexpectedPageException
     */
    public function verify(array $urlParameters)
    {
        $this->verifyResponse();
        $this->verifyUrl($urlParameters);
        $this->verifyPage();
    }

    /**
     * @param array $urlParameters
     *
     * @return bool
     */
    public function isOpen(array $urlParameters = [])
    {
        try {
            $this->verify($urlParameters);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    abstract protected function getPath();

    /**
     * @return string
     */
    protected function getName()
    {
        return preg_replace('/^.*\\\(.*?)$/', '$1', get_called_class());
    }

    /**
     * @param array $urlParameters
     *
     * @return string
     */
    protected function getUrl(array $urlParameters = [])
    {
        return $this->unmaskUrl($urlParameters);
    }

    /**
     * @throws UnexpectedPageException
     */
    protected function verifyResponse()
    {
        try {
            $statusCode = $this->getDriver()->getStatusCode();

            if ($this->isErrorResponse($statusCode)) {
                $currentUrl = $this->getDriver()->getCurrentUrl();
                $message = sprintf('Could not open the page: "%s". Received an error status code: %s', $currentUrl, $statusCode);

                throw new UnexpectedPageException($message);
            }
        } catch (DriverException $exception) {
            // ignore drivers which cannot check the response status code
        }
    }

    /**
     * Overload to verify if the current url matches the expected one. Throw an exception otherwise.
     *
     * @param array $urlParameters
     */
    protected function verifyUrl(array $urlParameters = [])
    {
        if ($this->getDriver()->getCurrentUrl() !== $this->getUrl($urlParameters)) {
            throw new UnexpectedPageException(sprintf('Expected to be on "%s" but found "%s" instead', $this->getUrl($urlParameters), $this->getDriver()->getCurrentUrl()));
        }
    }

    /**
     * Overload to verify if we're on an expected page. Throw an exception otherwise.
     *
     * @throws UnexpectedPageException
     */
    protected function verifyPage()
    {
    }

    /**
     * @param int $statusCode
     *
     * @return bool
     */
    protected function isErrorResponse($statusCode)
    {
        return 400 <= $statusCode && $statusCode < 600;
    }

    /**
     * @param string $name
     *
     * @return NodeElement
     */
    protected function getParameter($name)
    {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
    }

    /**
     * @param string $name
     *
     * @return NodeElement
     */
    public function getElement($name)
    {
        $element = $this->createElement($name);

        if (!$this->getDocument()->has('xpath', $element->getXpath())) {
            throw new ElementNotFoundException(sprintf('"%s" element is not present on the page', $name));
        }

        return $element;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    protected function hasElement($name)
    {
        return $this->getDocument()->has('xpath', $this->createElement($name)->getXpath());
    }

    /**
     * @param string $name
     *
     * @return NodeElement
     */
    protected function createElement($name)
    {
        if (isset($this->elements[$name])) {
            return new NodeElement(
                $this->getSelectorAsXpath($this->elements[$name], $this->session->getSelectorsHandler()),
                $this->session
            );
        }

        throw new \InvalidArgumentException();
    }

    /**
     * @return Session
     */
    protected function getSession()
    {
        return $this->session;
    }

    /**
     * @return DriverInterface
     */
    protected function getDriver()
    {
        return $this->session->getDriver();
    }

    /**
     * @return DocumentElement
     */
    protected function getDocument()
    {
        if (null === $this->document) {
            $this->document = new DocumentElement($this->session);
        }

        return $this->document;
    }

    /**
     * @param array $urlParameters
     *
     * @return string
     */
    private function unmaskUrl(array $urlParameters)
    {
        $url = $this->getPath();

        foreach ($urlParameters as $parameter => $value) {
            $url = str_replace(sprintf('{%s}', $parameter), $value, $url);
        }

        return $url;
    }

    /**
     * @param string|array $selector
     * @param SelectorsHandler $selectorsHandler
     *
     * @return string
     */
    private function getSelectorAsXpath($selector, SelectorsHandler $selectorsHandler)
    {
        $selectorType = is_array($selector) ? key($selector) : 'css';
        $locator = is_array($selector) ? $selector[$selectorType] : $selector;

        return $selectorsHandler->selectorToXpath($selectorType, $locator);
    }
}
