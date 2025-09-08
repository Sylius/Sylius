# UPGRADE FROM `2.1` TO `2.2`

### Deprecations

1. Not passing an instance of `Symfony\Contracts\EventDispatcher\EventDispatcherInterface` as the last argument to the constructor of `Sylius\Bundle\ShopBundle\Twig\Component\Checkout\Address\FormComponent` is deprecated since Sylius 2.2 and will be required in Sylius 3.0.

   This change enables extending the checkout address form with custom fields via the `sylius.checkout.address_updated` event.

```php
use Symfony\Component\Routing\RouterInterface;

    public function __construct(
        OrderRepositoryInterface $repository,
        FormFactoryInterface $formFactory,
        string $resourceClass,
        string $formClass,
        protected readonly CustomerContextInterface $customerContext,
        protected readonly UserRepositoryInterface $shopUserRepository,
        protected readonly AddressRepositoryInterface $addressRepository,
+       protected readonly ?EventDispatcherInterface $eventDispatcher = null,
    )
```
