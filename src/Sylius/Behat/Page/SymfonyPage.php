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

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
abstract class SymfonyPage extends Page implements SymfonyPageInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @param Session $session
     * @param array $parameters
     * @param RouterInterface $router
     */
    public function __construct(Session $session, array $parameters, RouterInterface $router)
    {
        parent::__construct($session, $parameters);

        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function getRouteName();

    /**
     * @param array $urlParameters
     *
     * @return string
     */
    protected function getUrl(array $urlParameters = [])
    {
        $path = $this->router->generate($this->getRouteName(), $urlParameters);

        return $this->makePathAbsolute($path);
    }

    /**
     * @param NodeElement $modalContainer
     *
     * @todo it really shouldn't be here :)
     */
    protected function waitForModalToAppear(NodeElement $modalContainer)
    {
        $this->getDocument()->waitFor(1, function () use ($modalContainer) {
            return false !== strpos($modalContainer->getAttribute('class'), 'in');
        });
    }

    /**
     * @param string $path
     *
     * @return string
     */
    final protected function makePathAbsolute($path)
    {
        $baseUrl = rtrim($this->getParameter('base_url'), '/').'/';

        return 0 !== strpos($path, 'http') ? $baseUrl.ltrim($path, '/') : $path;
    }
}
