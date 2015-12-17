<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

/*
 * Sylius front controller.
 * Dev environment.
 *
 * To develop on Sylius in vagrant set the SYLIUS_APP_DEV_PERMITTED to a non zero value.
 * e.g. in apache, through your vhost configuration file:
 *
 *   SetEnv SYLIUS_APP_DEV_PERMITTED 1
 */
if (!getenv("SYLIUS_APP_DEV_PERMITTED") && (
    isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !(in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1', '113.0.0.1'))
        || php_sapi_name() === 'cli-server')
)) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

require_once __DIR__.'/../app/bootstrap.php.cache';
Debug::enable();

require_once __DIR__.'/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);

$request = Request::createFromGlobals();

$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);
