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
 * Testing environment.
 */

require_once __DIR__.'/../app/bootstrap.php.cache';
require_once __DIR__.'/../app/AppKernel.php';

$request = Request::createFromGlobals();
if (!in_array($request->getClientIp(), array('127.0.0.1', '172.33.33.1', '::1', '10.0.0.1'))) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

$app = new AppKernel('test', true);

$stack = new Stack\Builder();
$app   = $stack
//    ->push('Sylius\Middleware\CookieGuard\CookieGuard')
    ->push('Sylius\Middleware\Locale\NegotiateLocale')
    ->resolve($app)
;

Request::enableHttpMethodParameterOverride();

$app->terminate($request, $app->handle($request)->send());
