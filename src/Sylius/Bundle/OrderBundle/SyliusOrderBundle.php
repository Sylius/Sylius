<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle;

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;

/**
 * Sales order management bundle.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusOrderBundle extends AbstractResourceBundle
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
    protected function getBundlePrefix()
    {
        return 'sylius_order';
    }

    /**
     * {@inheritdoc}
     */
    protected function getInterfaces()
    {
        return array(
            'Sylius\Component\Order\Model\OrderInterface'      => 'sylius.model.order.class',
            'Sylius\Component\Order\Model\OrderItemInterface'  => 'sylius.model.order_item.class',
            'Sylius\Component\Order\Model\AdjustmentInterface' => 'sylius.model.adjustment.class',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityNamespace()
    {
        return 'Sylius\Component\Order\Model';
    }
}
