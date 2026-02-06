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

use Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler\ApplyCatalogPromotionsOnVariantsHandler;
use Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler\DisableCatalogPromotionHandler;
use Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler\RemoveCatalogPromotionHandler;
use Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler\UpdateCatalogPromotionStateHandler;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.command_handler.catalog_promotion.apply_variants', ApplyCatalogPromotionsOnVariantsHandler::class)
        ->args([
            service('sylius.provider.eligible_catalog_promotions'),
            service('sylius.applicator.catalog_promotion'),
            service('sylius.repository.product_variant'),
            service('sylius.processor.catalog_promotion.clearer'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius.command_handler.catalog_promotion.disable', DisableCatalogPromotionHandler::class)
        ->args([
            service('sylius.repository.catalog_promotion'),
            service('sylius.processor.catalog_promotion.all_product_variant'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius.command_handler.catalog_promotion.remove', RemoveCatalogPromotionHandler::class)
        ->args([
            service('sylius.repository.catalog_promotion'),
            service('sylius.manager.catalog_promotion'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius.command_handler.catalog_promotion.update_state', UpdateCatalogPromotionStateHandler::class)
        ->args([
            service('sylius.processor.catalog_promotion.state'),
            service('sylius.repository.catalog_promotion'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);
};
