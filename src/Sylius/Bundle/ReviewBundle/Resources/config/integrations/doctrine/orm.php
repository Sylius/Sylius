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

use Sylius\Bundle\ReviewBundle\Doctrine\ORM\Subscriber\LoadMetadataSubscriber;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius.doctrine.orm.event_subscriber.load_metadata.review', LoadMetadataSubscriber::class)
        ->args(['%sylius.review.subjects%'])
        ->tag('doctrine.event_subscriber');
};
