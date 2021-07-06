# UPGRADE FROM `v1.10.X` TO `v1.11.0`

### API v2 changes (`/api/v2`)

1. `Sylius\Bundle\ApiBundle\View\CartShippingMethodInterface` and `Sylius\Bundle\ApiBundle\View\CartShippingMethod` have been removed.

1. `Sylius\Bundle\ApiBundle\View\Factory\CartShippingMethodFactoryInterface` and `Sylius\Bundle\ApiBundle\View\Factory\CartShippingMethodFactory` have been removed.

1. The constructor of `Sylius\Bundle\ApiBundle\DataProvider\CartShippingMethodsSubresourceDataProvider` has been changed:

    ```diff
        public function __construct(
            OrderRepositoryInterface $orderRepository,
            ShipmentRepositoryInterface $shipmentRepository,
            ShippingMethodsResolverInterface $shippingMethodsResolver,
    -       ServiceRegistryInterface $calculators,
    -       CartShippingMethodFactoryInterface $cartShippingMethodFactory
        ) {
            ...
        }
    ``` 

1. The response schema for endpoint `GET /api/v2/shop/orders/{tokenValue}/shipments/{shipments}/methods` has been changed from: 

    ```
    ...
    "hydra:member": [
        {
            ...
            "@type": "CartShippingMethod",
            "shippingMethod": {
                ...
                "price": 500
            }
        }
    ]
    ```
    to:
    ```
    ...
    "hydra:member": [
        {
            ...
            "@type": "ShippingMethod",
            ...
            "price": 500
        }
    ]
    ```

1. Constructor of `Core/OrderProcessing/OrderTaxesProcessor.php` has been changed where new service implementing
   `TaxationAddressResolverInterface` will become mandatory from Sylius version 2.0:

    ```diff
        public function __construct(
            ZoneProviderInterface $defaultTaxZoneProvider,
            ZoneMatcherInterface $zoneMatcher,
            PrioritizedServiceRegistryInterface $strategyRegistry,
    +       ?TaxationAddressResolverInterface $taxationAddressResolver = null
        ) {
            ...
        }
    ```

1. Constructor of `ApiBundle/Serializer/ProductVariantNormalizer.php` has been extended with `SectionProviderInterface`
    argument:

    ```diff
        public function __construct(
            ProductVariantPricesCalculatorInterface $priceCalculator,
            ChannelContextInterface $channelContext,
            AvailabilityCheckerInterface $availabilityChecker,
    +       SectionProviderInterface $uriBasedSectionContext
        ) {
            ...
        }
    ```

1. Request body of `POST` `api/v2/shop/addresses` endpoint has been changed:

    ```diff
        {
    -       "customer": "string",
            "firstName": "string",
            "lastName": "string",
            "phoneNumber": "string",
            "company": "string",
            "countryCode": "string",
            "provinceCode": "string",
            "provinceName": "string",
            "street": "string",
            "city": "string",
            "postcode": "string"
        }
    ```
