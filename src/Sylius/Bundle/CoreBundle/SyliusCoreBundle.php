<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle;

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\DoctrineSluggablePass;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Sylius core bundle.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusCoreBundle extends AbstractResourceBundle
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

        $container->addCompilerPass(new DoctrineSluggablePass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getBundlePrefix()
    {
        return 'sylius_core';
    }

    /**
     * {@inheritdoc}
     */
    protected function getInterfaces()
    {
        return array(
            'Sylius\Component\Core\Model\UserInterface'                => 'sylius.model.user.class',
            'Sylius\Component\Core\Model\UserOAuthInterface'           => 'sylius.model.user_oauth.class',
            'Sylius\Component\Core\Model\GroupInterface'               => 'sylius.model.group.class',
            'Sylius\Component\Core\Model\ProductVariantImageInterface' => 'sylius.model.product_variant_image.class',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityNamespace()
    {
        return 'Sylius\Component\Core\Model';
    }
}
