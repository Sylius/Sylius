# UPGRADE FROM `2.1` TO `2.2`

### Deprecations

1. Not injecting a `tagged_iterator with the tag `sylius_shop.modifier.address_form_values` into the constructor of `Sylius\Bundle\ShopBundle\Twig\Component\Checkout\Address\FormComponent` is deprecated since Sylius 2.2 and will be required in Sylius 3.0.

   This change enables extending the checkout address form with custom fields or logic by registering services tagged with `sylius_shop.modifier.address_form_values`, which implement the `AddressFormValuesModifierInterface`.

```php
    public function __construct(
        OrderRepositoryInterface $repository,
        FormFactoryInterface $formFactory,
        string $resourceClass,
        string $formClass,
        protected readonly CustomerContextInterface $customerContext,
        protected readonly UserRepositoryInterface $shopUserRepository,
        protected readonly AddressRepositoryInterface $addressRepository,
+       private readonly ?iterable $addressFormValuesModifiers = null,
    )
```
