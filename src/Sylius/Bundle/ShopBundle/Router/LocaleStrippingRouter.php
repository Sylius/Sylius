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

namespace Sylius\Bundle\ShopBundle\Router;

use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

final class LocaleStrippingRouter implements RouterInterface, WarmableInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var LocaleContextInterface
     */
    private $localeContext;

    /**
     * @param RouterInterface $router
     * @param LocaleContextInterface $localeContext
     */
    public function __construct(RouterInterface $router, LocaleContextInterface $localeContext)
    {
        $this->router = $router;
        $this->localeContext = $localeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathinfo): array
    {
        return $this->router->match($pathinfo);
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = [], $absolute = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        $url = $this->router->generate($name, $parameters, $absolute);

        if (false === strpos($url, '_locale')) {
            return $url;
        }

        return $this->removeUnusedQueryArgument($url, '_locale', $this->localeContext->getLocaleCode());
    }

    /**
     * {@inheritdoc}
     */
    public function setContext(RequestContext $context): void
    {
        $this->router->setContext($context);
    }

    /**
     * {@inheritdoc}
     */
    public function getContext(): RequestContext
    {
        return $this->router->getContext();
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection(): RouteCollection
    {
        return $this->router->getRouteCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir): void
    {
        if ($this->router instanceof WarmableInterface) {
            $this->router->warmUp($cacheDir);
        }
    }

    /**
     * @param string $url
     * @param string $key
     * @param string $value
     *
     * @return string
     */
    private function removeUnusedQueryArgument(string $url, string $key, string $value): string
    {
        $replace = [
            sprintf('&%s=%s', $key, $value) => '',
            sprintf('?%s=%s&', $key, $value) => '?',
            sprintf('?%s=%s', $key, $value) => '',
        ];

        return str_replace(array_keys($replace), array_values($replace), $url);
    }
}
