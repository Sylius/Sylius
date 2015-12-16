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

use Behat\Mink\Session;
use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\ElementNotFoundException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
abstract class SymfonyPage extends Page
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @param Session $session
     * @param Factory $factory
     * @param array $parameters
     * @param RouterInterface $router
     */
    public function __construct(Session $session, Factory $factory, array $parameters = array(), RouterInterface $router)
    {
        parent::__construct($session, $factory, $parameters);

        $this->router = $router;
    }

    /**
     * @param string $locator
     *
     * @throws ElementNotFoundException
     */
    public function pressRadio($locator)
    {
        $radio = $this->findField($locator);

        if (null === $radio) {
            throw new ElementNotFoundException(sprintf('"%s" element is not present on the page', $locator));
        }

        $this->fillField($radio->getAttribute('name'), $radio->getAttribute('value'));
    }

    /**
     * @param array $urlParameters
     */
    public function assertRoute(array $urlParameters = array())
    {
        $this->verify($urlParameters);
    }

    /**
     * @param array $urlParameters
     *
     * @return string
     */
    protected function getUrl(array $urlParameters = array())
    {
        if (null === $this->getRouteName()) {
            throw new \RuntimeException('You need to provide route name, null given');
        }

        return $this->router->generate($this->getRouteName(), $urlParameters, true);
    }

    abstract public function getRouteName();
}
