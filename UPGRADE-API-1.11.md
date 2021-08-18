# UPGRADE FROM `v1.10.X` TO `v1.11.0`

1. The product images should have a proper prefix (`/media/image/`) added to the path, so the images could be resolved. 
   This is now done out of the box and response of `Product Image` resource is now:
   
    ```diff
        {
            "@context": "/api/v2/contexts/ProductImage",
            "@id": "/api/v2/shop/product-images/123",
            "@type": "ProductImage",
            "id": "123",
            "type": "thumbnail",
    -       "path": "uo/product.jpg",
    +       "path": "/media/image/uo/product.jpg"
        } 
   ```
   
   To change the prefix you need to set parameter in ``app/config/packages/_sylius.yaml``:

    ```yaml
    sylius_api:
        product_image_prefix: 'media/image'
    ```

1. `Sylius\Bundle\ApiBundle\View\CartShippingMethodInterface` and `Sylius\Bundle\ApiBundle\View\CartShippingMethod` have been removed.

1. `Sylius\Bundle\ApiBundle\Applicator\ShipmentStateMachineTransitionApplicatorInterface` and `Sylius\Bundle\ApiBundle\Applicator\ShipmentStateMachineTransitionApplicator` have been removed.

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

1. The constructor of `Sylius\Bundle\ApiBundle\CommandHandler\Checkout\AddressOrderHandler` has been changed:

    ```diff
        public function __construct(
            OrderRepositoryInterface $orderRepository,
    -       CustomerRepositoryInterface $customerRepository,
    -       FactoryInterface $customerFactory,
            ObjectManager $manager,
            StateMachineFactoryInterface $stateMachineFactory,
            AddressMapperInterface $addressMapper
            AddressMapperInterface $addressMapper,
    +       CustomerProviderInterface $customerProvider
        ) {
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

1. The service `Sylius\Bundle\ApiBundle\Converter\ItemIriToIdentifierConverter` has changed its name to `Sylius\Bundle\ApiBundle\Converter\IriToIdentifierConverter`
     and its service definition from `Sylius\Bundle\ApiBundle\Converter\ItemIriToIdentifierConverter` to `Sylius\Bundle\ApiBundle\Converter\IriToIdentifierConverterInterface`

1. The service `Sylius\Bundle\ApiBundle\Serializer\CommandFieldItemIriToIdentifierDenormalizer` has changed its name to `Sylius\Bundle\ApiBundle\Serializer\CommandArgumentsDenormalizer`
     and also its service definition.

1. Following namespaces has been changed:
     * '\Sylius\Bundle\ApiBundle\CommandHandler\AddProductReviewHandler' => '\Sylius\Bundle\ApiBundle\CommandHandler\Catalog\AddProductReviewHandler'
     * '\Sylius\Bundle\ApiBundle\CommandHandler\BlameCartHandler' => '\Sylius\Bundle\ApiBundle\CommandHandler\Cart\BlameCartHandler'
     * '\Sylius\Bundle\ApiBundle\CommandHandler\RequestResetPasswordTokenHandler' => '\Sylius\Bundle\ApiBundle\CommandHandler\Account\RequestResetPasswordTokenHandler'
     * '\Sylius\Bundle\ApiBundle\CommandHandler\ResendVerificationEmailHandler' => '\Sylius\Bundle\ApiBundle\CommandHandler\Account\ResendVerificationEmailHandler'
     * '\Sylius\Bundle\ApiBundle\CommandHandler\ResetPasswordHandler' => '\Sylius\Bundle\ApiBundle\CommandHandler\Account\ResetPasswordHandler'
     * '\Sylius\Bundle\ApiBundle\CommandHandler\SendAccountRegistrationEmailHandler' => '\Sylius\Bundle\ApiBundle\CommandHandler\Account\SendAccountRegistrationEmailHandler'
     * '\Sylius\Bundle\ApiBundle\CommandHandler\SendAccountVerificationEmailHandler' => '\Sylius\Bundle\ApiBundle\CommandHandler\Account\SendAccountVerificationEmailHandler'
     * '\Sylius\Bundle\ApiBundle\CommandHandler\SendOrderConfirmationHandler' => '\Sylius\Bundle\ApiBundle\CommandHandler\Checkout\SendOrderConfirmationHandler'
     * '\Sylius\Bundle\ApiBundle\CommandHandler\SendResetPasswordEmailHandler' => '\Sylius\Bundle\ApiBundle\CommandHandler\Account\SendResetPasswordEmailHandler'
     * '\Sylius\Bundle\ApiBundle\CommandHandler\SendShipmentConfirmationEmailHandler' => '\Sylius\Bundle\ApiBundle\CommandHandler\Checkout\SendShipmentConfirmationEmailHandler'
     * '\Sylius\Bundle\ApiBundle\CommandHandler\VerifyCustomerAccountHandler' => '\Sylius\Bundle\ApiBundle\CommandHandler\Account\VerifyCustomerAccountHandler'
     * '\Sylius\Bundle\ApiBundle\Command\AddProductReview' => '\Sylius\Bundle\ApiBundle\Command\Catalog\AddProductReview'
     * '\Sylius\Bundle\ApiBundle\Command\BlameCart' => '\Sylius\Bundle\ApiBundle\Command\Cart\BlameCart'
     * '\Sylius\Bundle\ApiBundle\Command\RequestResetPasswordToken' => '\Sylius\Bundle\ApiBundle\Command\Account\RequestResetPasswordToken'
     * '\Sylius\Bundle\ApiBundle\Command\ResendVerificationEmail' => '\Sylius\Bundle\ApiBundle\Command\Account\ResendVerificationEmail'
     * '\Sylius\Bundle\ApiBundle\Command\ResetPassword' => '\Sylius\Bundle\ApiBundle\Command\Account\ResetPassword'
     * '\Sylius\Bundle\ApiBundle\Command\SendAccountRegistrationEmail' => '\Sylius\Bundle\ApiBundle\Command\Account\SendAccountRegistrationEmail'
     * '\Sylius\Bundle\ApiBundle\Command\SendAccountVerificationEmail' => '\Sylius\Bundle\ApiBundle\Command\Account\SendAccountVerificationEmail'
     * '\Sylius\Bundle\ApiBundle\Command\SendOrderConfirmation' => '\Sylius\Bundle\ApiBundle\Command\Checkout\SendOrderConfirmation'
     * '\Sylius\Bundle\ApiBundle\Command\SendResetPasswordEmail' => '\Sylius\Bundle\ApiBundle\Command\Account\SendResetPasswordEmail'
     * '\Sylius\Bundle\ApiBundle\Command\SendShipmentConfirmationEmail' => '\Sylius\Bundle\ApiBundle\Command\Checkout\SendShipmentConfirmationEmail'
     * '\Sylius\Bundle\ApiBundle\Command\VerifyCustomerAccount' => '\Sylius\Bundle\ApiBundle\Command\Account\VerifyCustomerAccount'
     * `\Sylius\Bundle\ApiBundle\CommandHandler\ChangeShopUserPasswordHandler` => `\Sylius\Bundle\ApiBundle\CommandHandler\Account\ChangeShopUserPasswordHandler`
     * `\Sylius\Bundle\ApiBundle\CommandHandler\PickupCartHandler` => `\Sylius\Bundle\ApiBundle\CommandHandler\Cart\PickupCartHandler`
     * `\Sylius\Bundle\ApiBundle\CommandHandler\RegisterShopUserHandler` => `\Sylius\Bundle\ApiBundle\CommandHandler\Account\RegisterShopUserHandler`
     * `\Sylius\Bundle\ApiBundle\Command\ChangeShopUserPassword` => `\Sylius\Bundle\ApiBundle\Command\Account\ChangeShopUserPassword`
     * `\Sylius\Bundle\ApiBundle\Command\RegisterShopUser` => `\Sylius\Bundle\ApiBundle\Command\Account\RegisterShopUser`
     * `\Sylius\Bundle\ApiBundle\Doctrine\Filters\ExchangeRateFilter` => `\Sylius\Bundle\ApiBundle\Doctrine\Filter\ExchangeRateFilter`
     * `\Sylius\Bundle\ApiBundle\Doctrine\Filters\TranslationOrderNameAndLocaleFilter` => `Sylius\Bundle\ApiBundle\Doctrine\Filter\TranslationOrderNameAndLocaleFilter`
