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

use Sylius\Bundle\CoreBundle\Fixture\Listener\CatalogPromotionExecutorListener;
use Sylius\Bundle\CoreBundle\Fixture\Listener\ImagesPurgerListener;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius.fixture.listener.catalog_promotion_executor', CatalogPromotionExecutorListener::class)
        ->private()
        ->args([
            service('sylius.processor.catalog_promotion.all_product_variant'),
            service('sylius.repository.catalog_promotion'),
            service('sylius.command_bus'),
            tagged_iterator('sylius.catalog_promotion.criteria'),
        ])
        ->tag('sylius_fixtures.listener');

    $services->set('sylius.fixture.listener.images_purger', ImagesPurgerListener::class)
        ->private()
        ->args([
            service('filesystem'),
            '%sylius_core.images_dir%',
        ])
        ->tag('sylius_fixtures.listener');
};
