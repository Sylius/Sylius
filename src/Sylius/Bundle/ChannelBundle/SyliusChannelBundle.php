<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ChannelBundle;

use Sylius\Bundle\ChannelBundle\DependencyInjection\Compiler\CompositeChannelContextPass;
use Sylius\Bundle\ChannelBundle\DependencyInjection\Compiler\CompositeRequestResolverPass;
use Sylius\Bundle\ChannelBundle\DependencyInjection\Compiler\RegisterChannelFactoryPass;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Channels bundle.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class SyliusChannelBundle extends AbstractResourceBundle
{
    /**
     * {@inheritdoc}
     */
    public static function getSupportedDrivers()
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterChannelFactoryPass());

        $container->addCompilerPass(new CompositeChannelContextPass());
        $container->addCompilerPass(new CompositeRequestResolverPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelInterfaces()
    {
        return [
            ChannelInterface::class => 'sylius.model.channel.class',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Sylius\Component\Channel\Model';
    }
}
