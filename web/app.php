<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Request;

/*
 * Sylius front controller.
 * Live (production) environment.
 */

require_once __DIR__.'/../app/bootstrap.php.cache';
require_once __DIR__.'/../app/AppKernel.php';
require_once __DIR__.'/../app/AppCache.php';

$kernel = new AppCache(
    new AppKernel('prod', false)
);

$stack = new Stack\Builder();
$app   = $stack
//    ->push('Sylius\Middleware\CookieGuard\CookieGuard')
    ->push('Sylius\Middleware\Locale\NegotiateLocale')
    ->resolve($app)
;

Request::enableHttpMethodParameterOverride();

$app->terminate($request, $app->handle($request)->send());
