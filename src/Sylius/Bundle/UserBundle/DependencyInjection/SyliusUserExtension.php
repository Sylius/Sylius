<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class SyliusUserExtension extends AbstractResourceExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load(sprintf('driver/%s.xml', $config['driver']));

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $configFiles = [
            'services.xml',
        ];

        foreach ($configFiles as $configFile) {
            $loader->load($configFile);
        }

        $container->setParameter('sylius.user.resetting.token_ttl', $config['resetting']['token']['ttl']);
        $container->setParameter('sylius.user.resetting.token_length', $config['resetting']['token']['length']);
        $container->setParameter('sylius.user.resetting.pin_length', $config['resetting']['pin']['length']);

        $container
            ->getDefinition('sylius.form.type.customer_registration')
            ->addArgument(new Reference('sylius.repository.customer'))
        ;
        $container
            ->getDefinition('sylius.form.type.customer_simple_registration')
            ->addArgument(new Reference('sylius.repository.customer'))
        ;
        $container
            ->getDefinition('sylius.form.type.customer_guest')
            ->addArgument(new Reference('sylius.form.subscriber.guest_customer'))
        ;
        $container
            ->getDefinition('sylius.form.type.customer')
            ->addArgument(new Reference('sylius.form.event_subscriber.add_user_type'))
        ;
    }
}
