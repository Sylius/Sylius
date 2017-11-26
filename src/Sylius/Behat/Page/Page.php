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

namespace Sylius\Behat\Page;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Selector\SelectorsHandler;
use Behat\Mink\Session;

abstract class Page implements PageInterface
{
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
     * @param array $parameters
     */
    public function __construct(Session $session, array $parameters = [])
    {
        $this->session = $session;
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function open(array $urlParameters = []): void
    {
        $this->tryToOpen($urlParameters);
        $this->verify($urlParameters);
    }

    /**
     * {@inheritdoc}
     */
    public function tryToOpen(array $urlParameters = []): void
    {
        $this->getSession()->visit($this->getUrl($urlParameters));
    }

    /**
     * {@inheritdoc}
     */
    public function verify(array $urlParameters = []): void
    {
        $this->verifyStatusCode();
        $this->verifyUrl($urlParameters);
    }

    /**
     * {@inheritdoc}
     */
    public function isOpen(array $urlParameters = [])
    {
        try {
            $this->verify($urlParameters);
        } catch (\Throwable $e) {
            return false;
        }

        return true;
    }

    /**
     * @param array $urlParameters
     *
     * @return string
     */
    abstract protected function getUrl(array $urlParameters = []): string;

    /**
     * @throws UnexpectedPageException
     */
    protected function verifyStatusCode(): void
    {
        try {
            $statusCode = $this->getSession()->getStatusCode();
        } catch (DriverException $exception) {
            return; // Ignore drivers which cannot check the response status code
        }

        if ($statusCode >= 200 && $statusCode <= 299) {
            return;
        }

        $currentUrl = $this->getSession()->getCurrentUrl();
        $message = sprintf('Could not open the page: "%s". Received an error status code: %s', $currentUrl, $statusCode);

        throw new UnexpectedPageException($message);
    }

    /**
     * Overload to verify if the current url matches the expected one. Throw an exception otherwise.
     *
     * @param array $urlParameters
     *
     * @throws UnexpectedPageException
     */
    protected function verifyUrl(array $urlParameters = []): void
    {
        if ($this->getSession()->getCurrentUrl() !== $this->getUrl($urlParameters)) {
            throw new UnexpectedPageException(sprintf('Expected to be on "%s" but found "%s" instead', $this->getUrl($urlParameters), $this->getSession()->getCurrentUrl()));
        }
    }

    /**
     * @param string $name
     *
     * @return NodeElement
     */
    protected function getParameter(string $name): NodeElement
    {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
    }

    /**
     * Defines elements by returning an array with items being:
     *  - :elementName => :cssLocator
     *  - :elementName => [:selectorType => :locator]
     *
     * @return array
     */
    protected function getDefinedElements(): array
    {
        return [];
    }

    /**
     * @param string $name
     * @param array $parameters
     *
     * @return NodeElement
     *
     * @throws ElementNotFoundException
     */
    protected function getElement(string $name, array $parameters = []): NodeElement
    {
        $element = $this->createElement($name, $parameters);

        if (!$this->getDocument()->has('xpath', $element->getXpath())) {
            throw new ElementNotFoundException(
                $this->getSession(),
                sprintf('Element named "%s" with parameters %s', $name, implode(', ', $parameters)),
                'xpath',
                $element->getXpath()
            );
        }

        return $element;
    }

    /**
     * @param string $name
     * @param array $parameters
     *
     * @return bool
     */
    protected function hasElement(string $name, array $parameters = []): bool
    {
        return $this->getDocument()->has('xpath', $this->createElement($name, $parameters)->getXpath());
    }

    /**
     * @return Session
     */
    protected function getSession(): Session
    {
        return $this->session;
    }

    /**
     * @return DriverInterface
     */
    protected function getDriver(): DriverInterface
    {
        return $this->session->getDriver();
    }

    /**
     * @return DocumentElement
     */
    protected function getDocument(): DocumentElement
    {
        if (null === $this->document) {
            $this->document = new DocumentElement($this->session);
        }

        return $this->document;
    }

    /**
     * @param string $name
     * @param array $parameters
     *
     * @return NodeElement
     */
    private function createElement(string $name, array $parameters = []): NodeElement
    {
        $definedElements = $this->getDefinedElements();

        if (!isset($definedElements[$name])) {
            throw new \InvalidArgumentException(sprintf(
                'Could not find a defined element with name "%s". The defined ones are: %s.',
                $name,
                implode(', ', array_keys($definedElements))
            ));
        }

        $elementSelector = $this->resolveParameters($name, $parameters, $definedElements);

        return new NodeElement(
            $this->getSelectorAsXpath($elementSelector, $this->session->getSelectorsHandler()),
            $this->session
        );
    }

    /**
     * @param string|array $selector
     *
     * @return string
     */
    private function getSelectorAsXpath($selector, SelectorsHandler $selectorsHandler): string
    {
        $selectorType = is_array($selector) ? key($selector) : 'css';
        $locator = is_array($selector) ? $selector[$selectorType] : $selector;

        return $selectorsHandler->selectorToXpath($selectorType, $locator);
    }

    /**
     * @param string $name
     * @param array $parameters
     * @param array$definedElements
     *
     * @return string
     */
    private function resolveParameters(string $name, array $parameters, array $definedElements): string
    {
        if (!is_array($definedElements[$name])) {
            return strtr($definedElements[$name], $parameters);
        }

        array_map(
            function ($definedElement) use ($parameters) {
                return strtr($definedElement, $parameters);
            }, $definedElements[$name]
        );

        return $definedElements[$name];
    }
}
