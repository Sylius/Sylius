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
   
1. The  `GET` `api/v2/shop/orders/{token}/shipments/{id}/methods` and `api/v2/shop/shipments/{id}/methods` endpoints have been removed and changed into collection request with 2 parameters `api/v2/shop/shipping-methods?shipmentId={id}&orderToken={token}`.
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
