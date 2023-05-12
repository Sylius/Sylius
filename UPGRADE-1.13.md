# UPGRADE FROM `v1.12.X` TO `v1.13.0`

1. Starting with Sylius 1.13, the `SyliusPriceHistoryPlugin` is included.
   If you are currently using the plugin in your project, we recommend following the upgrade guide located [here](UPGRADE-FROM-1.12-WITH-PRICE-HISTORY-PLUGIN-TO-1.13.md).

1. The `Sylius\Bundle\CoreBundle\CatalogPromotion\Command\RemoveInactiveCatalogPromotion` command and its handler
   `Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler\RemoveInactiveCatalogPromotionHandler` have been deprecated.
   Use `Sylius\Bundle\CoreBundle\CatalogPromotion\Command\RemoveCatalogPromotion` command instead.

1. Passing `Symfony\Component\Messenger\MessageBusInterface` to `Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionRemovalProcessor`
   as a second and third argument is deprecated.

1. Not passing `Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionRemovalAnnouncerInterface` to `Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionRemovalProcessor`
   as a second argument is deprecated.

1. Not passing `Doctrine\Persistence\ObjectManager` to `Sylius\Component\Core\Updater\UnpaidOrdersStateUpdater`
   as a fifth argument is deprecated.

1. To allow to autoconfigure order processors and cart context and define a priority for them in `1.13` we have introduced
   `Sylius\Bundle\OrderBundle\Attribute\AsCartContext` and `Sylius\Bundle\OrderBundle\Attribute\AsOrderProcessor` attributes. By default, Sylius still configures them using interfaces, but this way you cannot define a priority.
   If you want to define a priority, you need to set the following configuration in your `_sylius.yaml` file:
   ```yaml
   sylius_core:
       autoconfigure_with_attributes: true
   ```
   and use one of the new attributes accordingly to a type of your class, e.g.:
   ```php
    <?php

    declare(strict_types=1);

    namespace App\OrderProcessor;

    use Sylius\Bundle\OrderBundle\Attribute\AsOrderProcessor;
    use Sylius\Component\Order\Model\OrderInterface;
    use Sylius\Component\Order\Processor\OrderProcessorInterface;

    #[AsOrderProcessor(priority: 10)] //priority is optional
    //#[AsOrderProcessor] can be used as well
    final class OrderProcessorWithAttributeStub implements OrderProcessorInterface
    {
        public function process(OrderInterface $order): void
        {
        }
    }
   ```

1. Not passing `Sylius\Component\Core\Checker\ProductVariantLowestPriceDisplayCheckerInterface` 
   to `Sylius\Component\Core\Calculator\ProductVariantPriceCalculator`
   as a first argument is deprecated.

1. Not passing an instance of `Symfony\Component\PropertyAccess\PropertyAccessorInterface`
   to `Sylius\Bundle\CoreBundle\Validator\Constraints\HasEnabledEntityValidator`
   as the second argument is deprecated.

1. Class `\Sylius\Bundle\ShopBundle\Calculator\OrderItemsSubtotalCalculator` has been deprecated. Order items subtotal calculation
   is now available on the Order model `\Sylius\Component\Core\Model\Order::getItemsSubtotal`.

1. The way of getting variants prices based on options has been changed,
   as such the following services were deprecated, please use their new counterpart.
   * instead of `Sylius\Component\Core\Provider\ProductVariantsPricesProviderInterface` use `Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantsMapProviderInterface`
   * instead of `Sylius\Component\Core\Provider\ProductVariantsPricesProvider` use `Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantsPricesMapProvider`
   * instead of `Sylius\Bundle\CoreBundle\Templating\Helper\ProductVariantsPricesHelper` use `Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantsPricesMapProvider`
   * instead of `Sylius\Bundle\CoreBundle\Twig\ProductVariantsPricesExtension` use `Sylius\Bundle\CoreBundle\Twig\ProductVariantsMapExtension`

   Subsequently, the `sylius_product_variant_prices` twig function is deprecated, use `sylius_product_variants_map` instead.

   To add more data per variant create a service implementing the `Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantMapProviderInterface` and tag it with `sylius.product_variant_data_map_provider`.

1. Using Guzzle 6 has been deprecated in favor of Symfony HTTP Client. If you want to still use Guzzle 6 or Guzzle 7,
   you need to install `composer require php-http/guzzle6-adapter` or `composer require php-http/guzzle7-adapter`
   depending on your Guzzle version.
   Subsequently, you need to register the adapter as a `Psr\Http\Client\ClientInterface` service as the following:
    ```yaml
        services:    
            Psr\Http\Client\ClientInterface:
                class: Http\Adapter\Guzzle7\Client # for Guzzle 6 use Http\Adapter\Guzzle6\Client instead
    ```

1. The constructor of `Sylius\Bundle\AdminBundle\Controller\NotificationController` has been changed:

    ```diff
        public function __construct(
    -       private ClientInterface $client,
    -       private MessageFactory $messageFactory,
    +       private ClientInterface|DeprecatedClientInterface $client,
    +       private RequestFactoryInterface|MessageFactory $requestFactory,
            private string $hubUri,
            private string $environment,
    +       private ?StreamFactoryInterface $streamFactory = null,
        ) {
            ...
        }
    ```

1. The `sylius.http_message_factory` service has been deprecated. Use `Psr\Http\Message\RequestFactoryInterface` instead.

1. The `sylius.http_client` has become an alias to `psr18.http_client` service.

1. The `sylius.payum.http_client` has become a service ID of newly created `Sylius\Bundle\PayumBundle\HttpClient\HttpClient`.

1. Validation translation key `sylius.review.rating.range` has been replaced by `sylius.review.rating.not_in_range` in all places used by Sylius. The `sylius.review.rating.range` has been left for backward compatibility and will be removed in Sylius 2.0.

1. The `payum/payum` package has been replaced by concrete packages like `payum/core`, `payum/offline` or `payum/paypal-express-checkout-nvp`. If you need any other component so far provided by `payum/payum` package, you need to install it explicitly.
