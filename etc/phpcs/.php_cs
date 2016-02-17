<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$fixers = include 'common.php';

$finder = Symfony\Component\Finder\Finder::create()
    ->files()
    ->notName('*Spec.php')
    ->name('*.php')
    ->in(['app/migrations','src'])
;

return Symfony\CS\Config\Config::create()
        ->setUsingCache(true)
        ->fixers($fixers)
        ->finder($finder)
;
