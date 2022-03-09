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
    public function __construct(private RouterInterface $router, private LocaleContextInterface $localeContext)
    {
    }

    public function match($pathinfo): array
    {
        return $this->router->match($pathinfo);
    }

    public function generate($name, $parameters = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        $url = $this->router->generate($name, $parameters, $referenceType);

        if (!str_contains($url, '_locale')) {
            return $url;
        }

        return $this->removeUnusedQueryArgument($url, '_locale', $this->localeContext->getLocaleCode());
    }

    public function setContext(RequestContext $context): void
    {
        $this->router->setContext($context);
    }

    public function getContext(): RequestContext
    {
        return $this->router->getContext();
    }

    public function getRouteCollection(): RouteCollection
    {
        return $this->router->getRouteCollection();
    }

    public function warmUp($cacheDir): void
    {
        if ($this->router instanceof WarmableInterface) {
            $this->router->warmUp($cacheDir);
        }
    }

    private function removeUnusedQueryArgument(string $url, string $key, string $value): string
    {
        $replace = [
            sprintf('&%s=%s', $key, $value) => '',
            sprintf('?%s=%s&', $key, $value) => '?',
            sprintf('?%s=%s', $key, $value) => '',
        ];

        return str_replace(array_keys($replace), $replace, $url);
    }
}
