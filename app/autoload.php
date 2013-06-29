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
use Doctrine\Common\Annotations\AnnotationReader;

$loader = require __DIR__.'/../vendor/autoload.php';

// Intl stubs.
if (!function_exists('intl_get_error_code')) {
    require_once __DIR__.'/../vendor/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs/functions.php';
}

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

AnnotationReader::addGlobalIgnoredName('BeforeScenario');
AnnotationReader::addGlobalIgnoredName('Given');
AnnotationReader::addGlobalIgnoredName('When');
AnnotationReader::addGlobalIgnoredName('Then');
AnnotationReader::addGlobalIgnoredName('BeforeSuite');
AnnotationReader::addGlobalIgnoredName('AfterScenario');

return $loader;
