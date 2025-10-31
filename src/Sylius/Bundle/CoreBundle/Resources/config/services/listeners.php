<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $container->import('listeners/workflow/*.php');
    

    $services->defaults()
        ->public();

    $services->set('sylius.listener.channel_deletion', 'Sylius\Bundle\CoreBundle\EventListener\ChannelDeletionListener')
        ->args([service('sylius.checker.channel_deletion')])
        ->tag('kernel.event_listener', ['event' => 'sylius.channel.pre_delete', 'method' => 'onChannelPreDelete']);

    $services->set('sylius.listener.images_upload', 'Sylius\Bundle\CoreBundle\EventListener\ImagesUploadListener')
        ->args([service('sylius.uploader.image')])
        ->tag('kernel.event_listener', ['event' => 'sylius.product.pre_create', 'method' => 'uploadImages'])
        ->tag('kernel.event_listener', ['event' => 'sylius.product.pre_update', 'method' => 'uploadImages'])
        ->tag('kernel.event_listener', ['event' => 'sylius.taxon.pre_create', 'method' => 'uploadImages'])
        ->tag('kernel.event_listener', ['event' => 'sylius.taxon.pre_update', 'method' => 'uploadImages']);

    $services->set('sylius.listener.images_remove', 'Sylius\Bundle\CoreBundle\EventListener\ImagesRemoveListener')
        ->args([
            service('sylius.uploader.image'),
            service('liip_imagine.cache.manager'),
            service('liip_imagine.filter.manager'),
        ])
        ->tag('doctrine.event_listener', ['event' => 'onFlush', 'lazy' => true])
        ->tag('doctrine.event_listener', ['event' => 'postFlush', 'lazy' => true]);

    $services->set('sylius.listener.avatar_upload', 'Sylius\Bundle\CoreBundle\EventListener\ImageUploadListener')
        ->args([service('sylius.uploader.image')])
        ->tag('kernel.event_listener', ['event' => 'sylius.admin_user.pre_create', 'method' => 'uploadImage'])
        ->tag('kernel.event_listener', ['event' => 'sylius.admin_user.pre_update', 'method' => 'uploadImage']);

    $services->set('sylius.listener.order_recalculation', 'Sylius\Bundle\CoreBundle\EventListener\OrderRecalculationListener')
        ->args([service('sylius.order_processing.order_processor')])
        ->tag('kernel.event_listener', ['event' => 'sylius.cart_change', 'method' => 'recalculateOrder']);

    $services->set('sylius.listener.default_username_orm', 'Sylius\Bundle\CoreBundle\EventListener\DefaultUsernameORMListener')
        ->tag('doctrine.event_listener', ['event' => 'onFlush', 'lazy' => true]);

    $services->set('sylius.listener.canonicalizer', 'Sylius\Bundle\CoreBundle\EventListener\CanonicalizerListener')
        ->args([service('sylius.canonicalizer')])
        ->tag('doctrine.event_listener', ['event' => 'prePersist', 'lazy' => true])
        ->tag('doctrine.event_listener', ['event' => 'preUpdate', 'lazy' => true]);

    $services->set('sylius.listener.password_updater', 'Sylius\Bundle\CoreBundle\EventListener\PasswordUpdaterListener')
        ->args([service('sylius.security.password_updater')])
        ->tag('kernel.event_listener', ['event' => 'sylius.user.pre_password_reset', 'method' => 'genericEventUpdater'])
        ->tag('kernel.event_listener', ['event' => 'sylius.user.pre_password_change', 'method' => 'genericEventUpdater'])
        ->tag('kernel.event_listener', ['event' => 'sylius.admin_user.pre_update', 'method' => 'genericEventUpdater'])
        ->tag('kernel.event_listener', ['event' => 'sylius.customer.pre_update', 'method' => 'customerUpdateEvent'])
        ->tag('doctrine.event_listener', ['event' => 'prePersist', 'lazy' => true])
        ->tag('doctrine.event_listener', ['event' => 'preUpdate', 'lazy' => true]);

    $services->set('sylius.listener.review_create', 'Sylius\Bundle\CoreBundle\EventListener\ReviewCreateListener')
        ->args([service('sylius.context.customer')])
        ->tag('kernel.event_listener', ['event' => 'sylius.product_review.pre_create', 'method' => 'ensureReviewHasAuthor']);

    $services->set('sylius.listener.locking', 'Sylius\Bundle\CoreBundle\EventListener\LockingListener')
        ->args([service('doctrine.orm.entity_manager')])
        ->tag('kernel.event_listener', ['event' => 'sylius.product_variant.initialize_update', 'method' => 'lock']);

    $services->set('sylius.listener.simple_product_locking', 'Sylius\Bundle\CoreBundle\EventListener\SimpleProductLockingListener')
        ->args([service('doctrine.orm.entity_manager')])
        ->tag('kernel.event_listener', ['event' => 'sylius.product.initialize_update', 'method' => 'lock']);

    $services->set('sylius.listener.customer_default_address', 'Sylius\Bundle\CoreBundle\EventListener\CustomerDefaultAddressListener')
        ->tag('kernel.event_listener', ['event' => 'sylius.address.pre_create', 'method' => 'preCreate']);

    $services->set('sylius.listener.locale_aware', 'Sylius\Bundle\CoreBundle\EventListener\LocaleAwareListener')
        ->decorate('locale_aware_listener')
        ->args([service('.inner')]);

    $services->set('sylius.event_subscriber.x_frame_options', 'Sylius\Bundle\CoreBundle\EventListener\XFrameOptionsSubscriber')
        ->tag('kernel.event_subscriber');

    $services->set('sylius.listener.taxon_deletion', 'Sylius\Bundle\CoreBundle\EventListener\TaxonDeletionListener')
        ->args([
            service('request_stack'),
            service('sylius.repository.channel'),
            service('sylius.checker.promotion.taxon_in_promotion_rule'),
            service('sylius.updater.promotion_rule.has_taxon'),
        ])
        ->tag('kernel.event_listener', ['event' => 'sylius.taxon.pre_delete', 'method' => 'protectFromRemovingMenuTaxon'])
        ->tag('kernel.event_listener', ['event' => 'sylius.taxon.post_delete', 'method' => 'removeTaxonFromPromotionRules'])
        ->tag('kernel.event_listener', ['event' => 'sylius.taxon.pre_delete', 'method' => 'handleRemovingRootTaxonAtPositionZero'])
        ->tag('kernel.event_listener', ['event' => 'sylius.taxon.pre_delete', 'method' => 'protectFromRemovingTaxonInUseByPromotionRule']);

    $services->set('sylius.listener.payment_pre_complete', 'Sylius\Bundle\CoreBundle\EventListener\PaymentPreCompleteListener')
        ->args([service('sylius.checker.inventory.order_item_availability')])
        ->tag('kernel.event_listener', ['event' => 'sylius.payment.pre_complete', 'method' => 'checkStockAvailability']);

    $services->set('sylius.listener.product_deletion', 'Sylius\Bundle\CoreBundle\EventListener\ProductDeletionListener')
        ->args([service('sylius.checker.promotion.product_in_promotion_rule')])
        ->tag('kernel.event_listener', ['event' => 'sylius.product.pre_delete', 'method' => 'protectFromRemovingProductInUseByPromotionRule']);

    $services->set('sylius.listener.product_option_value', 'Sylius\Bundle\CoreBundle\EventListener\ProductOptionValueDeletionListener')
        ->args([service('sylius.repository.product_variant')])
        ->tag('doctrine.orm.entity_listener', ['event' => 'preRemove', 'entity' => '%sylius.model.product_option_value.class%']);

    $services->set('sylius.listener.postgre_sql_default_schema', 'Sylius\Bundle\CoreBundle\EventListener\PostgreSQLDefaultSchemaListener')
        ->tag('doctrine.event_listener', ['event' => 'postGenerateSchema', 'method' => 'postGenerateSchema', 'lazy' => true]);

    $services->set('sylius.listener.migration_skip', 'Sylius\Bundle\CoreBundle\EventListener\MigrationSkipListener')
        ->args([service('doctrine.migrations.dependency_factory')])
        ->tag('doctrine.event_listener', ['event' => 'onMigrationsVersionSkipped', 'method' => 'onMigrationsVersionSkipped', 'lazy' => true]);

    $services->set('sylius.listener.user_impersonator', 'Sylius\Bundle\CoreBundle\EventListener\UserImpersonatorSubscriber')
        ->args([service('security.firewall.map')])
        ->tag('kernel.event_subscriber');
};
