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
