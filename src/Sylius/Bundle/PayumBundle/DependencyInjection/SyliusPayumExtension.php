<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PayumBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SyliusPayumExtension extends AbstractResourceExtension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        list($config, $loader) = $this->configure($config, new Configuration(), $container, self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS);

        $loader->load('payment/generic.xml');

        if (class_exists('Payum\Be2Bill\PaymentFactory')) {
            $loader->load('payment/be2bill.xml');
        }

        if (class_exists('Payum\Paypal\ExpressCheckout\Nvp\PaymentFactory')) {
            $loader->load('payment/paypal_express_checkout_nvp.xml');
        }

        if (class_exists('Payum\Paypal\ExpressCheckout\Nvp\PaymentFactory')) {
            $loader->load('payment/paypal_express_checkout_nvp.xml');
        }

        if (class_exists('Payum\OmnipayBridge\PaymentFactory')) {
            $loader->load('payment/omnipay_bridge.xml');
        }
    }
}
