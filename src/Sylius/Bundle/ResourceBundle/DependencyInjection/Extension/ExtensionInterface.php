<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Extension;

use Symfony\Component\DependencyInjection\ContainerBuilder;

interface ExtensionInterface
{
    public function configure(ContainerBuilder $container, array $configuration, array $context = array());
    public function isSupported($configure);
}