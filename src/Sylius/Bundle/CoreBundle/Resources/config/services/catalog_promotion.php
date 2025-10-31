<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $container->import('catalog_promotion/applicators.php');
    $container->import('catalog_promotion/calculators.php');
    $container->import('catalog_promotion/checkers.php');
    $container->import('catalog_promotion/command_handlers.php');
    $container->import('catalog_promotion/listeners.php');
    $container->import('catalog_promotion/processors.php');
    

    $services->set('sylius.discount_application_criteria.catalog_promotion.exclusive', 'Sylius\Bundle\CoreBundle\CatalogPromotion\DiscountApplicationCriteria\ExclusiveCriteria')
        ->tag('sylius.catalog_promotion.applicator_criteria');

    $services->set('sylius.discount_application_criteria.catalog_promotion.minimum_price', 'Sylius\Bundle\CoreBundle\CatalogPromotion\DiscountApplicationCriteria\MinimumPriceCriteria')
        ->tag('sylius.catalog_promotion.applicator_criteria');

    $services->set('sylius.announcer.catalog_promotion', 'Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionAnnouncer')
        ->public()
        ->args([
            service('sylius.event_bus'),
            service('sylius.calculator.delay_stamp'),
            service('clock'),
        ]);

    $services->alias('Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionAnnouncerInterface', 'sylius.announcer.catalog_promotion');

    $services->set('sylius.announcer.catalog_promotion.removal', 'Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionRemovalAnnouncer')
        ->public()
        ->args([service('sylius.command_bus')]);

    $services->alias('Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionRemovalAnnouncerInterface', 'sylius.announcer.catalog_promotion.removal');

    $services->set('sylius.command_dispatcher.catalog_promotion.batched_apply_on_variants', 'Sylius\Bundle\CoreBundle\CatalogPromotion\CommandDispatcher\BatchedApplyCatalogPromotionsOnVariantsCommandDispatcher')
        ->args([
            service('sylius.command_bus'),
            '%sylius_core.catalog_promotions.batch_size%',
        ]);

    $services->alias('Sylius\Bundle\CoreBundle\CatalogPromotion\CommandDispatcher\ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface', 'sylius.command_dispatcher.catalog_promotion.batched_apply_on_variants');
};
