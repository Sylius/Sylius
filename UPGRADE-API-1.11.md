# UPGRADE FROM `v1.11.11` TO `v1.11.12`

1. So far, on production environment when any non-validation related error occurred, the `FOS\RestBundle\Serializer\Normalizer\FlattenExceptionHandler` was used, even on API Platform endpoints.
   Now, depending on the path of the request, the `FOS\RestBundle\Serializer\Normalizer\FlattenExceptionHandler` or `ApiPlatform\Hydra\Serializer\ErrorNormalizer` is used. If your code
   rely on the previous behavior, you should add the following configuration to your `config/packages/_sylius.yaml` file:
    ```yaml
    sylius_api:
        legacy_error_handling: true
    ```

   Example response before bugfix:
    ```json
    {
        "code": 500,
        "message": "Internal Server Error"
    }
    ```

   Example response after bugfix:
    ```json
    {
        "@context": "/api/v2/contexts/Error",
        "@type": "hydra:Error",
        "hydra:title": "An error occurred",
        "hydra:description": "Internal Server Error"
    }
    ```
   The status code is passed along the response as an HTTP status code, and the `message` value is returned in a `hydra:description` field.

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
   
   To change the prefix you need to set parameter in ``config/packages/_sylius.yaml``:

    ```yaml
    sylius_api:
        product_image_prefix: 'media/image'
    ```

1. The following resources' response format changed:
    * Taxons
    * Products
    * ProductVariants
    * ProductOptions
    * ProductOptionValues
    * ShippingMethods
    * Payments
    * PaymentMethods
    * Orders

   Note that it concerns only `shop` endpoints. Now instead of returning an array of `translations` for a given resource, 
   translation is done automatically based on the current locale. For example changes in request body of `GET` `api/v2/shop/shipping-methods/{code}` endpoint: 
    
    ```diff
         {
             "@context": "string",
             "@id": "string",
             "@type": "string",
             "id": 0,
             "code": "string",
             "position": 0,
     -       "translations": {
     -       "en_US": {
     -          "name": "string",
     -          "description": "string",
     -          "locale": "string"
     -         }
     -       },
             "name": "string"
     +       "description": "string"
         }
     ```

1. The `api/v2/shop/payment/{id}/methods` endpoint has now `shop:payment_method:read` serialization group assigned. 
   Therefore its body will look like this by default:
   
    ```
    {
        "@context": "\/api\/v2\/contexts\/PaymentMethod",
        "@id": "\/api\/v2\/shop\/orders\/nAWw2jewpA\/payments\/@integer@\/methods",
        "@id": "\/api\/v2\/shop\/payments\/@integer@\/methods",
        "@type": "hydra:Collection",
        "hydra:member": [
            {
                "@id": "\/api\/v2\/shop\/payment-methods\/CASH_ON_DELIVERY",
                "@type": "PaymentMethod",
                "id": 1,
                "code": "CASH_ON_DELIVERY",
                "position": 0,
                "name": "Cash on delivery",
                "description": "Description",
                "instructions": null
            }
        ],
        "hydra:totalItems": 1
    }
    ```

1. The method of the `/shop/orders/{tokenValue}/items` endpoint has been changed from `PATCH` to `POST`

1. `Sylius\Bundle\ApiBundle\View\CartShippingMethodInterface` and `Sylius\Bundle\ApiBundle\View\CartShippingMethod` have been removed.

1. `Sylius\Bundle\ApiBundle\Applicator\ShipmentStateMachineTransitionApplicatorInterface` and `Sylius\Bundle\ApiBundle\Applicator\ShipmentStateMachineTransitionApplicator` have been removed.

1. `Sylius\Bundle\ApiBundle\View\Factory\CartShippingMethodFactoryInterface` and `Sylius\Bundle\ApiBundle\View\Factory\CartShippingMethodFactory` have been removed.

1. The `Sylius\Bundle\ApiBundle\DataTransformer\LoggedInShopUserEmailAwareCommandDataTransformer` service has been renamed to `Sylius\Bundle\ApiBundle\DataTransformer\LoggedInCustomerEmailAwareCommandDataTransformer`

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

1. The `Sylius\Bundle\ApiBundle\Command\Cart\PickupCart` command has interface changed from `Sylius\Bundle\ApiBundle\Command\ShopUserIdAwareInterface`
   to `Sylius\Bundle\ApiBundle\Command\CustomerEmailAwareInterface`, therefore following fields and methods have been changed

    ```diff
    -   public $shopUserId;
    +   public ?string $email = null;
   
    -   public function getShopUserId()
    +   public function getEmail(): ?string
   
    -   public function setShopUserId($shopUserId): void
    +   public function setEmail(?string $email): void
    ```

1. The constructor of `Sylius\Bundle\ApiBundle\CommandHandler\Cart\PickupCartHandler` has been changed:

    ```diff
        public function __construct(
            FactoryInterface $cartFactory,
            OrderRepositoryInterface $cartRepository,
            ChannelRepositoryInterface $channelRepository,
            ObjectManager $orderManager,
            RandomnessGeneratorInterface $generator,
    -       UserRepositoryInterface $userRepository
    +       CustomerRepositoryInterface $customerRepository
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

1. Constructor of `Sylius\Bundle\ApiBundle\Command\Account\ResendVerificationEmail` has been removed. Relation to the current 
   customer is set through `setShopUserId()`

1. The `Sylius\Bundle\ApiBundle\Validator\Constraints\ShopUserExists` constraint and the `Sylius\Bundle\ApiBundle\Validator\Constraints\ShopUserExistsValidator`
   have been removed 

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

1. Request body of `POST` `api/v2/shop/account-verification-requests` endpoint has been removed:

    ```diff
        {
    -       "email": "string",
    -       "localeCode": "string"
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
      * '\Sylius\Bundle\ApiBundle\CommandHandler\ChangeShopUserPasswordHandler' => '\Sylius\Bundle\ApiBundle\CommandHandler\Account\ChangeShopUserPasswordHandler'
      * '\Sylius\Bundle\ApiBundle\CommandHandler\PickupCartHandler' => '\Sylius\Bundle\ApiBundle\CommandHandler\Cart\PickupCartHandler'
      * '\Sylius\Bundle\ApiBundle\CommandHandler\RegisterShopUserHandler' => '\Sylius\Bundle\ApiBundle\CommandHandler\Account\RegisterShopUserHandler'
      * '\Sylius\Bundle\ApiBundle\Command\ChangeShopUserPassword' => '\Sylius\Bundle\ApiBundle\Command\Account\ChangeShopUserPassword'
      * '\Sylius\Bundle\ApiBundle\Command\RegisterShopUser' => '\Sylius\Bundle\ApiBundle\Command\Account\RegisterShopUser'
      * '\Sylius\Bundle\ApiBundle\Doctrine\Filters\ExchangeRateFilter' => '\Sylius\Bundle\ApiBundle\Doctrine\Filter\ExchangeRateFilter'
      * '\Sylius\Bundle\ApiBundle\Doctrine\Filters\TranslationOrderNameAndLocaleFilter' => 'Sylius\Bundle\ApiBundle\Doctrine\Filter\TranslationOrderNameAndLocaleFilter'

1. Following validator alias has been changed:
    * `sylius.validator.unique_shop_user_email` => `sylius_validator_unique_shop_user_email`

1. Following services ids has been changed:
     * `sylius.api.upload_avatar_image_action` => `Sylius\Bundle\ApiBundle\Controller\UploadAvatarImageAction`
     * `sylius.api.upload_avatar_image_action` => `Sylius\Bundle\ApiBundle\Controller\UploadAvatarImageAction`
     * `Sylius\Bundle\ApiBundle\Changer\PaymentMethodChanger` => `Sylius\Bundle\ApiBundle\Changer\PaymentMethodChangerInterface`
     * `sylius_api.serializer_context_builder.channel` => `Sylius\Bundle\ApiBundle\SerializerContextBuilder\ChannelContextBuilder`
     * `sylius.api.context.user` => `Sylius\Bundle\ApiBundle\Context\UserContextInterface`
     * `sylius.api.checker.applied_coupon_eligibility_checker` => `Sylius\Bundle\ApiBundle\Checker\AppliedCouponEligibilityCheckerInterface`
     * `Sylius\Bundle\ApiBundle\Applicator\ArchivingShippingMethodApplicator` => `Sylius\Bundle\ApiBundle\Applicator\ArchivingShippingMethodApplicatorInterface`
     * `Sylius\Bundle\ApiBundle\Applicator\OrderStateMachineTransitionApplicator` => `Sylius\Bundle\ApiBundle\Applicator\OrderStateMachineTransitionApplicatorInterface`
     * `Sylius\Bundle\ApiBundle\Applicator\PaymentStateMachineTransitionApplicator` => `Sylius\Bundle\ApiBundle\Applicator\PaymentStateMachineTransitionApplicatorInterface`
     * `Sylius\Bundle\ApiBundle\Applicator\ProductReviewStateMachineTransitionApplicator` => `Sylius\Bundle\ApiBundle\Applicator\ProductReviewStateMachineTransitionApplicatorInterface`
     * `sylius.api.property_info.extractor.empty_list_extractor` => `Sylius\Bundle\ApiBundle\PropertyInfo\Extractor\EmptyPropertyListExtractor`
     * `sylius.api.data_transformer.order_token_value_aware_input_data_transformer` => `Sylius\Bundle\ApiBundle\DataTransformer\OrderTokenValueAwareInputCommandDataTransformer`
     * `sylius.api.data_transformer.shipment_id_aware_input_command` => `Sylius\Bundle\ApiBundle\DataTransformer\ShipmentIdAwareInputCommandDataTransformer`
     * `sylius.api.data_transformer.logged_in_shop_user_id_aware_input_data_transformer` => `Sylius\Bundle\ApiBundle\DataTransformer\LoggedInShopUserIdAwareCommandDataTransformer`
     * `sylius.api.data_transformer.channel_code_aware_input_data_transformer` => `Sylius\Bundle\ApiBundle\DataTransformer\ChannelCodeAwareInputCommandDataTransformer`
     * `sylius.api.data_transformer.logged_in_shop_user_email_aware_command` => `Sylius\Bundle\ApiBundle\DataTransformer\LoggedInCustomerEmailAwareCommandDataTransformer`
     * `sylius.api.data_transformer.locale_code_aware_input_data_transformer` => `Sylius\Bundle\ApiBundle\DataTransformer\LocaleCodeAwareInputCommandDataTransformer`
     * `sylius.api.data_transformer.subresource_id_aware_data_transformer` => `Sylius\Bundle\ApiBundle\DataTransformer\SubresourceIdAwareCommandDataTransformer`
     * `sylius.api.section_resolver.admin_api_uri_based_section_resolver` => `Sylius\Bundle\ApiBundle\SectionResolver\AdminApiUriBasedSectionResolver`
     * `sylius.api.section_resolver.shop_api_uri_based_section_resolver` => `Sylius\Bundle\ApiBundle\SectionResolver\ShopApiUriBasedSectionResolver`
     * `sylius.listener.api_cart_blamer` => `Sylius\Bundle\ApiBundle\EventListener\ApiCartBlamerListener`
     * `sylius.context.cart.token_value_based` => `Sylius\Bundle\ApiBundle\Context\TokenValueBasedCartContext`
     * `sylius.api.data_persister.address` => `Sylius\Bundle\ApiBundle\DataPersister\AddressDataPersister`
     * `sylius.api.data_persister.admin_user` => `Sylius\Bundle\ApiBundle\DataPersister\AdminUserDataPersister`
     * `sylius.api.data_persister.shipping_method` => `Sylius\Bundle\ApiBundle\DataPersister\ShippingMethodDataPersister`
     * `sylius.api.item_data_provider.address` => `Sylius\Bundle\ApiBundle\DataProvider\AddressItemDataProvider`
     * `sylius.api.item_data_provider.order_item` => `Sylius\Bundle\ApiBundle\DataProvider\OrderItemItemDataProvider`
     * `sylius.api.item_data_provider.order_item_unit` => `Sylius\Bundle\ApiBundle\DataProvider\OrderItemUnitItemDataProvider`
     * `sylius.api.item_data_provider.payment` => `Sylius\Bundle\ApiBundle\DataProvider\PaymentItemDataProvider`
     * `sylius.api.item_data_provider.shipment` => `Sylius\Bundle\ApiBundle\DataProvider\ShipmentItemDataProvider`
     * `sylius.api.collection_data_provider.country` => `Sylius\Bundle\ApiBundle\DataProvider\CountryCollectionDataProvider`
     * `sylius.api.collection_data_provider.taxon` => `Sylius\Bundle\ApiBundle\DataProvider\TaxonCollectionDataProvider`
     * `sylius.api.collection_data_provider.locale` => `Sylius\Bundle\ApiBundle\DataProvider\LocaleCollectionDataProvider`
     * `sylius.api.collection_data_provider.product` => `Sylius\Bundle\ApiBundle\DataProvider\ProductItemDataProvider`
     * `sylius.api.collection_data_provider.customer` => `Sylius\Bundle\ApiBundle\DataProvider\CustomerItemDataProvider`
     * `sylius.api.item_data_provider.reset_password_item` => `Sylius\Bundle\ApiBundle\DataProvider\ResetPasswordItemDataProvider`
     * `sylius.api.kerner_request_event_subscriber` => `Sylius\Bundle\ApiBundle\EventSubscriber\KernelRequestEventSubscriber`
     * `sylius.api.product_slug_event_subscriber` => `Sylius\Bundle\ApiBundle\EventSubscriber\ProductSlugEventSubscriber`
     * `sylius.api.product_taxon_filter` => `Sylius\Bundle\ApiBundle\Filter\Doctrine\TaxonFilter`
     * `sylius.api.exchange_rate_filter` => `Sylius\Bundle\ApiBundle\Doctrine\Filter\ExchangeRateFilter`
     * `sylius.api.translation_order_name_and_locale_filter` => `Sylius\Bundle\ApiBundle\Doctrine\Filter\TranslationOrderNameAndLocaleFilter`
     * `sylius.api.product_variant_option_value_filter` => `Sylius\Bundle\ApiBundle\Doctrine\Filter\ProductVariantOptionValueFilter`
     * `sylius.api.get_configuration_action` => `Sylius\Bundle\ApiBundle\Controller\Payment\GetPaymentConfiguration" public="true`
     * `sylius.validator.unique_shop_user_email` => `Sylius\Bundle\ApiBundle\Validator\Constraints\UniqueShopUserEmailValidator`
     * `sylius.api.validator.order_not_empty` => `Sylius\Bundle\ApiBundle\Validator\Constraints\OrderNotEmptyValidator`
     * `sylius.api.validator.order_product_eligibility` => `Sylius\Bundle\ApiBundle\Validator\Constraints\OrderProductEligibilityValidator`
     * `sylius.api.validator.order_item_availability` => `Sylius\Bundle\ApiBundle\Validator\Constraints\OrderItemAvailabilityValidator`
     * `sylius.api.validator.shipping_method_eligibility` => `Sylius\Bundle\ApiBundle\Validator\Constraints\OrderShippingMethodEligibilityValidator`
     * `sylius.api.validator.chosen_shipping_method_eligibility` => `Sylius\Bundle\ApiBundle\Validator\Constraints\ChosenShippingMethodEligibilityValidator`
     * `sylius.api.validator.adding_eligible_product_variant_to_cart` => `Sylius\Bundle\ApiBundle\Validator\Constraints\AddingEligibleProductVariantToCartValidator`
     * `sylius.api.validator.changing_item_quantity_in_cart` => `Sylius\Bundle\ApiBundle\Validator\Constraints\ChangedItemQuantityInCartValidator`
     * `sylius.api.validator.correct_order_address` => `Sylius\Bundle\ApiBundle\Validator\Constraints\CorrectOrderAddressValidator`
     * `sylius.api.validator.payment_method_eligibility` => `Sylius\Bundle\ApiBundle\Validator\Constraints\OrderPaymentMethodEligibilityValidator`
     * `sylius.api.validator.chosen_payment_method_eligibility` => `Sylius\Bundle\ApiBundle\Validator\Constraints\ChosenPaymentMethodEligibilityValidator`
     * `sylius.api.validator.correct_change_shop_user_confirm_password` => `Sylius\Bundle\ApiBundle\Validator\Constraints\CorrectChangeShopUserConfirmPasswordValidator`
     * `sylius.api.validator.confirm_reset_password` => `Sylius\Bundle\ApiBundle\Validator\Constraints\ConfirmResetPasswordValidator`
     * `sylius.api.validator.promotion_coupon_eligibility` => `Sylius\Bundle\ApiBundle\Validator\Constraints\PromotionCouponEligibilityValidator`
     * `sylius.api.validator.shipment_already_shipped` => `Sylius\Bundle\ApiBundle\Validator\Constraints\ShipmentAlreadyShippedValidator`
     * `sylius.api.validator.shop_user_not_verified` => `Sylius\Bundle\ApiBundle\Validator\Constraints\ShopUserNotVerifiedValidator`
     * `sylius.api.validator.account_verification_token_eligibility` => `Sylius\Bundle\ApiBundle\Validator\Constraints\AccountVerificationTokenEligibilityValidator`
     * `sylius.api.validator.unique_reviewer_email` => `Sylius\Bundle\ApiBundle\Validator\Constraints\UniqueReviewerEmailValidator`
     * `sylius.api.collection_data_provider.payment_method` => `Sylius\Bundle\ApiBundle\DataProvider\CartPaymentMethodsSubresourceDataProvider`
     * `sylius.api.collection_data_provider.adjustments_for_order` => `Sylius\Bundle\ApiBundle\DataProvider\OrderAdjustmentsSubresourceDataProvider`
     * `sylius.api.collection_data_provider.adjustments_for_order_item` => `Sylius\Bundle\ApiBundle\DataProvider\OrderItemAdjustmentsSubresourceDataProvider`
     * `sylius.api.item_data_provider.verify_customer_account` => `Sylius\Bundle\ApiBundle\DataProvider\VerifyCustomerAccountItemDataProvider`
     * `sylius_api.serializer_context_builder.locale` => `Sylius\Bundle\ApiBundle\SerializerContextBuilder\LocaleContextBuilder`
     * `sylius_api.serializer_context_builder.http_request_method_type` => `Sylius\Bundle\ApiBundle\SerializerContextBuilder\HttpRequestMethodTypeContextBuilder`
     * `sylius.api.doctrine.query_collection_extension.hide_archived_shipping_method` => `Sylius\Bundle\ApiBundle\Doctrine\QueryCollectionExtension\HideArchivedShippingMethodExtension`
     * `sylius.api.doctrine.query_collection_extension.accepted_product_reviews` => `Sylius\Bundle\ApiBundle\Doctrine\QueryCollectionExtension\AcceptedProductReviewsExtension`
     * `sylius.api.doctrine.query_collection_extension.addresses` => `Sylius\Bundle\ApiBundle\Doctrine\QueryCollectionExtension\AddressesExtension`
     * `sylius.api.doctrine.query_collection_extension.products_by_channel_and_locale_code` => `Sylius\Bundle\ApiBundle\Doctrine\QueryCollectionExtension\ProductsByChannelAndLocaleCodeExtension`
     * `sylius.api.doctrine.query_collection_extension.orders_by_logged_in_user` => `Sylius\Bundle\ApiBundle\Doctrine\QueryCollectionExtension\OrdersByLoggedInUserExtension`
     * `sylius.api.doctrine.query_collection_extension.products_with_enable_flag` => `Sylius\Bundle\ApiBundle\Doctrine\QueryCollectionExtension\ProductsWithEnableFlagExtension`
     * `sylius.api.doctrine.query_item_extension.get_order` => `Sylius\Bundle\ApiBundle\Doctrine\QueryItemExtension\OrderGetMethodItemExtension`
     * `sylius.api.doctrine.query_item_extension.delete_order` => `Sylius\Bundle\ApiBundle\Doctrine\QueryItemExtension\OrderMethodsItemExtension`
     * `sylius.api.collection_data_provider.shipping_methods_available_for_order` => `Sylius\Bundle\ApiBundle\DataProvider\CartShippingMethodsSubresourceDataProvider`
     * `sylius.api.swagger_admin_authentication_documentation_normalizer` => `Sylius\Bundle\ApiBundle\Swagger\AdminAuthenticationTokenDocumentationNormalizer`
     * `sylius.api.swagger_shop_authentication_documentation_normalizer` => `Sylius\Bundle\ApiBundle\Swagger\ShopAuthenticationTokenDocumentationNormalizer`
     * `sylius.api.swagger_product_documentation_normalizer` => `Sylius\Bundle\ApiBundle\Swagger\ProductDocumentationNormalizer`
     * `sylius.api.swagger_product_image_documentation_normalizer` => `Sylius\Bundle\ApiBundle\Swagger\ProductImageDocumentationNormalizer`
     * `sylius.api.swagger_product_variant_documentation_normalizer` => `Sylius\Bundle\ApiBundle\Swagger\ProductVariantDocumentationNormalizer`
     * `sylius.api.swagger_shipping_method_documentation_normalizer` => `Sylius\Bundle\ApiBundle\Swagger\ShippingMethodDocumentationNormalizer`

1. Following Data Providers have been transformed to Doctrine Extensions:
    * `Sylius\Bundle\ApiBundle\DataProvider\AddressItemDataProvider` => `Sylius\Bundle\ApiBundle\Doctrine\QueryItemExtension\AddressItemExtension`
    * `Sylius\Bundle\ApiBundle\DataProvider\CountryCollectionDataProvider` => `Sylius\Bundle\ApiBundle\Doctrine\QueryCollectionExtension\CountryCollectionExtension`
    * `Sylius\Bundle\ApiBundle\DataProvider\CurrencyCollectionDataProvider` => `Sylius\Bundle\ApiBundle\Doctrine\QueryCollectionExtension\CurrencyCollectionExtension`
    * `Sylius\Bundle\ApiBundle\DataProvider\LocaleCollectionDataProvider` => `Sylius\Bundle\ApiBundle\Doctrine\QueryCollectionExtension\LocaleCollectionExtension`
    * `Sylius\Bundle\ApiBundle\DataProvider\TaxonCollectionDataProvider` => `Sylius\Bundle\ApiBundle\Doctrine\QueryCollectionExtension\TaxonCollectionExtension`

1. The following filters have been moved to new namespace:
    *  `Sylius\Bundle\ApiBundle\Doctrine\Filter\ExchangeRateFilter` service has been moved and renamed to `Sylius\Bundle\ApiBundle\Filter\Doctrine\ExchangeRateFilter`
    *  `Sylius\Bundle\ApiBundle\Doctrine\Filter\TranslationOrderNameAndLocaleFilter` service has been moved and renamed to `Sylius\Bundle\ApiBundle\Filter\Doctrine\TranslationOrderNameAndLocaleFilter`
    *  `Sylius\Bundle\ApiBundle\Doctrine\Filter\ProductVariantOptionValueFilter` service has been moved and renamed to `Sylius\Bundle\ApiBundle\Filter\Doctrine\ProductVariantOptionValueFilter`
    *  `Sylius\Bundle\ApiBundle\Doctrine\Filter\ProductPriceOrderFilter` service has been moved and renamed to `Sylius\Bundle\ApiBundle\Filter\Doctrine\ProductPriceOrderFilter`

1. `Sylius\Bundle\ApiBundle\Command\Cart\ApplyCouponToCart` and `Sylius\Bundle\ApiBundle\Command\Checkout\AddressOrder` commands have been replaced with `Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart`.

1. `Sylius\Bundle\ApiBundle\CommandHandler\Cart\ApplyCouponToCartHandler` and `Sylius\Bundle\ApiBundle\CommandHandler\Checkout\AddressOrderHandler` command handlers have been replaced with `Sylius\Bundle\ApiBundle\CommandHandler\Checkout\UpdateCartHandler`.

1. The `sylius.api.filter_archived_shipping_methods` services has been renamed to `sylius.api.archived_shipping_methods_filter` to be coherent with rest of the filters

1. The argument of `Sylius\Bundle\ApiBundle\Applicator\ArchivingShippingMethodApplicatorInterface` service has been changed
   from `sylius.calendar` to `Sylius\Calendar\Provider\DateTimeProviderInterface`.

1. Renamed response body field `originalPrice` to `originalUnitPrice` of the following endpoints:
   - `'POST', '/api/v2/shop/orders/{tokenValue}/items'`
   - `'GET', '/api/v2/shop/orders/{tokenValue}/items'`
   - `'GET', '/api/v2/shop/orders/{tokenValue}'`

1. Added response body field `originalUnitPrice` to the following endpoint:
   - `'GET', '/api/v2/admin/orders/{tokenValue}'`
