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

use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DoctrineOrmExtension implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function isSupported(ContainerBuilder $container, array $context = array())
    {
        $driver = sprintf('%s.driver', $context['bundlePrefix']);

        return
            $container->hasExtension('doctrine') &&
            $container->hasParameter($driver) &&
            $container->getParameter($driver) === SyliusResourceBundle::DRIVER_DOCTRINE_ORM
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ContainerBuilder $container, array $context = array())
    {
        $interfaces = $container->getParameter($context['bundlePrefix'].'_interface');

        foreach ($interfaces as $interface => $class) {
            $interfaces[$interface] = '%'.$class.'%';
        }

        $container->prependExtensionConfig('doctrine', array(
            'orm' => array(
                'resolve_target_entities' => $interfaces
            )
        ));
    }
}