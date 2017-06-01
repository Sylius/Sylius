<?php

/*
 * This file is part of the Behat
 *
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

spl_autoload_register(function($class) {
    if (false !== strpos($class, 'Behat\\MinkLogExtension')) {
        require_once(__DIR__.'/src/'.str_replace('\\', '/', $class).'.php');
        return true;
    }
}, true, false);

return new Sylius\Behat\MinkLogExtension\ServiceContainer\MinkLogExtension;
