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

use Sylius\Bundle\AdminBundle\Form\Type\AdminUserType;
use Sylius\Bundle\AdminBundle\Form\Type\CatalogPromotionType;
use Sylius\Bundle\AdminBundle\Form\Type\ChannelType;
use Sylius\Bundle\AdminBundle\Form\Type\CountryType;
use Sylius\Bundle\AdminBundle\Form\Type\CurrencyType;
use Sylius\Bundle\AdminBundle\Form\Type\CustomerGroupType;
use Sylius\Bundle\AdminBundle\Form\Type\CustomerType;
use Sylius\Bundle\AdminBundle\Form\Type\ExchangeRateType;
use Sylius\Bundle\AdminBundle\Form\Type\LocaleType;
use Sylius\Bundle\AdminBundle\Form\Type\PaymentMethodType;
use Sylius\Bundle\AdminBundle\Form\Type\ProductAssociationTypeType;
use Sylius\Bundle\AdminBundle\Form\Type\ProductGenerateVariantsType;
use Sylius\Bundle\AdminBundle\Form\Type\ProductReviewType;
use Sylius\Bundle\AdminBundle\Form\Type\PromotionType;
use Sylius\Bundle\AdminBundle\Form\Type\ShippingCategoryType;
use Sylius\Bundle\AdminBundle\Form\Type\ShippingMethodType;
use Sylius\Bundle\AdminBundle\Form\Type\TaxCategoryType;
use Sylius\Bundle\AdminBundle\Form\Type\TaxRateType;
use Sylius\Bundle\AdminBundle\Twig\Component\Shared\Navbar\NotificationsComponent;
use Sylius\Bundle\AdminBundle\Twig\Component\Shared\Navbar\ShopPreviewComponent;
use Sylius\Bundle\AdminBundle\Twig\Component\Shared\Navbar\UserDropdownComponent;
use Sylius\Bundle\AdminBundle\Twig\Component\Shared\RenderEntityWithTemplateComponent;
use Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponent;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.twig.component.admin_user.form', ResourceFormComponent::class)
        ->args([
            service('sylius.repository.admin_user'),
            service('form.factory'),
            '%sylius.model.admin_user.class%',
            AdminUserType::class,
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:admin_user:form']);

    $services->set('sylius_admin.twig.component.catalog_promotion.form', ResourceFormComponent::class)
        ->args([
            service('sylius.repository.catalog_promotion'),
            service('form.factory'),
            '%sylius.model.catalog_promotion.class%',
            CatalogPromotionType::class,
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:catalog_promotion:form']);

    $services->set('sylius_admin.twig.component.channel.form', ResourceFormComponent::class)
        ->args([
            service('sylius.repository.channel'),
            service('form.factory'),
            '%sylius.model.channel.class%',
            ChannelType::class,
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:channel:form']);

    $services->set('sylius_admin.twig.component.country.form', ResourceFormComponent::class)
        ->args([
            service('sylius.repository.country'),
            service('form.factory'),
            '%sylius.model.country.class%',
            CountryType::class,
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:country:form']);

    $services->set('sylius_admin.twig.component.currency.form', ResourceFormComponent::class)
        ->args([
            service('sylius.repository.currency'),
            service('form.factory'),
            '%sylius.model.currency.class%',
            CurrencyType::class,
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:currency:form']);

    $services->set('sylius_admin.twig.component.customer.form', ResourceFormComponent::class)
        ->args([
            service('sylius.repository.customer'),
            service('form.factory'),
            '%sylius.model.customer.class%',
            CustomerType::class,
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:customer:form']);

    $services->set('sylius_admin.twig.component.customer_group.form', ResourceFormComponent::class)
        ->args([
            service('sylius.repository.customer_group'),
            service('form.factory'),
            '%sylius.model.customer_group.class%',
            CustomerGroupType::class,
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:customer_group:form']);

    $services->set('sylius_admin.twig.component.exchange_rate.form', ResourceFormComponent::class)
        ->args([
            service('sylius.repository.exchange_rate'),
            service('form.factory'),
            '%sylius.model.exchange_rate.class%',
            ExchangeRateType::class,
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:exchange_rate:form']);

    $services->set('sylius_admin.twig.component.locale.form', ResourceFormComponent::class)
        ->args([
            service('sylius.repository.locale'),
            service('form.factory'),
            '%sylius.model.locale.class%',
            LocaleType::class,
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:locale:form']);

    $services->set('sylius_admin.twig.component.payment_method.form', ResourceFormComponent::class)
        ->args([
            service('sylius.repository.payment_method'),
            service('form.factory'),
            '%sylius.model.payment_method.class%',
            PaymentMethodType::class,
        ])
        ->tag('sylius.twig_component', ['key' => 'sylius_admin:payment_method:form']);

    $services->set('sylius_admin.twig.component.product.generate_product_variants_form', ResourceFormComponent::class)
        ->args([
            service('sylius.repository.product'),
            service('form.factory'),
            '%sylius.model.product.class%',
            ProductGenerateVariantsType::class,
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:product:generate_product_variants_form']);

    $services->set('sylius_admin.twig.component.product_association_type.form', ResourceFormComponent::class)
        ->args([
            service('sylius.repository.product_association_type'),
            service('form.factory'),
            '%sylius.model.product_association_type.class%',
            ProductAssociationTypeType::class,
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:product_association_type:form']);

    $services->set('sylius_admin.twig.component.product_review.form', ResourceFormComponent::class)
        ->args([
            service('sylius.repository.product_review'),
            service('form.factory'),
            '%sylius.model.product_review.class%',
            ProductReviewType::class,
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:product_review:form']);

    $services->set('sylius_admin.twig.component.promotion.form', ResourceFormComponent::class)
        ->args([
            service('sylius.repository.promotion'),
            service('form.factory'),
            '%sylius.model.promotion.class%',
            PromotionType::class,
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:promotion:form']);

    $services->set('sylius_admin.twig.component.shipping_category.form', ResourceFormComponent::class)
        ->args([
            service('sylius.repository.shipping_category'),
            service('form.factory'),
            '%sylius.model.shipping_category.class%',
            ShippingCategoryType::class,
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:shipping_category:form']);

    $services->set('sylius_admin.twig.component.shipping_method.form', ResourceFormComponent::class)
        ->args([
            service('sylius.repository.shipping_method'),
            service('form.factory'),
            '%sylius.model.shipping_method.class%',
            ShippingMethodType::class,
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:shipping_method:form']);

    $services->set('sylius_admin.twig.component.tax_category.form', ResourceFormComponent::class)
        ->args([
            service('sylius.repository.tax_category'),
            service('form.factory'),
            '%sylius.model.tax_category.class%',
            TaxCategoryType::class,
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:tax_category:form']);

    $services->set('sylius_admin.twig.component.tax_rate.form', ResourceFormComponent::class)
        ->args([
            service('sylius.repository.tax_rate'),
            service('form.factory'),
            '%sylius.model.tax_rate.class%',
            TaxRateType::class,
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:tax_rate:form']);

    $services->set('sylius_admin.twig.component.render_entity_with_template', RenderEntityWithTemplateComponent::class)
        ->args([service('doctrine.orm.entity_manager')])
        ->tag('sylius.twig_component', ['key' => 'sylius_admin:render_entity_with_template']);

    $services->set('sylius_admin.twig.component.shared.navbar.notifications', NotificationsComponent::class)
        ->args([
            service('sylius_admin.provider.notification'),
            '%sylius.admin.notification.enabled%',
        ])
        ->tag('sylius.twig_component', ['key' => 'sylius_admin:navbar:notifications']);

    $services->set('sylius_admin.twig.component.shared.navbar.shop_preview', ShopPreviewComponent::class)
        ->args([service('sylius.repository.channel')])
        ->tag('sylius.twig_component', ['key' => 'sylius_admin:navbar:shop_preview']);

    $services->set('sylius_admin.twig.component.shared.navbar.user_dropdown', UserDropdownComponent::class)
        ->args([
            service('router'),
            service('sylius_admin.provider.logged_in_admin_user'),
        ])
        ->tag('sylius.twig_component', ['key' => 'sylius_admin:navbar:user_dropdown']);
};
