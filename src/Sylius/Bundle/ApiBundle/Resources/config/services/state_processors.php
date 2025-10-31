<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_api.state_processor.admin.admin_user.remove', 'Sylius\Bundle\ApiBundle\StateProcessor\Admin\AdminUser\RemoveProcessor')
        ->args([
            service('api_platform.doctrine.orm.state.remove_processor'),
            service('security.token_storage'),
        ])
        ->tag('api_platform.state_processor');

    $services->set('sylius_api.state_processor.admin.admin_user.persist', 'Sylius\Bundle\ApiBundle\StateProcessor\Admin\AdminUser\PersistProcessor')
        ->args([
            service('api_platform.doctrine.orm.state.persist_processor'),
            service('sylius.security.password_updater'),
        ])
        ->tag('api_platform.state_processor');

    $services->set('sylius_api.state_processor.admin.country.persist', 'Sylius\Bundle\ApiBundle\StateProcessor\Admin\Country\PersistProcessor')
        ->args([
            service('api_platform.doctrine.orm.state.persist_processor'),
            service('sylius.checker.country_provinces_deletion'),
        ])
        ->tag('api_platform.state_processor');

    $services->set('sylius_api.state_processor.admin.channel.remove', 'Sylius\Bundle\ApiBundle\StateProcessor\Admin\Channel\RemoveProcessor')
        ->args([
            service('api_platform.doctrine.orm.state.remove_processor'),
            service('sylius.checker.channel_deletion'),
        ])
        ->tag('api_platform.state_processor');

    $services->set('sylius_api.state_processor.admin.locale.remove', 'Sylius\Bundle\ApiBundle\StateProcessor\Admin\Locale\RemoveProcessor')
        ->args([
            service('api_platform.doctrine.orm.state.remove_processor'),
            service('sylius.checker.locale_usage'),
        ])
        ->tag('api_platform.state_processor');

    $services->set('sylius_api.state_processor.admin.avatar_image.persist', 'Sylius\Bundle\ApiBundle\StateProcessor\Admin\AvatarImage\PersistProcessor')
        ->args([
            service('api_platform.doctrine.orm.state.persist_processor'),
            service('sylius_api.creator.avatar_image'),
            service('sylius.repository.avatar_image'),
        ])
        ->tag('api_platform.state_processor');

    $services->set('sylius_api.state_processor.admin.zone.remove', 'Sylius\Bundle\ApiBundle\StateProcessor\Admin\Zone\RemoveProcessor')
        ->args([
            service('api_platform.doctrine.orm.state.remove_processor'),
            service('sylius.checker.zone_deletion'),
        ])
        ->tag('api_platform.state_processor');

    $services->set('sylius_api.state_processor.shop.address.persist', 'Sylius\Bundle\ApiBundle\StateProcessor\Shop\Address\PersistProcessor')
        ->args([
            service('api_platform.doctrine.orm.state.persist_processor'),
            service('sylius_api.context.user.token_based'),
        ])
        ->tag('api_platform.state_processor');

    $services->set('sylius_api.state_processor.admin.product_image.persist', 'Sylius\Bundle\ApiBundle\StateProcessor\Admin\ProductImage\PersistProcessor')
        ->args([
            service('api_platform.doctrine.orm.state.persist_processor'),
            service('sylius_api.creator.product_image'),
            service('validator'),
        ])
        ->tag('api_platform.state_processor');

    $services->set('sylius_api.state_processor.admin.taxon_image.persist', 'Sylius\Bundle\ApiBundle\StateProcessor\Admin\TaxonImage\PersistProcessor')
        ->args([
            service('api_platform.doctrine.orm.state.persist_processor'),
            service('sylius_api.creator.taxon_image'),
        ])
        ->tag('api_platform.state_processor');

    $services->set('sylius_api.state_processor.common.messenger.persist', 'Sylius\Bundle\ApiBundle\StateProcessor\Common\MessengerPersistProcessor')
        ->decorate('api_platform.state_processor.write')
        ->args([service('.inner')])
        ->tag('api_platform.state_processor');

    $services->set('sylius_api.state_processor.admin.common.remove', 'Sylius\Bundle\ApiBundle\StateProcessor\Admin\Common\ResourceRemoveProcessor')
        ->decorate('api_platform.doctrine.orm.state.remove_processor')
        ->args([service('.inner')])
        ->tag('api_platform.state_processor');

    $services->set('sylius_api.state_processor.admin.promotion.promotion_coupon.persist', 'Sylius\Bundle\ApiBundle\StateProcessor\Admin\Promotion\PromotionCoupon\PersistProcessor')
        ->args([
            service('api_platform.doctrine.orm.state.persist_processor'),
            service('sylius_api.resolver.uri_template_parent_resource_resolver'),
            service('validator'),
        ])
        ->tag('api_platform.state_processor');

    $services->set('sylius_api.state_processor.admin.customer.persist', 'Sylius\Bundle\ApiBundle\StateProcessor\Admin\Customer\PersistProcessor')
        ->args([
            service('api_platform.doctrine.orm.state.persist_processor'),
            service('sylius.security.password_updater'),
        ])
        ->tag('api_platform.state_processor');
};
