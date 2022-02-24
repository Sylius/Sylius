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
