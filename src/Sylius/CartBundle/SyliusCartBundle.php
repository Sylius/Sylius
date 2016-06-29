<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\CartBundle;

use Sylius\ResourceBundle\AbstractResourceBundle;
use Sylius\ResourceBundle\SyliusResourceBundle;

/**
 * Flexible shopping cart system for Symfony2 ecommerce applications.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusCartBundle extends AbstractResourceBundle
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
    protected function getBundlePrefix()
    {
        return 'sylius_order';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Sylius\Cart\Model';
    }
}
