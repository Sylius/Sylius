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
    /**
     * @param string $prefix
     * @param string $resourceName
     * @param array  $classes
     * @param mixed  $templates
     *
     * @return mixed
     */
    public function create($prefix, $resourceName, array $classes, $templates = null);

    /**
     * @return string
     */
    public function getSupportedDriver();
}
