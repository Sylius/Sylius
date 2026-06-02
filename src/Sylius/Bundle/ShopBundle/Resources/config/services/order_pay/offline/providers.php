<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\Bundle\CoreBundle\OrderPay\Provider\Offline\StatusHttpResponseProvider;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius_shop.provider.order_pay.http_response.offline.status', StatusHttpResponseProvider::class)
        ->args([service('sylius_shop.provider.order_pay.final_url')])
        ->tag('sylius.provider.payment_request.http_response.offline', ['action' => 'status'])
    ;
};
