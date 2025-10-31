<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.twig.component.admin_user.form', 'Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponent')
        ->args([
            service('sylius.repository.admin_user'),
            service('form.factory'),
            '%sylius.model.admin_user.class%',
            'Sylius\Bundle\AdminBundle\Form\Type\AdminUserType',
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:admin_user:form']);

    $services->set('sylius_admin.twig.component.catalog_promotion.form', 'Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponent')
        ->args([
            service('sylius.repository.catalog_promotion'),
            service('form.factory'),
            '%sylius.model.catalog_promotion.class%',
            'Sylius\Bundle\AdminBundle\Form\Type\CatalogPromotionType',
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:catalog_promotion:form']);

    $services->set('sylius_admin.twig.component.channel.form', 'Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponent')
        ->args([
            service('sylius.repository.channel'),
            service('form.factory'),
            '%sylius.model.channel.class%',
            'Sylius\Bundle\AdminBundle\Form\Type\ChannelType',
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:channel:form']);

    $services->set('sylius_admin.twig.component.country.form', 'Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponent')
        ->args([
            service('sylius.repository.country'),
            service('form.factory'),
            '%sylius.model.country.class%',
            'Sylius\Bundle\AdminBundle\Form\Type\CountryType',
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:country:form']);

    $services->set('sylius_admin.twig.component.currency.form', 'Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponent')
        ->args([
            service('sylius.repository.currency'),
            service('form.factory'),
            '%sylius.model.currency.class%',
            'Sylius\Bundle\AdminBundle\Form\Type\CurrencyType',
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:currency:form']);

    $services->set('sylius_admin.twig.component.customer.form', 'Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponent')
        ->args([
            service('sylius.repository.customer'),
            service('form.factory'),
            '%sylius.model.customer.class%',
            'Sylius\Bundle\AdminBundle\Form\Type\CustomerType',
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:customer:form']);

    $services->set('sylius_admin.twig.component.customer_group.form', 'Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponent')
        ->args([
            service('sylius.repository.customer_group'),
            service('form.factory'),
            '%sylius.model.customer_group.class%',
            'Sylius\Bundle\AdminBundle\Form\Type\CustomerGroupType',
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:customer_group:form']);

    $services->set('sylius_admin.twig.component.exchange_rate.form', 'Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponent')
        ->args([
            service('sylius.repository.exchange_rate'),
            service('form.factory'),
            '%sylius.model.exchange_rate.class%',
            'Sylius\Bundle\AdminBundle\Form\Type\ExchangeRateType',
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:exchange_rate:form']);

    $services->set('sylius_admin.twig.component.locale.form', 'Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponent')
        ->args([
            service('sylius.repository.locale'),
            service('form.factory'),
            '%sylius.model.locale.class%',
            'Sylius\Bundle\AdminBundle\Form\Type\LocaleType',
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:locale:form']);

    $services->set('sylius_admin.twig.component.payment_method.form', 'Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponent')
        ->args([
            service('sylius.repository.payment_method'),
            service('form.factory'),
            '%sylius.model.payment_method.class%',
            'Sylius\Bundle\AdminBundle\Form\Type\PaymentMethodType',
        ])
        ->tag('sylius.twig_component', ['key' => 'sylius_admin:payment_method:form']);

    $services->set('sylius_admin.twig.component.product.generate_product_variants_form', 'Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponent')
        ->args([
            service('sylius.repository.product'),
            service('form.factory'),
            '%sylius.model.product.class%',
            'Sylius\Bundle\AdminBundle\Form\Type\ProductGenerateVariantsType',
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:product:generate_product_variants_form']);

    $services->set('sylius_admin.twig.component.product_association_type.form', 'Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponent')
        ->args([
            service('sylius.repository.product_association_type'),
            service('form.factory'),
            '%sylius.model.product_association_type.class%',
            'Sylius\Bundle\AdminBundle\Form\Type\ProductAssociationTypeType',
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:product_association_type:form']);

    $services->set('sylius_admin.twig.component.product_review.form', 'Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponent')
        ->args([
            service('sylius.repository.product_review'),
            service('form.factory'),
            '%sylius.model.product_review.class%',
            'Sylius\Bundle\AdminBundle\Form\Type\ProductReviewType',
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:product_review:form']);

    $services->set('sylius_admin.twig.component.promotion.form', 'Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponent')
        ->args([
            service('sylius.repository.promotion'),
            service('form.factory'),
            '%sylius.model.promotion.class%',
            'Sylius\Bundle\AdminBundle\Form\Type\PromotionType',
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:promotion:form']);

    $services->set('sylius_admin.twig.component.shipping_category.form', 'Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponent')
        ->args([
            service('sylius.repository.shipping_category'),
            service('form.factory'),
            '%sylius.model.shipping_category.class%',
            'Sylius\Bundle\AdminBundle\Form\Type\ShippingCategoryType',
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:shipping_category:form']);

    $services->set('sylius_admin.twig.component.shipping_method.form', 'Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponent')
        ->args([
            service('sylius.repository.shipping_method'),
            service('form.factory'),
            '%sylius.model.shipping_method.class%',
            'Sylius\Bundle\AdminBundle\Form\Type\ShippingMethodType',
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:shipping_method:form']);

    $services->set('sylius_admin.twig.component.tax_category.form', 'Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponent')
        ->args([
            service('sylius.repository.tax_category'),
            service('form.factory'),
            '%sylius.model.tax_category.class%',
            'Sylius\Bundle\AdminBundle\Form\Type\TaxCategoryType',
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:tax_category:form']);

    $services->set('sylius_admin.twig.component.tax_rate.form', 'Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponent')
        ->args([
            service('sylius.repository.tax_rate'),
            service('form.factory'),
            '%sylius.model.tax_rate.class%',
            'Sylius\Bundle\AdminBundle\Form\Type\TaxRateType',
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:tax_rate:form']);

    $services->set('sylius_admin.twig.component.render_entity_with_template', 'Sylius\Bundle\AdminBundle\Twig\Component\Shared\RenderEntityWithTemplateComponent')
        ->args([service('doctrine.orm.entity_manager')])
        ->tag('sylius.twig_component', ['key' => 'sylius_admin:render_entity_with_template']);

    $services->set('sylius_admin.twig.component.shared.navbar.notifications', 'Sylius\Bundle\AdminBundle\Twig\Component\Shared\Navbar\NotificationsComponent')
        ->args([
            service('sylius_admin.provider.notification'),
            '%sylius.admin.notification.enabled%',
        ])
        ->tag('sylius.twig_component', ['key' => 'sylius_admin:navbar:notifications']);

    $services->set('sylius_admin.twig.component.shared.navbar.shop_preview', 'Sylius\Bundle\AdminBundle\Twig\Component\Shared\Navbar\ShopPreviewComponent')
        ->args([service('sylius.repository.channel')])
        ->tag('sylius.twig_component', ['key' => 'sylius_admin:navbar:shop_preview']);

    $services->set('sylius_admin.twig.component.shared.navbar.user_dropdown', 'Sylius\Bundle\AdminBundle\Twig\Component\Shared\Navbar\UserDropdownComponent')
        ->args([
            service('router'),
            service('sylius_admin.provider.logged_in_admin_user'),
        ])
        ->tag('sylius.twig_component', ['key' => 'sylius_admin:navbar:user_dropdown']);
};
