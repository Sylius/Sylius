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

if (!in_array(@$_SERVER['REMOTE_ADDR'], array(
    '127.0.0.1',
    '172.33.33.1',
    '::1',
))) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';

// Require kernel.
require_once __DIR__.'/../app/AppKernel.php';

// Initialize kernel and run the application.
$kernel = new AppKernel('test', true);
$request = Request::createFromGlobals();

Request::enableHttpMethodParameterOverride();

$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);
