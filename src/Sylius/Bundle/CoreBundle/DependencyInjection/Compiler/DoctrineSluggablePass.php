<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Disables softdeleteable filter when slugs are generated.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class DoctrineSluggablePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('stof_doctrine_extensions.listener.sluggable')) {
            $container
                ->getDefinition('stof_doctrine_extensions.listener.sluggable')
                ->addMethodCall('addManagedFilter', array('softdeleteable'))
            ;
        }
    }
}
