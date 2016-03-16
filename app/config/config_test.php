<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if ($container->hasParameter('paypal.express_checkout.username')) {
    $container->loadFromExtension(
        'payum',
        [
            'gateways' => [
                'paypal_express_checkout' => [
                    'paypal_express_checkout_nvp' => [
                        'username' => $container->getParameter('paypal.express_checkout.username'),
                        'password' => $container->getParameter('paypal.express_checkout.password'),
                        'signature' => $container->getParameter('paypal.express_checkout.signature'),
                        'sandbox' => (bool) $container->getParameter('paypal.express_checkout.sandbox'),
                    ],
                ],
                'offline' => [
                    'offline' => null,
                ],
            ],
        ]
    );
}
