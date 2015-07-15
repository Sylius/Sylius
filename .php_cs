<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\CS\FixerInterface;

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->notName('*.yml')
    ->notName('*.xml')
    ->notName('*Spec.php')
    ->exclude('app')
;

return Symfony\CS\Config\Config::create()->finder($finder);
