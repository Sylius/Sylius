<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Driver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface DatabaseDriverInterface
{
    public function load(array $classes);

    public function getSupportedDriver();
}
