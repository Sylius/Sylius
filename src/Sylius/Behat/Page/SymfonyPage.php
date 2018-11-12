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

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use Symfony\Component\Routing\RouterInterface;

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

    public function __construct(Session $session, array $parameters, RouterInterface $router)
    {
        parent::__construct($session, $parameters);

        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function getRouteName(): string;

    /**
     * {@inheritdoc}
     */
    protected function getUrl(array $urlParameters = []): string
    {
        $path = $this->router->generate($this->getRouteName(), $urlParameters + static::$additionalParameters);

        $replace = [];
        foreach (static::$additionalParameters as $key => $value) {
            $replace[sprintf('&%s=%s', $key, $value)] = '';
            $replace[sprintf('?%s=%s&', $key, $value)] = '?';
            $replace[sprintf('?%s=%s', $key, $value)] = '';
        }

        $path = str_replace(array_keys($replace), $replace, $path);

        return $this->makePathAbsolute($path);
    }

    /**
     * {@inheritdoc}
     */
    protected function verifyUrl(array $urlParameters = []): void
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
     * @throws UnexpectedPageException
     */
    public function verifyRoute(array $requiredUrlParameters = []): void
    {
        $url = $this->getDriver()->getCurrentUrl();
        $path = parse_url($url)['path'];

        $path = preg_replace('#^/app(_dev|_test|_test_cached)?\.php/#', '/', $path);
        $matchedRoute = $this->router->match($path);

        $this->verifyRouteName($matchedRoute, $url);
        $this->verifyRouteParameters($requiredUrlParameters, $matchedRoute);
    }

    /**
     * @throws UnexpectedPageException
     */
    private function verifyRouteName(array $matchedRoute, string $url): void
    {
        if ($matchedRoute['_route'] !== $this->getRouteName()) {
            throw new UnexpectedPageException(
                sprintf(
                    "Matched route '%s' does not match the expected route '%s' for URL '%s'",
                    $matchedRoute['_route'],
                    $this->getRouteName(),
                    $url
                )
            );
        }
    }

    /**
     * @throws UnexpectedPageException
     */
    private function verifyRouteParameters(array $requiredUrlParameters, array $matchedRoute): void
    {
        foreach ($requiredUrlParameters as $key => $value) {
            if (!isset($matchedRoute[$key]) || $matchedRoute[$key] !== $value) {
                throw new UnexpectedPageException(
                    sprintf(
                        "Matched route does not match the expected parameter '%s'='%s' (%s found)",
                        $key,
                        $value,
                        $matchedRoute[$key] ?? 'null'
                    )
                );
            }
        }
    }

    /**
     * @todo it really shouldn't be here :)
     */
    protected function waitForModalToAppear(NodeElement $modalContainer, string $appearClass = 'in'): void
    {
        $this->getDocument()->waitFor(1, function () use ($modalContainer, $appearClass) {
            return false !== strpos($modalContainer->getAttribute('class'), $appearClass);
        });
    }

    final protected function makePathAbsolute(string $path): string
    {
        $baseUrl = rtrim($this->getParameter('base_url'), '/') . '/';

        return 0 !== strpos($path, 'http') ? $baseUrl . ltrim($path, '/') : $path;
    }
}
