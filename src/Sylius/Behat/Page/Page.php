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
    public function isOpen(array $urlParameters = []): bool
    {
        try {
            $this->verify($urlParameters);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

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
     *
     * @throws UnexpectedPageException
     */
    protected function verifyUrl(array $urlParameters = []): void
    {
        if ($this->getSession()->getCurrentUrl() !== $this->getUrl($urlParameters)) {
            throw new UnexpectedPageException(sprintf('Expected to be on "%s" but found "%s" instead', $this->getUrl($urlParameters), $this->getSession()->getCurrentUrl()));
        }
    }

    protected function getParameter(string $name): string
    {
        return $this->parameters[$name] ?? null;
    }

    /**
     * Defines elements by returning an array with items being:
     *  - :elementName => :cssLocator
     *  - :elementName => [:selectorType => :locator]
     */
    protected function getDefinedElements(): array
    {
        return [];
    }

    /**
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

    protected function hasElement(string $name, array $parameters = []): bool
    {
        return $this->getDocument()->has('xpath', $this->createElement($name, $parameters)->getXpath());
    }

    protected function getSession(): Session
    {
        return $this->session;
    }

    protected function getDriver(): DriverInterface
    {
        return $this->session->getDriver();
    }

    protected function getDocument(): DocumentElement
    {
        if (null === $this->document) {
            $this->document = new DocumentElement($this->session);
        }

        return $this->document;
    }

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
     */
    private function getSelectorAsXpath($selector, SelectorsHandler $selectorsHandler): string
    {
        $selectorType = is_array($selector) ? key($selector) : 'css';
        $locator = is_array($selector) ? $selector[$selectorType] : $selector;

        return $selectorsHandler->selectorToXpath($selectorType, $locator);
    }

    /**
     * @param array$definedElements
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
