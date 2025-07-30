<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\UiBundle\Util\Ux;

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\UX\LiveComponent\Util\UrlFactory as BaseUrlFactory;

/**
 * @internal
 *
 * Temporary override of the Symfony UX LiveComponent UrlFactory to resolve
 * a compatibility issue introduced in version 2.28 of symfony/ux-live-component.
 *
 * This override ensures consistent URL generation behavior within Live Components
 * until the upstream library provides a native fix.
 *
 * This class is intended as a stopgap and should be removed once the issue is
 * resolved in a future release of symfony/ux-live-component.
 */
final class UrlFactory extends BaseUrlFactory
{
    public function __construct(
        private readonly RouterInterface $router,
    ) {
        parent::__construct($router);
    }

    /**
     * @param array<string, mixed> $pathMappedProps
     * @param array<string, mixed> $queryMappedProps
     */
    public function createFromPreviousAndProps(
        string $previousUrl,
        array $pathMappedProps,
        array $queryMappedProps,
    ): ?string {
        $parsed = parse_url($previousUrl);
        if (false === $parsed) {
            return null;
        }

        $previousUrl = $parsed['path'] ?? '';
        if (isset($parsed['query'])) {
            $previousUrl .= '?' . $parsed['query'];
        }

        try {
            $newUrl = $this->createPath($previousUrl, $pathMappedProps);
        } catch (MethodNotAllowedException|MissingMandatoryParametersException|ResourceNotFoundException) {
            return null;
        }

        return $this->replaceQueryString(
            $newUrl,
            array_merge(
                $this->getPreviousQueryParameters($parsed['query'] ?? ''),
                $this->getRemnantProps($newUrl),
                $queryMappedProps,
            ),
        );
    }

    /**
     * @param array<string, mixed> $props
     */
    private function createPath(string $previousUrl, array $props): string
    {
        return $this->router->generate(
            $this->router->match($previousUrl)['_route'] ?? '',
            $props,
        );
    }

    /**
     * @param array<string, mixed> $props
     */
    private function replaceQueryString(string $url, array $props): string
    {
        $queryString = http_build_query($props);

        return preg_replace('/[?#].*/', '', $url)
            . ('' !== $queryString ? '?' : '')
            . $queryString;
    }

    /**
     * Keep the query parameters of the previous request.
     *
     * @return array<string, mixed>
     */
    private function getPreviousQueryParameters(string $query): array
    {
        parse_str($query, $previousQueryParams);

        return $previousQueryParams;
    }

    /**
     * Symfony router will set props in query if they do not match route parameter.
     *
     * @return array<string, mixed>
     */
    private function getRemnantProps(string $newUrl): array
    {
        parse_str(parse_url($newUrl)['query'] ?? '', $remnantQueryParams);

        return $remnantQueryParams;
    }
}
