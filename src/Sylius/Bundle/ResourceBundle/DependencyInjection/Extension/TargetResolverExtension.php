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

use Sylius\Bundle\ResourceBundle\DependencyInjection\AbstractResourceExtension;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TargetResolverExtension implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function isSupported($configure)
    {
        return $configure & AbstractResourceExtension::CONFIGURE_TARGET_RESOLVER;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ContainerBuilder $container, array $configuration = array(), array $context = array())
    {
        $interfaces = $container->getParameter($context['bundle_name'].'_interface');
        $driver =  $container->getParameter(sprintf('%s.driver', $context['bundle_name']));

        foreach ($interfaces as $interface => $class) {
            $interfaces[$interface] = '%'.$class.'%';
        }

        switch ($driver) {
            case SyliusResourceBundle::DRIVER_DOCTRINE_ORM:
                $container->prependExtensionConfig('doctrine', array(
                    'orm' => array(
                        'resolve_target_entities' => $interfaces
                    )
                ));
                break;
            case SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM:
                $container->prependExtensionConfig('doctrine_mongodb', array(
                    'resolve_target_documents' => $interfaces
                ));
                break;
        }



    }

}