<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PayumBundle;

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;

class SyliusPayumBundle extends AbstractResourceBundle
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
        return 'sylius_payum';
    }

    /**
     * {@inheritdoc}
     */
    protected function getInterfaces()
    {
        return array(
            'Payum\Core\Security\TokenInterface' => 'sylius.model.payment_security_token.class',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityNamespace()
    {
        return 'Sylius\Bundle\PayumBundle\Model';
    }
}
