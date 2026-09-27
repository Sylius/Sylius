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

use Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionAnnouncer;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionAnnouncerInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionRemovalAnnouncer;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionRemovalAnnouncerInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\CommandDispatcher\ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\CommandDispatcher\BatchedApplyCatalogPromotionsOnVariantsCommandDispatcher;
use Sylius\Bundle\CoreBundle\CatalogPromotion\DiscountApplicationCriteria\ExclusiveCriteria;
use Sylius\Bundle\CoreBundle\CatalogPromotion\DiscountApplicationCriteria\MinimumPriceCriteria;

return static function (ContainerConfigurator $container) {
    $container->import('catalog_promotion/applicators.php');
    $container->import('catalog_promotion/calculators.php');
    $container->import('catalog_promotion/checkers.php');
    $container->import('catalog_promotion/command_handlers.php');
    $container->import('catalog_promotion/listeners.php');
    $container->import('catalog_promotion/processors.php');

    $services = $container->services();

    $services
        ->set('sylius.discount_application_criteria.catalog_promotion.exclusive', ExclusiveCriteria::class)
        ->tag('sylius.catalog_promotion.applicator_criteria')
    ;

    $services
        ->set('sylius.discount_application_criteria.catalog_promotion.minimum_price', MinimumPriceCriteria::class)
        ->tag('sylius.catalog_promotion.applicator_criteria')
    ;

    $services
        ->set('sylius.announcer.catalog_promotion', CatalogPromotionAnnouncer::class)
        ->args([
            service('sylius.event_bus'),
            service('sylius.calculator.delay_stamp'),
            service('clock'),
        ])
        ->public()
    ;
    $services->alias(CatalogPromotionAnnouncerInterface::class, 'sylius.announcer.catalog_promotion');

    $services
        ->set('sylius.announcer.catalog_promotion.removal', CatalogPromotionRemovalAnnouncer::class)
        ->args([service('sylius.command_bus')])
        ->public()
    ;
    $services->alias(CatalogPromotionRemovalAnnouncerInterface::class, 'sylius.announcer.catalog_promotion.removal');

    $services
        ->set('sylius.command_dispatcher.catalog_promotion.batched_apply_on_variants', BatchedApplyCatalogPromotionsOnVariantsCommandDispatcher::class)
        ->args([
            service('sylius.command_bus'),
            '%sylius_core.catalog_promotions.batch_size%',
        ])
    ;
    $services->alias(ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface::class, 'sylius.command_dispatcher.catalog_promotion.batched_apply_on_variants');
};
