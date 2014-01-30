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

use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class AbstractFactory implements DatabaseDriverFactoryInterface
{
    protected $container;

    public function __construct(ContainerBuilder $container)
    {
        $this->container = $container;
    }
}
