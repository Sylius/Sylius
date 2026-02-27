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

namespace Sylius\Bundle\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

trait RedirectTrait
{
    private function getRedirectUrl(Request $request, ?RouterInterface $router, string $fallbackRoute = 'sylius_shop_homepage', array $fallbackParameters = []): string
    {
        if (null === $router) {
            return $request->headers->get('referer', $request->getSchemeAndHttpHost());
        }

        $redirect = $request->attributes->get('_sylius', [])['redirect'] ?? null;
        if (null !== $redirect) {
            return $router->generate($redirect);
        }

        $referer = $request->headers->get('referer');
        if (null !== $referer) {
            try {
                $match = $router->match(parse_url($referer, \PHP_URL_PATH));
                $route = $match['_route'];
                unset($match['_route'], $match['_controller']);

                return $router->generate($route, array_merge($match, $fallbackParameters));
            } catch (\Exception $e) {
            }
        }

        return $router->generate($fallbackRoute, $fallbackParameters);
    }
}
