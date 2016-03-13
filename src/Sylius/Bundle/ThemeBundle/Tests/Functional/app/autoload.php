<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Composer\Autoload\ClassLoader;

$loader = require __DIR__.'/../../../vendor/autoload.php';

require __DIR__.'/AppKernel.php';

return $loader;
