<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\ChannelBundle;

use Sylius\ChannelBundle\DependencyInjection\Compiler\CompositeChannelContextPass;
use Sylius\ChannelBundle\DependencyInjection\Compiler\CompositeRequestResolverPass;
use Sylius\ChannelBundle\DependencyInjection\Compiler\RegisterChannelFactoryPass;
use Sylius\ResourceBundle\AbstractResourceBundle;
use Sylius\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Channels bundle.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusChannelBundle extends AbstractResourceBundle
{
    /**
     * {@inheritdoc}
     */
    public function getSupportedDrivers()
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
    protected function getModelNamespace()
    {
        return 'Sylius\Channel\Model';
    }
}
