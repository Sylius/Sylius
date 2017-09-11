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

use Symfony\Component\HttpFoundation\Request;

/*
 * Sylius front controller.
 * Staging environment.
 */

require __DIR__.'/../vendor/autoload.php';

$kernel = new AppKernel('staging', false);

$request = Request::createFromGlobals();

$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);
