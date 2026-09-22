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

use Sylius\Bundle\CoreBundle\EventListener\CanonicalizerListener;
use Sylius\Bundle\CoreBundle\EventListener\ChannelDeletionListener;
use Sylius\Bundle\CoreBundle\EventListener\CustomerDefaultAddressListener;
use Sylius\Bundle\CoreBundle\EventListener\DefaultUsernameORMListener;
use Sylius\Bundle\CoreBundle\EventListener\ImagesRemoveListener;
use Sylius\Bundle\CoreBundle\EventListener\ImagesUploadListener;
use Sylius\Bundle\CoreBundle\EventListener\ImageUploadListener;
use Sylius\Bundle\CoreBundle\EventListener\LocaleAwareListener;
use Sylius\Bundle\CoreBundle\EventListener\LockingListener;
use Sylius\Bundle\CoreBundle\EventListener\MigrationSkipListener;
use Sylius\Bundle\CoreBundle\EventListener\OrderRecalculationListener;
use Sylius\Bundle\CoreBundle\EventListener\PasswordUpdaterListener;
use Sylius\Bundle\CoreBundle\EventListener\PaymentPreCompleteListener;
use Sylius\Bundle\CoreBundle\EventListener\PostgreSQLDefaultSchemaListener;
use Sylius\Bundle\CoreBundle\EventListener\ProductDeletionListener;
use Sylius\Bundle\CoreBundle\EventListener\ProductOptionValueDeletionListener;
use Sylius\Bundle\CoreBundle\EventListener\ReviewCreateListener;
use Sylius\Bundle\CoreBundle\EventListener\SimpleProductLockingListener;
use Sylius\Bundle\CoreBundle\EventListener\TaxonDeletionListener;
use Sylius\Bundle\CoreBundle\EventListener\UserImpersonatorSubscriber;
use Sylius\Bundle\CoreBundle\EventListener\XFrameOptionsSubscriber;
use Sylius\Bundle\CoreBundle\Telemetry\EventListener\TelemetryIndexSchemaListener;

return static function (ContainerConfigurator $container) {
    $container->import('listeners/workflow/*.php');

    $services = $container->services();

    $services->defaults()->public();

    $services
        ->set('sylius.listener.channel_deletion', ChannelDeletionListener::class)
        ->args([
            service('sylius.checker.channel_deletion'),
            service('sylius.updater.shipping_method'),
        ])
        ->tag('kernel.event_listener', ['event' => 'sylius.channel.pre_delete', 'method' => 'onChannelPreDelete'])
        ->tag('kernel.event_listener', ['event' => 'sylius.channel.post_delete', 'method' => 'removeChannelConfigurationFromShippingMethods'])
    ;

    $services
        ->set('sylius.listener.images_upload', ImagesUploadListener::class)
        ->args([service('sylius.uploader.image')])
        ->tag('kernel.event_listener', ['event' => 'sylius.product.pre_create', 'method' => 'uploadImages'])
        ->tag('kernel.event_listener', ['event' => 'sylius.product.pre_update', 'method' => 'uploadImages'])
        ->tag('kernel.event_listener', ['event' => 'sylius.taxon.pre_create', 'method' => 'uploadImages'])
        ->tag('kernel.event_listener', ['event' => 'sylius.taxon.pre_update', 'method' => 'uploadImages'])
    ;

    $services
        ->set('sylius.listener.images_remove', ImagesRemoveListener::class)
        ->args([
            service('sylius.uploader.image'),
            service('liip_imagine.cache.manager'),
            service('liip_imagine.filter.manager'),
        ])
        ->tag('doctrine.event_listener', ['event' => 'onFlush', 'lazy' => true])
        ->tag('doctrine.event_listener', ['event' => 'postFlush', 'lazy' => true])
    ;

    $services
        ->set('sylius.listener.avatar_upload', ImageUploadListener::class)
        ->args([service('sylius.uploader.image')])
        ->tag('kernel.event_listener', ['event' => 'sylius.admin_user.pre_create', 'method' => 'uploadImage'])
        ->tag('kernel.event_listener', ['event' => 'sylius.admin_user.pre_update', 'method' => 'uploadImage'])
    ;

    $services
        ->set('sylius.listener.order_recalculation', OrderRecalculationListener::class)
        ->args([service('sylius.order_processing.order_processor')])
        ->tag('kernel.event_listener', ['event' => 'sylius.cart_change', 'method' => 'recalculateOrder'])
    ;

    $services
        ->set('sylius.listener.default_username_orm', DefaultUsernameORMListener::class)
        ->tag('doctrine.event_listener', ['event' => 'onFlush', 'lazy' => true])
    ;

    $services
        ->set('sylius.listener.canonicalizer', CanonicalizerListener::class)
        ->args([service('sylius.canonicalizer')])
        ->tag('doctrine.event_listener', ['event' => 'prePersist', 'lazy' => true])
        ->tag('doctrine.event_listener', ['event' => 'preUpdate', 'lazy' => true])
    ;

    $services
        ->set('sylius.listener.password_updater', PasswordUpdaterListener::class)
        ->args([service('sylius.security.password_updater')])
        ->tag('kernel.event_listener', ['event' => 'sylius.user.pre_password_reset', 'method' => 'genericEventUpdater'])
        ->tag('kernel.event_listener', ['event' => 'sylius.user.pre_password_change', 'method' => 'genericEventUpdater'])
        ->tag('kernel.event_listener', ['event' => 'sylius.admin_user.pre_update', 'method' => 'genericEventUpdater'])
        ->tag('kernel.event_listener', ['event' => 'sylius.customer.pre_update', 'method' => 'customerUpdateEvent'])
        ->tag('doctrine.event_listener', ['event' => 'prePersist', 'lazy' => true])
        ->tag('doctrine.event_listener', ['event' => 'preUpdate', 'lazy' => true])
    ;

    $services
        ->set('sylius.listener.review_create', ReviewCreateListener::class)
        ->args([service('sylius.context.customer')])
        ->tag('kernel.event_listener', ['event' => 'sylius.product_review.pre_create', 'method' => 'ensureReviewHasAuthor'])
    ;

    $services
        ->set('sylius.listener.locking', LockingListener::class)
        ->args([service('doctrine.orm.entity_manager')])
        ->tag('kernel.event_listener', ['event' => 'sylius.product_variant.initialize_update', 'method' => 'lock'])
    ;

    $services
        ->set('sylius.listener.simple_product_locking', SimpleProductLockingListener::class)
        ->args([service('doctrine.orm.entity_manager')])
        ->tag('kernel.event_listener', ['event' => 'sylius.product.initialize_update', 'method' => 'lock'])
    ;

    $services
        ->set('sylius.listener.customer_default_address', CustomerDefaultAddressListener::class)
        ->tag('kernel.event_listener', ['event' => 'sylius.address.pre_create', 'method' => 'preCreate'])
    ;

    $services
        ->set('sylius.listener.locale_aware', LocaleAwareListener::class)
        ->decorate('locale_aware_listener')
        ->args([service('.inner')])
    ;

    $services
        ->set('sylius.event_subscriber.x_frame_options', XFrameOptionsSubscriber::class)
        ->tag('kernel.event_subscriber')
    ;

    $services
        ->set('sylius.listener.taxon_deletion', TaxonDeletionListener::class)
        ->args([
            service('request_stack'),
            service('sylius.repository.channel'),
            service('sylius.checker.promotion.taxon_in_promotion_rule'),
            service('sylius.updater.promotion_rule.has_taxon'),
        ])
        ->tag('kernel.event_listener', ['event' => 'sylius.taxon.pre_delete', 'method' => 'protectFromRemovingMenuTaxon'])
        ->tag('kernel.event_listener', ['event' => 'sylius.taxon.post_delete', 'method' => 'removeTaxonFromPromotionRules'])
        ->tag('kernel.event_listener', ['event' => 'sylius.taxon.pre_delete', 'method' => 'handleRemovingRootTaxonAtPositionZero'])
        ->tag('kernel.event_listener', ['event' => 'sylius.taxon.pre_delete', 'method' => 'protectFromRemovingTaxonInUseByPromotionRule'])
    ;

    $services
        ->set('sylius.listener.payment_pre_complete', PaymentPreCompleteListener::class)
        ->args([service('sylius.checker.inventory.order_item_availability')])
        ->tag('kernel.event_listener', ['event' => 'sylius.payment.pre_complete', 'method' => 'checkStockAvailability'])
    ;

    $services
        ->set('sylius.listener.product_deletion', ProductDeletionListener::class)
        ->args([service('sylius.checker.promotion.product_in_promotion_rule')])
        ->tag('kernel.event_listener', ['event' => 'sylius.product.pre_delete', 'method' => 'protectFromRemovingProductInUseByPromotionRule'])
    ;

    $services
        ->set('sylius.listener.product_option_value', ProductOptionValueDeletionListener::class)
        ->args([service('sylius.repository.product_variant')])
        ->tag('doctrine.orm.entity_listener', ['event' => 'preRemove', 'entity' => '%sylius.model.product_option_value.class%'])
    ;

    $services
        ->set('sylius.listener.postgre_sql_default_schema', PostgreSQLDefaultSchemaListener::class)
        ->tag('doctrine.event_listener', ['event' => 'postGenerateSchema', 'method' => 'postGenerateSchema', 'lazy' => true])
    ;

    $services
        ->set('sylius.listener.migration_skip', MigrationSkipListener::class)
        ->args([service('doctrine.migrations.dependency_factory')])
        ->tag('doctrine.event_listener', ['event' => 'onMigrationsVersionSkipped', 'method' => 'onMigrationsVersionSkipped', 'lazy' => true])
    ;

    $services
        ->set('sylius.listener.user_impersonator', UserImpersonatorSubscriber::class)
        ->args([service('security.firewall.map')])
        ->tag('kernel.event_subscriber')
    ;

    $services
        ->set('sylius.listener.telemetry_index_schema', TelemetryIndexSchemaListener::class)
        ->args([service('doctrine.dbal.default_connection')])
        ->tag('doctrine.event_listener', ['event' => 'postGenerateSchema'])
    ;
};
