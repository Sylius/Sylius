<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;

/*
 * Sylius front controller.
 * Live (production) environment.
 */

$loader = require_once __DIR__.'/../sylius/bootstrap.php.cache';

//$loader = new ApcClassLoader('sylius', $loader);
//$loader->register(true);

require_once __DIR__.'/../sylius/SyliusKernel.php';
//require_once __DIR__.'/../sylius/SyliusCache.php';

$kernel = new SyliusKernel('live', false);
//$kernel = new SyliusCache($kernel);

$request = Request::createFromGlobals();

Request::enableHttpMethodParameterOverride();

$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);
