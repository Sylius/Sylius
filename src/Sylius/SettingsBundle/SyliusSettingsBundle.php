<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\SettingsBundle;

use Sylius\ResourceBundle\AbstractResourceBundle;
use Sylius\ResourceBundle\SyliusResourceBundle;
use Sylius\SettingsBundle\DependencyInjection\Compiler\RegisterResolversPass;
use Sylius\SettingsBundle\DependencyInjection\Compiler\RegisterSchemasPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Settings system for ecommerce Symfony2 applications.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusSettingsBundle extends AbstractResourceBundle
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

        $container->addCompilerPass(new RegisterSchemasPass());
        $container->addCompilerPass(new RegisterResolversPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Sylius\SettingsBundle\Model';
    }
}
