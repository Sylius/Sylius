<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Factory;

interface DatabaseDriverFactoryInterface
{
    public function create($prefix, $resourceName, array $classes, $templates = null);

    public function getSupportedDriver();
}