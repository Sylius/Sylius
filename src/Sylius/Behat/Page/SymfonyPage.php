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
     * @var array
     */
    protected static $additionalParameters = ['_locale' => 'en_US'];

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
     * {@inheritdoc}
     */
    protected function getUrl(array $urlParameters = [])
    {
        $path = $this->router->generate($this->getRouteName(), $urlParameters + static::$additionalParameters);

        $replace = [];
        foreach (static::$additionalParameters as $key => $value) {
            $replace[sprintf('&%s=%s', $key, $value)] = '';
            $replace[sprintf('?%s=%s&', $key, $value)] = '?';
            $replace[sprintf('?%s=%s', $key, $value)] = '';
        }

        $path = str_replace(array_keys($replace), array_values($replace), $path);

        return $this->makePathAbsolute($path);
    }

    /**
     * {@inheritdoc}
     */
    protected function verifyUrl(array $urlParameters = [])
    {
        $url = $this->getDriver()->getCurrentUrl();
        $path = parse_url($url)['path'];

        $path = preg_replace('#^/app(_dev|_test|_test_cached)?\.php/#', '/', $path);
        $matchedRoute = $this->router->match($path);

        if (isset($matchedRoute['_locale'])) {
            $urlParameters += ['_locale' => $matchedRoute['_locale']];
        }

        parent::verifyUrl($urlParameters);
    }

    /**
     * @param NodeElement $modalContainer
     * @param string $appearClass
     *
     * @todo it really shouldn't be here :)
     */
    protected function waitForModalToAppear(NodeElement $modalContainer, $appearClass = 'in')
    {
        $this->getDocument()->waitFor(1, function () use ($modalContainer, $appearClass) {
            return false !== strpos($modalContainer->getAttribute('class'), $appearClass);
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
