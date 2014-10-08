<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PaymentBundle;

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;

/**
 * Payments component for Symfony2 applications.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusPaymentBundle extends AbstractResourceBundle
{
    /**
     * {@inheritdoc}
     */
    public static function getSupportedDrivers()
    {
        return array(
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getSecurityRoles()
    {
        return array(
            'ROLE_SYLIUS_ADMIN'         => array(
                'ROLE_SYLIUS_PAYMENT_ADMIN',
            ),
            'ROLE_SYLIUS_PAYMENT_ADMIN' => array(
                'ROLE_SYLIUS_PAYMENT_LIST',
                'ROLE_SYLIUS_PAYMENT_SHOW',
                'ROLE_SYLIUS_PAYMENT_CREATE',
                'ROLE_SYLIUS_PAYMENT_UPDATE',
                'ROLE_SYLIUS_PAYMENT_DELETE',
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelInterfaces()
    {
        return array(
            'Sylius\Component\Payment\Model\CreditCardInterface'    => 'sylius.model.credit_card.class',
            'Sylius\Component\Payment\Model\PaymentInterface'       => 'sylius.model.payment.class',
            'Sylius\Component\Payment\Model\PaymentMethodInterface' => 'sylius.model.payment_method.class',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Sylius\Component\Payment\Model';
    }
}
