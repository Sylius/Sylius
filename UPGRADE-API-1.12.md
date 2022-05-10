# UPGRADE FROM `v1.11.X` TO `v1.12.0`

1. The `Sylius\Bundle\ApiBundle\DataProvider\CartShippingMethodsSubresourceDataProvider` has been removed and replaced by `Sylius\Bundle\ApiBundle\DataProvider\ShippingMethodsCollectionDataProvider`.

1. The `Sylius\Bundle\ApiBundle\Serializer\ShippingMethodNormalizer` logic and constructor has been changed due to refactor above.

    ```diff
        public function __construct(
            private OrderRepositoryInterface $orderRepository,
            private ShipmentRepositoryInterface $shipmentRepository,
            private ServiceRegistryInterface $shippingCalculators,
    +       private RequestStack $requestStack,
    +       private ChannelContextInterface $channelContext
        ) {
            ...
        }
    ```

1. The  `GET` `api/v2/shop/orders/{token}/shipments/{id}/methods` and `api/v2/shop/shipments/{id}/methods` endpoints have been removed and changed into collection request with 2 parameters `api/v2/shop/shipping-methods?shipmentId={id}&tokenValue={token}`.
Now when we do not provide parameters in response it returns all available `shippingMethods` in channel.
Wrong parameters otherwise cause empty array `[]` in response and correct parameters return `shippingMethods` available for your `shipment`.     
Here is how the response looks like:
   
   ```
      {
        "@context": "/api/v2/contexts/ShippingMethod",
        "@id": "/api/v2/shop/shipping-methods",
        "@type": "hydra:Collection",
        "hydra:member": [
          {
            "@id": "/api/v2/shop/shipping-methods/ups",
            "@type": "ShippingMethod",
            "id": 1,
            "code": "ups",
            "position": 0,
            "name": "UPS",
            "description": "Quasi perferendis debitis officiis ut inventore exercitationem."
          }
        ],
        "hydra:totalItems": 1,
        "hydra:search": {
          "@type": "hydra:IriTemplate",
          "hydra:template": "/api/v2/shop/shipping-methods{?shipmentId,tokenValue}",
          "hydra:variableRepresentation": "BasicRepresentation",
          "hydra:mapping": [
            {
              "@type": "IriTemplateMapping",
              "variable": "shipmentId",
              "property": null,
              "required": false
            },
            {
              "@type": "IriTemplateMapping",
              "variable": "tokenValue",
              "property": null,
              "required": false
            }
          ]
        }
      }
   ```

1. Service `src/Sylius/Bundle/ApiBundle/DataProvider/CartPaymentMethodsSubresourceDataProvider.php` has been removed and logic was replaced by `src/Sylius/Bundle/ApiBundle/DataProvider/PaymentMethodsCollectionDataProvider.php`

1. Endpoints `/shop/orders/{tokenValue}/payments/{payments}/methods`, `/shop/payments/{id}/methods` has been removed and replaced by `/shop/payment-methods` with filter `paymentId` and `tokenValue`
   `/shop/payment-methods` returns all enable payment methods if filters are not set, payment methods related to payment if filters are filled or empty response if filters ale filled with invalid data.
1. Service `Sylius\Bundle\ApiBundle\DataProvider/CartPaymentMethodsSubresourceDataProvider` has been removed and logic was replaced by `Sylius\Bundle\ApiBundle\DataProvider\PaymentMethodsCollectionDataProvider`

1. The  `GET` `api/v2/shop/orders/{tokenValue}/payments/{payments}/methods` and `api/v2/shop/payments/{id}/methods` endpoints have been removed and changed into collection request with 2 parameters `api/v2/shop/payment-methods?paymentId={id}&tokenValue={token}`.
   Now when we do not provide parameters in response it returns all available `paymentMethods` in channel.
   Wrong parameters otherwise cause empty array `[]` in response and correct parameters return `paymentMethods` available for your `payment`.

1. The 2nd parameter `MetadataInterface` has been removed from `src/Sylius/Bundle/ApiBundle/CommandHandler/Account/ResetPasswordHandler` and replaced by `Sylius\Component\User\Security\PasswordUpdaterInterface` (previously 3rd parameter). From now on a token TTL value must be used instead as the 3rd parameter.

1. Constructor of `Sylius\Bundle\ApiBundle\CommandHandler\Account\RequestResetPasswordTokenHandler` has been extended with `Sylius\Calendar\Provider\DateTimeProviderInterface` argument:

    ```diff
        public function __construct(
            private UserRepositoryInterface $userRepository,
            private MessageBusInterface $commandBus,
    -       private GeneratorInterface $generator
    +       private GeneratorInterface $generator,
    +       private DateTimeProviderInterface $calendar
        ) {
        }
    ```

1. Constructor of `\Sylius\Bundle\ApiBundle\CommandHandler\Account\VerifyCustomerAccountHandler` has been extended with `Sylius\Calendar\Provider\DateTimeProviderInterface` argument:

    ```diff
    -   public function __construct(private RepositoryInterface $shopUserRepository)
    -   {
    +   public function __construct(
    +       private RepositoryInterface $shopUserRepository,
    +       private DateTimeProviderInterface $calendar
    +   ) {
        }
    ```

1. The 2nd parameter `localeCode` has been removed from `src/Sylius/Bundle/ApiBundle/Command/Cart/PickupCart.php` and now is set automatically by `src/Sylius/Bundle/ApiBundle/DataTransformer/LocaleCodeAwareInputCommandDataTransformer.php`.

1. The responses of endpoints `/api/v2/admin/products` and `/api/v2/admin/products/{code}` have been changed in such a way that the field `defaultVariant` has been removed.
