<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $container->import('services/*.php');
    
    $parameters->set('sylius.order_item_quantity_modifier.limit', 9999);
    $parameters->set('env(SYLIUS_UNSECURED_URLS)', 'yes');
    $parameters->set('sylius.unsecured_urls', '%env(bool:SYLIUS_UNSECURED_URLS)%');
    $parameters->set('sylius.channel_pricing_log_entry.old_logs_removal_batch_size', 100);

    $services->set('sylius.distributor.integer', 'Sylius\Component\Core\Distributor\IntegerDistributor');

    $services->alias('Sylius\Component\Core\Distributor\IntegerDistributorInterface', 'sylius.distributor.integer');

    $services->set('sylius.distributor.proportional_integer', 'Sylius\Component\Core\Distributor\ProportionalIntegerDistributor');

    $services->alias('Sylius\Component\Core\Distributor\ProportionalIntegerDistributorInterface', 'sylius.distributor.proportional_integer');

    $services->set('sylius.distributor.minimum_price', 'Sylius\Component\Core\Distributor\MinimumPriceDistributor')
        ->args([service('sylius.distributor.proportional_integer')]);

    $services->alias('Sylius\Component\Core\Distributor\MinimumPriceDistributorInterface', 'sylius.distributor.minimum_price');

    $services->set('sylius.generator.invoice_number.id_based', 'Sylius\Component\Core\Payment\IdBasedInvoiceNumberGenerator');

    $services->alias('Sylius\Component\Core\Payment\InvoiceNumberGeneratorInterface', 'sylius.generator.invoice_number.id_based');

    $services->set('sylius.uploader.image', 'Sylius\Component\Core\Uploader\ImageUploader')
        ->public()
        ->args([
            service('sylius.adapter.filesystem.default'),
            service('sylius.generator.image_path'),
        ]);

    $services->alias('Sylius\Component\Core\Uploader\ImageUploaderInterface', 'sylius.uploader.image')
        ->public();

    $services->set('sylius.adapter.filesystem.flysystem', 'Sylius\Component\Core\Filesystem\Adapter\FlysystemFilesystemAdapter')
        ->args([service('sylius.storage')]);

    $services->set('sylius.generator.image_path', 'Sylius\Component\Core\Generator\UploadedImagePathGenerator');

    $services->alias('Sylius\Component\Core\Generator\ImagePathGeneratorInterface', 'sylius.generator.image_path');

    $services->set('sylius.collector.core', 'Sylius\Bundle\CoreBundle\Collector\SyliusCollector')
        ->args([
            service('sylius.context.shopper'),
            '%kernel.bundles%',
            '%locale%',
        ])
        ->tag('data_collector', ['template' => '@SyliusCore/Collector/sylius.html.twig', 'id' => 'sylius_core', 'priority' => -512]);

    $services->set('sylius.collector.cart', 'Sylius\Bundle\CoreBundle\Collector\CartCollector')
        ->private()
        ->args([service('sylius.context.cart')])
        ->tag('data_collector', ['template' => '@SyliusCore/Collector/cart.html.twig', 'id' => 'sylius_cart', 'priority' => -512]);

    $services->set('sylius.resolver.shipping_methods.zones_and_channel_based', 'Sylius\Component\Core\Resolver\ZoneAndChannelBasedShippingMethodsResolver')
        ->args([
            service('sylius.repository.shipping_method'),
            service('sylius.matcher.zone'),
            service('sylius.checker.shipping_method_eligibility'),
        ])
        ->tag('sylius.shipping_method_resolver', ['type' => 'zones_and_channel_based', 'label' => 'sylius.shipping_methods_resolver.zones_and_channel_based', 'priority' => 1]);

    $services->set('sylius.resolver.payment_methods.channel_based', 'Sylius\Component\Core\Resolver\ChannelBasedPaymentMethodsResolver')
        ->args([service('sylius.repository.payment_method')])
        ->tag('sylius.payment_method_resolver', ['type' => 'channel_based', 'label' => 'sylius.payment_methods_resolver.channel_based', 'priority' => 1]);

    $services->set('sylius.resolver.payment_method.default', 'Sylius\Component\Core\Resolver\DefaultPaymentMethodResolver')
        ->args([service('sylius.repository.payment_method')]);

    $services->alias('Sylius\Component\Payment\Resolver\DefaultPaymentMethodResolverInterface', 'sylius.resolver.payment_method.default');

    $services->set('sylius.resolver.shipping_method.default', 'Sylius\Component\Core\Resolver\EligibleDefaultShippingMethodResolver')
        ->args([
            service('sylius.repository.shipping_method'),
            service('sylius.checker.shipping_method_eligibility'),
            service('sylius.matcher.zone'),
        ]);

    $services->set('sylius.resolver.taxation_address', 'Sylius\Component\Core\Resolver\TaxationAddressResolver')
        ->args(['%sylius_core.taxation.shipping_address_based_taxation%']);

    $services->set('sylius.context.customer', 'Sylius\Bundle\CoreBundle\Context\CustomerContext')
        ->public()
        ->args([
            service('security.token_storage'),
            service('security.authorization_checker'),
        ]);

    $services->alias('Sylius\Component\Customer\Context\CustomerContextInterface', 'sylius.context.customer')
        ->public();

    $services->set('sylius.checker.inventory.order_item_availability', 'Sylius\Component\Core\Inventory\Checker\OrderItemAvailabilityChecker');

    $services->alias('Sylius\Component\Core\Inventory\Checker\OrderItemAvailabilityCheckerInterface', 'sylius.checker.inventory.order_item_availability');

    $services->set('sylius.operator.inventory.order_inventory', 'Sylius\Component\Core\Inventory\Operator\OrderInventoryOperator')
        ->public();

    $services->alias('Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface', 'sylius.operator.inventory.order_inventory')
        ->public();

    $services->set('sylius.custom_operator.inventory.order_inventory', 'Sylius\Bundle\CoreBundle\Doctrine\ORM\Inventory\Operator\OrderInventoryOperator')
        ->private()
        ->decorate('sylius.operator.inventory.order_inventory')
        ->args([
            service('sylius.custom_operator.inventory.order_inventory.inner'),
            service('sylius.manager.product_variant'),
        ]);

    $services->set('sylius.custom_factory.order_item', 'Sylius\Component\Core\Factory\CartItemFactory')
        ->decorate('sylius.factory.order_item', null, 256)
        ->args([
            service('sylius.custom_factory.order_item.inner'),
            service('sylius.resolver.product_variant'),
            service('sylius.modifier.order_item_quantity'),
        ]);

    $services->alias('sylius.factory.cart_item', 'sylius.custom_factory.order_item');

    $services->set('sylius.custom_factory.address', 'Sylius\Component\Core\Factory\AddressFactory')
        ->decorate('sylius.factory.address', null, 256)
        ->args([service('sylius.custom_factory.address.inner')]);

    $services->set('sylius.custom_factory.channel', 'Sylius\Component\Core\Factory\ChannelFactory')
        ->decorate('sylius.factory.channel', null, 256)
        ->args([
            service('sylius.custom_factory.channel.inner'),
            'order_items_based',
            service('sylius.factory.channel_price_history_config'),
        ]);

    $services->set('sylius.factory.customer_after_checkout', 'Sylius\Component\Core\Factory\CustomerAfterCheckoutFactory')
        ->public()
        ->args([service('sylius.factory.customer')]);

    $services->alias('Sylius\Component\Core\Factory\CustomerAfterCheckoutFactoryInterface', 'sylius.factory.customer_after_checkout')
        ->public();

    $services->set('sylius.twig.extension.product_variants_map', 'Sylius\Bundle\CoreBundle\Twig\ProductVariantsMapExtension')
        ->args([service('sylius.provider.product_variant_map')])
        ->tag('twig.extension');

    $services->set('sylius.twig.extension.checkout_steps', 'Sylius\Bundle\CoreBundle\Twig\CheckoutStepsExtension')
        ->args([
            service('sylius.checker.order_payment_method_selection_requirement'),
            service('sylius.checker.order_shipping_method_selection_requirement'),
        ])
        ->tag('twig.extension');

    $services->set('sylius.assigner.order_token.unique_id_based', 'Sylius\Component\Core\TokenAssigner\UniqueIdBasedOrderTokenAssigner')
        ->public()
        ->args([
            service('sylius.random_generator'),
            '%sylius_core.order_token_length%',
        ]);

    $services->alias('Sylius\Component\Core\TokenAssigner\OrderTokenAssignerInterface', 'sylius.assigner.order_token.unique_id_based')
        ->public();

    $services->set('sylius.adder.customer.unique_address', 'Sylius\Component\Core\Customer\CustomerUniqueAddressAdder')
        ->args([service('sylius.comparator.address')]);

    $services->alias('Sylius\Component\Core\Customer\CustomerAddressAdderInterface', 'sylius.adder.customer.unique_address');

    $services->set('sylius.saver.customer.order_addresses', 'Sylius\Component\Core\Customer\CustomerOrderAddressesSaver')
        ->public()
        ->args([service('sylius.adder.customer.unique_address')]);

    $services->alias('Sylius\Component\Core\Customer\OrderAddressesSaverInterface', 'sylius.saver.customer.order_addresses')
        ->public();

    $services->set('sylius.modifier.cart.limiting_order_item_quantity', 'Sylius\Component\Core\Cart\Modifier\LimitingOrderItemQuantityModifier')
        ->decorate('sylius.modifier.order_item_quantity', null, 256)
        ->args([
            service('sylius.modifier.cart.limiting_order_item_quantity.inner'),
            '%sylius.order_item_quantity_modifier.limit%',
        ]);

    $services->set('sylius.assigner.customer_id', 'Sylius\Bundle\CoreBundle\Assigner\CustomerIpAssigner');

    $services->alias('Sylius\Bundle\CoreBundle\Assigner\IpAssignerInterface', 'sylius.assigner.customer_id');

    $services->set('sylius.calculator.product_variant_price', 'Sylius\Component\Core\Calculator\ProductVariantPriceCalculator')
        ->args([service('sylius.checker.product_variant_lowest_price_display')]);

    $services->alias('Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface', 'sylius.calculator.product_variant_price');

    $services->set('sylius.section_resolver.uri_based', 'Sylius\Bundle\CoreBundle\SectionResolver\UriBasedSectionProvider')
        ->args([
            service('request_stack'),
            [],
        ]);

    $services->alias('Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface', 'sylius.section_resolver.uri_based');

    $services->set('sylius.remover.reviewer_reviews', 'Sylius\Bundle\CoreBundle\Remover\ReviewerReviewsRemover')
        ->args([
            service('sylius.repository.product_review'),
            service('sylius.manager.product_review'),
            service('sylius.updater.product_review.average_rating'),
        ]);

    $services->alias('Sylius\Bundle\CoreBundle\Remover\ReviewerReviewsRemoverInterface', 'sylius.remover.reviewer_reviews');

    $services->set('sylius.remover.channel_pricing_log_entries', 'Sylius\Bundle\CoreBundle\PriceHistory\Remover\ChannelPricingLogEntriesRemover')
        ->args([
            service('sylius.repository.channel_pricing_log_entry'),
            service('doctrine.orm.entity_manager'),
            service('clock'),
            service('event_dispatcher'),
            '%sylius.channel_pricing_log_entry.old_logs_removal_batch_size%',
        ]);

    $services->alias('Sylius\Bundle\CoreBundle\PriceHistory\Remover\ChannelPricingLogEntriesRemoverInterface', 'sylius.remover.channel_pricing_log_entries');

    $services->set('sylius.updater.unpaid_orders_state', 'Sylius\Component\Core\Updater\UnpaidOrdersStateUpdater')
        ->args([
            service('sylius.repository.order'),
            service('sylius_abstraction.state_machine'),
            '%sylius_order.order_expiration_period%',
            service('logger'),
            service('sylius.manager.order'),
        ]);

    $services->alias('Sylius\Component\Core\Updater\UnpaidOrdersStateUpdaterInterface', 'sylius.updater.unpaid_orders_state');

    $services->set('sylius.provider.payment.order', 'Sylius\Component\Core\Payment\Provider\OrderPaymentProvider')
        ->args([
            service('sylius.resolver.payment_method.default'),
            service('sylius.factory.payment'),
            service('sylius_abstraction.state_machine'),
        ]);

    $services->alias('Sylius\Component\Core\Payment\Provider\OrderPaymentProviderInterface', 'sylius.provider.payment.order');

    $services->set('sylius.remover.payment.order', 'Sylius\Component\Core\Payment\Remover\OrderPaymentsRemover');

    $services->alias('Sylius\Component\Core\Payment\Remover\OrderPaymentsRemoverInterface', 'sylius.remover.payment.order');

    $services->set('sylius.provider.statistics.customer', 'Sylius\Component\Core\Customer\Statistics\CustomerStatisticsProvider')
        ->args([
            service('sylius.repository.order'),
            service('sylius.repository.channel'),
        ]);

    $services->alias('Sylius\Component\Core\Customer\Statistics\CustomerStatisticsProviderInterface', 'sylius.provider.statistics.customer');

    $services->set('sylius.number_generator.sequential_order', 'Sylius\Bundle\CoreBundle\Order\NumberGenerator\SequentialOrderNumberGenerator')
        ->args([
            service('sylius.repository.order_sequence'),
            service('sylius.factory.order_sequence'),
            service('sylius.manager.order_sequence'),
        ]);

    $services->set('sylius.custom_resource_controller.resource_update_handler', 'Sylius\Bundle\CoreBundle\Doctrine\ORM\Handler\ResourceUpdateHandler')
        ->decorate('sylius.resource_controller.resource_update_handler')
        ->args([
            service('sylius.custom_resource_controller.resource_update_handler.inner'),
            service('doctrine.orm.entity_manager'),
        ]);

    $services->set('sylius.custom_resource_controller.resource_delete_handler', 'Sylius\Bundle\CoreBundle\Doctrine\ORM\Handler\ResourceDeleteHandler')
        ->decorate('sylius.resource_controller.resource_delete_handler')
        ->args([
            service('sylius.custom_resource_controller.resource_delete_handler.inner'),
            service('doctrine.orm.entity_manager'),
        ]);

    $services->set('sylius.setter.order.item_names', 'Sylius\Component\Core\Order\OrderItemNamesSetter')
        ->public();

    $services->alias('Sylius\Component\Core\Order\OrderItemNamesSetterInterface', 'sylius.setter.order.item_names')
        ->public();

    $services->set('sylius.grid_filter.resource_autocomplete', 'Sylius\Component\Core\Grid\Filter\ResourceAutocompleteFilter')
        ->tag('sylius.grid_filter', ['type' => 'resource_autocomplete', 'form_type' => 'Sylius\Bundle\CoreBundle\Form\Type\Grid\Filter\ResourceAutocompleteFilterType']);

    $services->set('sylius.resolver.cart.created_by_guest_flag', 'Sylius\Component\Core\Cart\Resolver\CreatedByGuestFlagResolver')
        ->args([service('security.token_storage')]);

    $services->alias('Sylius\Component\Core\Cart\Resolver\CreatedByGuestFlagResolverInterface', 'sylius.resolver.cart.created_by_guest_flag');

    $services->set('sylius.checker.order.promotions_integrity', 'Sylius\Bundle\CoreBundle\Order\Checker\OrderPromotionsIntegrityChecker')
        ->args([service('sylius.order_processing.order_processor')]);

    $services->alias('Sylius\Bundle\CoreBundle\Order\Checker\OrderPromotionsIntegrityCheckerInterface', 'sylius.checker.order.promotions_integrity');

    $services->set('sylius.resetter.user_password.admin', 'Sylius\Bundle\CoreBundle\Security\UserPasswordResetter')
        ->args([
            service('sylius.repository.admin_user'),
            service('sylius.security.password_updater'),
            '%sylius.admin_user.token.password_reset.ttl%',
        ]);

    $services->set('sylius.resetter.user_password.shop', 'Sylius\Bundle\CoreBundle\Security\UserPasswordResetter')
        ->args([
            service('sylius.repository.shop_user'),
            service('sylius.security.password_updater'),
            '%sylius.shop_user.token.password_reset.ttl%',
        ]);

    $services->set('sylius.resolver.customer', 'Sylius\Bundle\CoreBundle\Resolver\CustomerResolver')
        ->public()
        ->args([
            service('sylius.factory.customer'),
            service('sylius.provider.customer'),
        ]);

    $services->alias('Sylius\Bundle\CoreBundle\Resolver\CustomerResolverInterface', 'sylius.resolver.customer')
        ->public();

    $services->set('sylius.registry.statistics.orders_totals_providers', 'Sylius\Component\Core\Statistics\Registry\OrdersTotalsProvidersRegistry')
        ->args([tagged_iterator('sylius.statistics.orders_totals_provider', indexAttribute: 'type')])
        ->tag('sylius.statistics.provider_registry', ['priority' => 100]);

    $services->alias('Sylius\Component\Core\Statistics\Registry\OrdersTotalsProvidersRegistryInterface', 'sylius.registry.statistics.orders_totals_providers');

    $services->set('sylius.registry.statistics.orders_count_provider', 'Sylius\Component\Core\Statistics\Registry\OrdersCountProviderRegistry')
        ->args([tagged_iterator('sylius.statistics.orders_count_provider', indexAttribute: 'type')])
        ->tag('sylius.statistics.provider_registry', ['priority' => 0]);

    $services->alias('Sylius\Component\Core\Statistics\Registry\OrdersCountProviderRegistryInterface', 'sylius.registry.statistics.orders_count_provider');

    $services->set('sylius.positioner', 'Sylius\Component\Core\Positioner\Positioner')
        ->public();

    $services->alias('Sylius\Component\Core\Positioner\PositionerInterface', 'sylius.positioner')
        ->public();

    $services->set('sylius.security.voter.impersonation', 'Sylius\Bundle\CoreBundle\Security\ImpersonationVoter')
        ->args([
            service('request_stack'),
            service('security.firewall.map'),
        ])
        ->tag('security.voter');
};
