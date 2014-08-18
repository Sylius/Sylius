<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Bundle\SettingsBundle\DependencyInjection\Compiler\RegisterSchemasPass;

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
    public static function getSupportedDrivers()
    {
        return array(
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM
        );
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterSchemasPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getBundlePrefix()
    {
        return 'sylius_settings';
    }

    /**
     * {@inheritdoc}
     */
    protected function getInterfaces()
    {
        return array(
            'Sylius\Bundle\SettingsBundle\Model\ParameterInterface' => 'sylius.model.parameter.class',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityNamespace()
    {
        return 'Sylius\Bundle\SettingsBundle\Model';
    }
}
