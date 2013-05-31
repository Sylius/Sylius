<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!is_dir(__DIR__.'/../vendor')) {
    exec('cd .. && wget http://getcomposer.org/composer.phar && php composer.phar install --dev --no-interaction');
}

header('Location: /installer');
