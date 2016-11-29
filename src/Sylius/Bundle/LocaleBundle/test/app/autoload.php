<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = require __DIR__.'/../../vendor/autoload.php';

require __DIR__.'/AppKernel.php';

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

return $loader;
