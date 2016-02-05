<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\PageObjectExtension\PageObject;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Exception\DriverException;
use Behat\Mink\Session;
use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\ElementNotFoundException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\UnexpectedPageException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory;
use SensioLabs\Behat\PageObjectExtension\PageObject\PageObject;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
abstract class Page implements PageObject
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
     * @var Factory
     */
    private $factory;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var DocumentElement
     */
    private $document;

    /**
     * @param Session $session
     * @param Factory $factory
     * @param array $parameters
     */
    public function __construct(Session $session, Factory $factory, array $parameters = [])
    {
        $this->session = $session;
        $this->factory = $factory;
        $this->parameters = $parameters;

        $this->document = new DocumentElement($session);
    }

    /**
     * @param array $urlParameters
     *
     * @return Page
     */
    public function open(array $urlParameters = [])
    {
        $url = $this->getUrl($urlParameters);

        $this->getDriver()->visit($url);

        $this->verify($urlParameters);

        return $this;
    }

    /**
     * @param array $urlParameters
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
        return $this->makeSurePathIsAbsolute($this->unmaskUrl($urlParameters));
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
     * @return string|null
     */
    protected function getParameter($name)
    {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
    }

    /**
     * @param string $name
     *
     * @return Element
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
     * @return Element
     */
    protected function createElement($name)
    {
        if (isset($this->elements[$name])) {
            return $this->factory->createInlineElement($this->elements[$name]);
        }

        return $this->factory->createElement($name);
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
        return $this->document;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function makeSurePathIsAbsolute($path)
    {
        $baseUrl = rtrim($this->getParameter('base_url'), '/').'/';

        return 0 !== strpos($path, 'http') ? $baseUrl.ltrim($path, '/') : $path;
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
}
