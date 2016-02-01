<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\SymfonyPageObjectExtension\Page;

use Behat\Mink\Session;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory;
use Sylius\Behat\PageObjectExtension\Page\Page;
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
    public function __construct(Session $session, Factory $factory, array $parameters = [], RouterInterface $router)
    {
        parent::__construct($session, $factory, $parameters);

        $this->router = $router;
    }

    /**
     * @param array $urlParameters
     *
     * @return string
     */
    protected function getUrl(array $urlParameters = [])
    {
        if (null === $this->getRouteName()) {
            throw new \RuntimeException('You need to provide route name, null given');
        }

        return $this->router->generate($this->getRouteName(), $urlParameters, true);
    }

    abstract protected function getRouteName();
}
