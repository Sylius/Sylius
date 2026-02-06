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

use Sylius\Bundle\CoreBundle\Mailer\ShipmentEmailManager;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.mailer.shipment_email_manager', ShipmentEmailManager::class)
        ->args([service('sylius.email_sender')]);
};
