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

use Sylius\Bundle\CoreBundle\Mailer\ContactEmailManager;
use Sylius\Bundle\CoreBundle\Mailer\OrderEmailManager;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius_shop.mailer.contact_email_manager', ContactEmailManager::class)
        ->args([service('sylius.email_sender')])
    ;

    $services
        ->set('sylius_shop.mailer.order_email_manager', OrderEmailManager::class)
        ->args([service('sylius.email_sender')])
    ;
};
