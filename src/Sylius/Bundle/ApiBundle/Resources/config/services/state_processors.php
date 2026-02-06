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

use Sylius\Bundle\ApiBundle\StateProcessor\Admin\AdminUser\PersistProcessor as AdminUserPersistProcessor;
use Sylius\Bundle\ApiBundle\StateProcessor\Admin\AdminUser\RemoveProcessor as AdminUserRemoveProcessor;
use Sylius\Bundle\ApiBundle\StateProcessor\Admin\AvatarImage\PersistProcessor as AvatarImagePersistProcessor;
use Sylius\Bundle\ApiBundle\StateProcessor\Admin\Channel\RemoveProcessor as ChannelRemoveProcessor;
use Sylius\Bundle\ApiBundle\StateProcessor\Admin\Common\ResourceRemoveProcessor;
use Sylius\Bundle\ApiBundle\StateProcessor\Admin\Country\PersistProcessor as CountryPersistProcessor;
use Sylius\Bundle\ApiBundle\StateProcessor\Admin\Customer\PersistProcessor as CustomerPersistProcessor;
use Sylius\Bundle\ApiBundle\StateProcessor\Admin\Locale\RemoveProcessor as LocaleRemoveProcessor;
use Sylius\Bundle\ApiBundle\StateProcessor\Admin\ProductImage\PersistProcessor as ProductImagePersistProcessor;
use Sylius\Bundle\ApiBundle\StateProcessor\Admin\Promotion\PromotionCoupon\PersistProcessor as PromotionCouponPersistProcessor;
use Sylius\Bundle\ApiBundle\StateProcessor\Admin\TaxonImage\PersistProcessor as TaxonImagePersistProcessor;
use Sylius\Bundle\ApiBundle\StateProcessor\Admin\Zone\RemoveProcessor as ZoneRemoveProcessor;
use Sylius\Bundle\ApiBundle\StateProcessor\Common\MessengerPersistProcessor;
use Sylius\Bundle\ApiBundle\StateProcessor\Shop\Address\PersistProcessor as AddressPersistProcessor;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_api.state_processor.admin.admin_user.remove', AdminUserRemoveProcessor::class)
        ->args([
            service('api_platform.doctrine.orm.state.remove_processor'),
            service('security.token_storage'),
        ])
        ->tag('api_platform.state_processor');

    $services->set('sylius_api.state_processor.admin.admin_user.persist', AdminUserPersistProcessor::class)
        ->args([
            service('api_platform.doctrine.orm.state.persist_processor'),
            service('sylius.security.password_updater'),
        ])
        ->tag('api_platform.state_processor');

    $services->set('sylius_api.state_processor.admin.country.persist', CountryPersistProcessor::class)
        ->args([
            service('api_platform.doctrine.orm.state.persist_processor'),
            service('sylius.checker.country_provinces_deletion'),
        ])
        ->tag('api_platform.state_processor');

    $services->set('sylius_api.state_processor.admin.channel.remove', ChannelRemoveProcessor::class)
        ->args([
            service('api_platform.doctrine.orm.state.remove_processor'),
            service('sylius.checker.channel_deletion'),
        ])
        ->tag('api_platform.state_processor');

    $services->set('sylius_api.state_processor.admin.locale.remove', LocaleRemoveProcessor::class)
        ->args([
            service('api_platform.doctrine.orm.state.remove_processor'),
            service('sylius.checker.locale_usage'),
        ])
        ->tag('api_platform.state_processor');

    $services->set('sylius_api.state_processor.admin.avatar_image.persist', AvatarImagePersistProcessor::class)
        ->args([
            service('api_platform.doctrine.orm.state.persist_processor'),
            service('sylius_api.creator.avatar_image'),
            service('sylius.repository.avatar_image'),
        ])
        ->tag('api_platform.state_processor');

    $services->set('sylius_api.state_processor.admin.zone.remove', ZoneRemoveProcessor::class)
        ->args([
            service('api_platform.doctrine.orm.state.remove_processor'),
            service('sylius.checker.zone_deletion'),
        ])
        ->tag('api_platform.state_processor');

    $services->set('sylius_api.state_processor.shop.address.persist', AddressPersistProcessor::class)
        ->args([
            service('api_platform.doctrine.orm.state.persist_processor'),
            service('sylius_api.context.user.token_based'),
        ])
        ->tag('api_platform.state_processor');

    $services->set('sylius_api.state_processor.admin.product_image.persist', ProductImagePersistProcessor::class)
        ->args([
            service('api_platform.doctrine.orm.state.persist_processor'),
            service('sylius_api.creator.product_image'),
            service('validator'),
        ])
        ->tag('api_platform.state_processor');

    $services->set('sylius_api.state_processor.admin.taxon_image.persist', TaxonImagePersistProcessor::class)
        ->args([
            service('api_platform.doctrine.orm.state.persist_processor'),
            service('sylius_api.creator.taxon_image'),
        ])
        ->tag('api_platform.state_processor');

    $services->set('sylius_api.state_processor.common.messenger.persist', MessengerPersistProcessor::class)
        ->decorate('api_platform.state_processor.write')
        ->args([service('.inner')])
        ->tag('api_platform.state_processor');

    $services->set('sylius_api.state_processor.admin.common.remove', ResourceRemoveProcessor::class)
        ->decorate('api_platform.doctrine.orm.state.remove_processor')
        ->args([service('.inner')])
        ->tag('api_platform.state_processor');

    $services->set('sylius_api.state_processor.admin.promotion.promotion_coupon.persist', PromotionCouponPersistProcessor::class)
        ->args([
            service('api_platform.doctrine.orm.state.persist_processor'),
            service('sylius_api.resolver.uri_template_parent_resource_resolver'),
            service('validator'),
        ])
        ->tag('api_platform.state_processor');

    $services->set('sylius_api.state_processor.admin.customer.persist', CustomerPersistProcessor::class)
        ->args([
            service('api_platform.doctrine.orm.state.persist_processor'),
            service('sylius.security.password_updater'),
        ])
        ->tag('api_platform.state_processor');
};
