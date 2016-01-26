<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$paypalAccountName = getenv('SECRET_BEHAT_PAYPAL_ACCOUNT_NAME');
$paypalAccountPassword = getenv('SECRET_BEHAT_PAYPAL_ACCOUNT_PASSWORD');

if (!$paypalAccountName || !$paypalAccountName) {
    $container->setParameter('sylius.behat.paypal_account_name', null);
    $container->setParameter('sylius.behat.paypal_account_password', null);
}

$container->setParameter('sylius.behat.paypal_account_name', $paypalAccountName);
$container->setParameter('sylius.behat.paypal_account_password', $paypalAccountPassword);
