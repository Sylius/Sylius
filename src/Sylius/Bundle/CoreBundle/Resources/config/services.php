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

use Sylius\Bundle\CoreBundle\Assigner\CustomerIpAssigner;
use Sylius\Bundle\CoreBundle\Assigner\IpAssignerInterface;
use Sylius\Bundle\CoreBundle\Collector\CartCollector;
use Sylius\Bundle\CoreBundle\Collector\SyliusCollector;
use Sylius\Bundle\CoreBundle\Context\CustomerContext;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\Handler\ResourceDeleteHandler;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\Handler\ResourceUpdateHandler;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\Inventory\Operator\OrderInventoryOperator as OrmOrderInventoryOperator;
use Sylius\Bundle\CoreBundle\Form\Type\Grid\Filter\ResourceAutocompleteFilterType;
use Sylius\Bundle\CoreBundle\Order\Checker\OrderPromotionsIntegrityChecker;
use Sylius\Bundle\CoreBundle\Order\Checker\OrderPromotionsIntegrityCheckerInterface;
use Sylius\Bundle\CoreBundle\Order\NumberGenerator\SequentialOrderNumberGenerator;
use Sylius\Bundle\CoreBundle\PriceHistory\Remover\ChannelPricingLogEntriesRemover;
use Sylius\Bundle\CoreBundle\PriceHistory\Remover\ChannelPricingLogEntriesRemoverInterface;
use Sylius\Bundle\CoreBundle\Remover\ReviewerReviewsRemover;
use Sylius\Bundle\CoreBundle\Remover\ReviewerReviewsRemoverInterface;
use Sylius\Bundle\CoreBundle\Resolver\CustomerResolver;
use Sylius\Bundle\CoreBundle\Resolver\CustomerResolverInterface;
use Sylius\Bundle\CoreBundle\Routing\RequestContext;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\CoreBundle\SectionResolver\UriBasedSectionProvider;
use Sylius\Bundle\CoreBundle\Security\ImpersonationVoter;
use Sylius\Bundle\CoreBundle\Security\UserPasswordResetter;
use Sylius\Bundle\CoreBundle\ShippingMethod\Updater\ShippingMethodUpdater;
use Sylius\Bundle\CoreBundle\Twig\CheckoutStepsExtension;
use Sylius\Bundle\CoreBundle\Twig\ProductVariantsMapExtension;
use Sylius\Component\Core\Calculator\CatalogPricesCalculatorInterface;
use Sylius\Component\Core\Calculator\ProductVariantPriceCalculator;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Cart\Modifier\LimitingOrderItemQuantityModifier;
use Sylius\Component\Core\Cart\Resolver\CreatedByGuestFlagResolver;
use Sylius\Component\Core\Cart\Resolver\CreatedByGuestFlagResolverInterface;
use Sylius\Component\Core\Customer\CustomerAddressAdderInterface;
use Sylius\Component\Core\Customer\CustomerOrderAddressesSaver;
use Sylius\Component\Core\Customer\CustomerUniqueAddressAdder;
use Sylius\Component\Core\Customer\OrderAddressesSaverInterface;
use Sylius\Component\Core\Customer\Statistics\CustomerStatisticsProvider;
use Sylius\Component\Core\Customer\Statistics\CustomerStatisticsProviderInterface;
use Sylius\Component\Core\Distributor\IntegerDistributor;
use Sylius\Component\Core\Distributor\IntegerDistributorInterface;
use Sylius\Component\Core\Distributor\MinimumPriceDistributor;
use Sylius\Component\Core\Distributor\MinimumPriceDistributorInterface;
use Sylius\Component\Core\Distributor\ProportionalIntegerDistributor;
use Sylius\Component\Core\Distributor\ProportionalIntegerDistributorInterface;
use Sylius\Component\Core\Factory\AddressFactory;
use Sylius\Component\Core\Factory\CartItemFactory;
use Sylius\Component\Core\Factory\ChannelFactory;
use Sylius\Component\Core\Factory\CustomerAfterCheckoutFactory;
use Sylius\Component\Core\Factory\CustomerAfterCheckoutFactoryInterface;
use Sylius\Component\Core\Filesystem\Adapter\FlysystemFilesystemAdapter;
use Sylius\Component\Core\Generator\ImagePathGeneratorInterface;
use Sylius\Component\Core\Generator\UploadedImagePathGenerator;
use Sylius\Component\Core\Grid\Filter\ResourceAutocompleteFilter;
use Sylius\Component\Core\Inventory\Checker\OrderItemAvailabilityChecker;
use Sylius\Component\Core\Inventory\Checker\OrderItemAvailabilityCheckerInterface;
use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperator;
use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use Sylius\Component\Core\Order\OrderItemNamesSetter;
use Sylius\Component\Core\Order\OrderItemNamesSetterInterface;
use Sylius\Component\Core\Payment\IdBasedInvoiceNumberGenerator;
use Sylius\Component\Core\Payment\InvoiceNumberGeneratorInterface;
use Sylius\Component\Core\Payment\Provider\OrderPaymentProvider;
use Sylius\Component\Core\Payment\Provider\OrderPaymentProviderInterface;
use Sylius\Component\Core\Payment\Remover\OrderPaymentsRemover;
use Sylius\Component\Core\Payment\Remover\OrderPaymentsRemoverInterface;
use Sylius\Component\Core\Positioner\Positioner;
use Sylius\Component\Core\Positioner\PositionerInterface;
use Sylius\Component\Core\Resolver\ChannelBasedPaymentMethodsResolver;
use Sylius\Component\Core\Resolver\DefaultPaymentMethodResolver;
use Sylius\Component\Core\Resolver\EligibleDefaultShippingMethodResolver;
use Sylius\Component\Core\Resolver\TaxationAddressResolver;
use Sylius\Component\Core\Resolver\ZoneAndChannelBasedShippingMethodsResolver;
use Sylius\Component\Core\ShippingMethod\Updater\ChannelAwareShippingMethodUpdaterInterface;
use Sylius\Component\Core\Statistics\Registry\OrdersCountProviderRegistry;
use Sylius\Component\Core\Statistics\Registry\OrdersCountProviderRegistryInterface;
use Sylius\Component\Core\Statistics\Registry\OrdersTotalsProviderRegistryInterface;
use Sylius\Component\Core\Statistics\Registry\OrdersTotalsProvidersRegistry;
use Sylius\Component\Core\TokenAssigner\OrderTokenAssignerInterface;
use Sylius\Component\Core\TokenAssigner\UniqueIdBasedOrderTokenAssigner;
use Sylius\Component\Core\Updater\UnpaidOrdersStateUpdater;
use Sylius\Component\Core\Updater\UnpaidOrdersStateUpdaterInterface;
use Sylius\Component\Core\Uploader\ImageUploader;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\Component\Payment\Resolver\DefaultPaymentMethodResolverInterface;

return static function (ContainerConfigurator $container) {
    $container->import('services/*.php');

    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.order_item_quantity_modifier.limit', 9999);
    $parameters->set('env(SYLIUS_UNSECURED_URLS)', 'yes');
    $parameters->set('sylius.unsecured_urls', '%env(bool:SYLIUS_UNSECURED_URLS)%');
    $parameters->set('sylius.channel_pricing_log_entry.old_logs_removal_batch_size', 100);

    $services->set('sylius.distributor.integer', IntegerDistributor::class);
    $services->alias(IntegerDistributorInterface::class, 'sylius.distributor.integer');

    $services->set('sylius.distributor.proportional_integer', ProportionalIntegerDistributor::class);
    $services->alias(ProportionalIntegerDistributorInterface::class, 'sylius.distributor.proportional_integer');

    $services
        ->set('sylius.distributor.minimum_price', MinimumPriceDistributor::class)
        ->args([service('sylius.distributor.proportional_integer')])
    ;
    $services->alias(MinimumPriceDistributorInterface::class, 'sylius.distributor.minimum_price');

    $services->set('sylius.generator.invoice_number.id_based', IdBasedInvoiceNumberGenerator::class);
    $services->alias(InvoiceNumberGeneratorInterface::class, 'sylius.generator.invoice_number.id_based');

    $services
        ->set('sylius.uploader.image', ImageUploader::class)
        ->args([
            service('sylius.adapter.filesystem.default'),
            service('sylius.generator.image_path'),
        ])
        ->public()
    ;
    $services->alias(ImageUploaderInterface::class, 'sylius.uploader.image')->public();

    $services
        ->set('sylius.adapter.filesystem.flysystem', FlysystemFilesystemAdapter::class)
        ->args([service('sylius.storage')])
    ;

    $services->set('sylius.generator.image_path', UploadedImagePathGenerator::class);
    $services->alias(ImagePathGeneratorInterface::class, 'sylius.generator.image_path');

    $services
        ->set('sylius.collector.core', SyliusCollector::class)
        ->args([
            service('sylius.context.shopper'),
            '%kernel.bundles%',
            '%locale%',
        ])
        ->tag('data_collector', ['template' => '@SyliusCore/Collector/sylius.html.twig', 'id' => 'sylius_core', 'priority' => -512])
    ;

    $services
        ->set('sylius.collector.cart', CartCollector::class)
        ->args([service('sylius.context.cart')])
        ->tag('data_collector', ['template' => '@SyliusCore/Collector/cart.html.twig', 'id' => 'sylius_cart', 'priority' => -512])
    ;

    $services
        ->set('sylius.resolver.shipping_methods.zones_and_channel_based', ZoneAndChannelBasedShippingMethodsResolver::class)
        ->args([
            service('sylius.repository.shipping_method'),
            service('sylius.matcher.zone'),
            service('sylius.checker.shipping_method_eligibility'),
        ])
        ->tag('sylius.shipping_method_resolver', ['type' => 'zones_and_channel_based', 'label' => 'sylius.shipping_methods_resolver.zones_and_channel_based', 'priority' => 1])
    ;

    $services
        ->set('sylius.resolver.payment_methods.channel_based', ChannelBasedPaymentMethodsResolver::class)
        ->args([service('sylius.repository.payment_method')])
        ->tag('sylius.payment_method_resolver', ['type' => 'channel_based', 'label' => 'sylius.payment_methods_resolver.channel_based', 'priority' => 1])
    ;

    $services
        ->set('sylius.resolver.payment_method.default', DefaultPaymentMethodResolver::class)
        ->args([service('sylius.repository.payment_method')])
    ;
    $services->alias(DefaultPaymentMethodResolverInterface::class, 'sylius.resolver.payment_method.default');

    $services
        ->set('sylius.resolver.shipping_method.default', EligibleDefaultShippingMethodResolver::class)
        ->args([
            service('sylius.repository.shipping_method'),
            service('sylius.checker.shipping_method_eligibility'),
            service('sylius.matcher.zone'),
        ])
    ;

    $services
        ->set('sylius.resolver.taxation_address', TaxationAddressResolver::class)
        ->args(['%sylius_core.taxation.shipping_address_based_taxation%'])
    ;

    $services
        ->set('sylius.context.customer', CustomerContext::class)
        ->args([
            service('security.token_storage'),
            service('security.authorization_checker'),
        ])
        ->public()
    ;
    $services->alias(CustomerContextInterface::class, 'sylius.context.customer')->public();

    $services->set('sylius.checker.inventory.order_item_availability', OrderItemAvailabilityChecker::class);
    $services->alias(OrderItemAvailabilityCheckerInterface::class, 'sylius.checker.inventory.order_item_availability');

    $services->set('sylius.operator.inventory.order_inventory', OrderInventoryOperator::class)->public();
    $services->alias(OrderInventoryOperatorInterface::class, 'sylius.operator.inventory.order_inventory')->public();

    $services
        ->set('sylius.custom_operator.inventory.order_inventory', OrmOrderInventoryOperator::class)
        ->decorate('sylius.operator.inventory.order_inventory')
        ->args([
            service('sylius.custom_operator.inventory.order_inventory.inner'),
            service('sylius.manager.product_variant'),
        ])
    ;

    $services
        ->set('sylius.custom_factory.order_item', CartItemFactory::class)
        ->decorate('sylius.factory.order_item', null, 256)
        ->args([
            service('sylius.custom_factory.order_item.inner'),
            service('sylius.resolver.product_variant'),
            service('sylius.modifier.order_item_quantity'),
        ])
    ;
    $services->alias('sylius.factory.cart_item', 'sylius.custom_factory.order_item');

    $services
        ->set('sylius.custom_factory.address', AddressFactory::class)
        ->decorate('sylius.factory.address', null, 256)
        ->args([service('sylius.custom_factory.address.inner')])
    ;

    $services
        ->set('sylius.custom_factory.channel', ChannelFactory::class)
        ->decorate('sylius.factory.channel', null, 256)
        ->args([
            service('sylius.custom_factory.channel.inner'),
            'order_items_based',
            service('sylius.factory.channel_price_history_config'),
        ])
    ;

    $services
        ->set('sylius.factory.customer_after_checkout', CustomerAfterCheckoutFactory::class)
        ->args([service('sylius.factory.customer')])
        ->public()
    ;
    $services->alias(CustomerAfterCheckoutFactoryInterface::class, 'sylius.factory.customer_after_checkout')->public();

    $services
        ->set('sylius.twig.extension.product_variants_map', ProductVariantsMapExtension::class)
        ->args([service('sylius.provider.product_variant_map')])
        ->tag('twig.extension')
    ;

    $services
        ->set('sylius.twig.extension.checkout_steps', CheckoutStepsExtension::class)
        ->args([
            service('sylius.checker.order_payment_method_selection_requirement'),
            service('sylius.checker.order_shipping_method_selection_requirement'),
        ])
        ->tag('twig.extension')
    ;

    $services
        ->set('sylius.assigner.order_token.unique_id_based', UniqueIdBasedOrderTokenAssigner::class)
        ->args([
            service('sylius.random_generator'),
            '%sylius_core.order_token_length%',
        ])
        ->public()
    ;
    $services->alias(OrderTokenAssignerInterface::class, 'sylius.assigner.order_token.unique_id_based')->public();

    $services
        ->set('sylius.adder.customer.unique_address', CustomerUniqueAddressAdder::class)
        ->args([service('sylius.comparator.address')])
    ;
    $services->alias(CustomerAddressAdderInterface::class, 'sylius.adder.customer.unique_address');

    $services
        ->set('sylius.saver.customer.order_addresses', CustomerOrderAddressesSaver::class)
        ->args([service('sylius.adder.customer.unique_address')])
        ->public()
    ;
    $services->alias(OrderAddressesSaverInterface::class, 'sylius.saver.customer.order_addresses')->public();

    $services
        ->set('sylius.modifier.cart.limiting_order_item_quantity', LimitingOrderItemQuantityModifier::class)
        ->decorate('sylius.modifier.order_item_quantity', null, 256)
        ->args([
            service('sylius.modifier.cart.limiting_order_item_quantity.inner'),
            '%sylius.order_item_quantity_modifier.limit%',
        ])
    ;

    $services->set('sylius.assigner.customer_id', CustomerIpAssigner::class);
    $services->alias(IpAssignerInterface::class, 'sylius.assigner.customer_id');

    $services
        ->set('sylius.calculator.product_variant_price', ProductVariantPriceCalculator::class)
        ->args([service('sylius.checker.product_variant_lowest_price_display')])
    ;
    $services->alias(ProductVariantPricesCalculatorInterface::class, 'sylius.calculator.product_variant_price');

    $services
        ->set('sylius.calculator.product_variant_catalog_price', ProductVariantPriceCalculator::class)
        ->args([service('sylius.checker.product_variant_lowest_price_display')])
    ;
    $services->alias(CatalogPricesCalculatorInterface::class, 'sylius.calculator.product_variant_catalog_price');

    $services
        ->set('sylius.section_resolver.uri_based', UriBasedSectionProvider::class)
        ->args([
            service('request_stack'),
            [],
        ])
    ;
    $services->alias(SectionProviderInterface::class, 'sylius.section_resolver.uri_based');

    $services
        ->set('sylius.remover.reviewer_reviews', ReviewerReviewsRemover::class)
        ->args([
            service('sylius.repository.product_review'),
            service('sylius.manager.product_review'),
            service('sylius.updater.product_review.average_rating'),
        ])
    ;
    $services->alias(ReviewerReviewsRemoverInterface::class, 'sylius.remover.reviewer_reviews');

    $services
        ->set('sylius.remover.channel_pricing_log_entries', ChannelPricingLogEntriesRemover::class)
        ->args([
            service('sylius.repository.channel_pricing_log_entry'),
            service('doctrine.orm.entity_manager'),
            service('clock'),
            service('event_dispatcher'),
            '%sylius.channel_pricing_log_entry.old_logs_removal_batch_size%',
        ])
    ;
    $services->alias(ChannelPricingLogEntriesRemoverInterface::class, 'sylius.remover.channel_pricing_log_entries');

    $services
        ->set('sylius.updater.unpaid_orders_state', UnpaidOrdersStateUpdater::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius_abstraction.state_machine'),
            '%sylius_order.order_expiration_period%',
            service('logger'),
            service('sylius.manager.order'),
        ])
    ;
    $services->alias(UnpaidOrdersStateUpdaterInterface::class, 'sylius.updater.unpaid_orders_state');

    $services
        ->set('sylius.provider.payment.order', OrderPaymentProvider::class)
        ->args([
            service('sylius.resolver.payment_method.default'),
            service('sylius.factory.payment'),
            service('sylius_abstraction.state_machine'),
        ])
    ;
    $services->alias(OrderPaymentProviderInterface::class, 'sylius.provider.payment.order');

    $services->set('sylius.remover.payment.order', OrderPaymentsRemover::class);
    $services->alias(OrderPaymentsRemoverInterface::class, 'sylius.remover.payment.order');

    $services
        ->set('sylius.provider.statistics.customer', CustomerStatisticsProvider::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius.repository.channel'),
        ])
    ;
    $services->alias(CustomerStatisticsProviderInterface::class, 'sylius.provider.statistics.customer');

    $services
        ->set('sylius.number_generator.sequential_order', SequentialOrderNumberGenerator::class)
        ->args([
            service('sylius.repository.order_sequence'),
            service('sylius.factory.order_sequence'),
            service('sylius.manager.order_sequence'),
        ])
    ;

    $services
        ->set('sylius.custom_resource_controller.resource_update_handler', ResourceUpdateHandler::class)
        ->decorate('sylius.resource_controller.resource_update_handler')
        ->args([
            service('sylius.custom_resource_controller.resource_update_handler.inner'),
            service('doctrine.orm.entity_manager'),
        ])
    ;

    $services
        ->set('sylius.custom_resource_controller.resource_delete_handler', ResourceDeleteHandler::class)
        ->decorate('sylius.resource_controller.resource_delete_handler')
        ->args([
            service('sylius.custom_resource_controller.resource_delete_handler.inner'),
            service('doctrine.orm.entity_manager'),
        ])
    ;

    $services->set('sylius.setter.order.item_names', OrderItemNamesSetter::class)->public();
    $services->alias(OrderItemNamesSetterInterface::class, 'sylius.setter.order.item_names')->public();

    $services
        ->set('sylius.grid_filter.resource_autocomplete', ResourceAutocompleteFilter::class)
        ->tag('sylius.grid_filter', ['type' => 'resource_autocomplete', 'form_type' => ResourceAutocompleteFilterType::class])
    ;

    $services
        ->set('sylius.resolver.cart.created_by_guest_flag', CreatedByGuestFlagResolver::class)
        ->args([service('security.token_storage')])
    ;
    $services->alias(CreatedByGuestFlagResolverInterface::class, 'sylius.resolver.cart.created_by_guest_flag');

    $services
        ->set('sylius.checker.order.promotions_integrity', OrderPromotionsIntegrityChecker::class)
        ->args([service('sylius.order_processing.order_processor')])
    ;
    $services->alias(OrderPromotionsIntegrityCheckerInterface::class, 'sylius.checker.order.promotions_integrity');

    $services
        ->set('sylius.resetter.user_password.admin', UserPasswordResetter::class)
        ->args([
            service('sylius.repository.admin_user'),
            service('sylius.security.password_updater'),
            '%sylius.admin_user.token.password_reset.ttl%',
        ])
    ;

    $services
        ->set('sylius.resetter.user_password.shop', UserPasswordResetter::class)
        ->args([
            service('sylius.repository.shop_user'),
            service('sylius.security.password_updater'),
            '%sylius.shop_user.token.password_reset.ttl%',
        ])
    ;

    $services
        ->set('sylius.resolver.customer', CustomerResolver::class)
        ->args([
            service('sylius.factory.customer'),
            service('sylius.provider.customer'),
        ])
        ->public()
    ;
    $services->alias(CustomerResolverInterface::class, 'sylius.resolver.customer')->public();

    $services
        ->set('sylius.registry.statistics.orders_totals_providers', OrdersTotalsProvidersRegistry::class)
        ->args([tagged_iterator('sylius.statistics.orders_totals_provider', indexAttribute: 'type')])
        ->tag('sylius.statistics.provider_registry', ['priority' => 100])
    ;
    $services->alias(OrdersTotalsProviderRegistryInterface::class, 'sylius.registry.statistics.orders_totals_providers');

    $services
        ->set('sylius.registry.statistics.orders_count_provider', OrdersCountProviderRegistry::class)
        ->args([tagged_iterator('sylius.statistics.orders_count_provider', indexAttribute: 'type')])
        ->tag('sylius.statistics.provider_registry', ['priority' => 0])
    ;
    $services->alias(OrdersCountProviderRegistryInterface::class, 'sylius.registry.statistics.orders_count_provider');

    $services->set('sylius.positioner', Positioner::class)->public();
    $services->alias(PositionerInterface::class, 'sylius.positioner')->public();

    $services->set('sylius.routing.request_context', RequestContext::class)
        ->decorate('router.request_context')
        ->args([
            service('.inner'),
            param('sylius_core.routing.bc_layer'),
        ])
    ;

    $services
        ->set('sylius.security.voter.impersonation', ImpersonationVoter::class)
        ->args([
            service('request_stack'),
            service('security.firewall.map'),
        ])
        ->tag('security.voter')
    ;

    $services
        ->set('sylius.updater.shipping_method', ShippingMethodUpdater::class)
        ->args([
            service('sylius.repository.shipping_method'),
            service('doctrine.orm.entity_manager'),
        ])
    ;
    $services->alias(ChannelAwareShippingMethodUpdaterInterface::class, 'sylius.updater.shipping_method');
};
