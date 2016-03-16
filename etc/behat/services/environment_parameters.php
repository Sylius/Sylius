<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

convertEnvironmentVariableToBehatParameter($container, 'PAYPAL__EXPRESS_CHECKOUT__BUYER__USERNAME');
convertEnvironmentVariableToBehatParameter($container, 'PAYPAL__EXPRESS_CHECKOUT__BUYER__PASSWORD');

function convertEnvironmentVariableToBehatParameter(\Symfony\Component\DependencyInjection\ContainerInterface $container, $key, $override = false)
{
    if (!$override && $container->hasParameter($key)) {
        return;
    }

    $parameter = getenv($key);

    if ($parameter) {
        $container->setParameter(parseEnvironmentVariableKey($key), $parameter);

        return;
    }

    $container->setParameter(parseEnvironmentVariableKey($key), 'EXPORT_PROPER_ENV');
}

function parseEnvironmentVariableKey($key)
{
    return strtolower(str_replace('__', '.', $key));
}
