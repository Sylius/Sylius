# UPGRADE FROM `v1.13.6` TO `v1.13.7`

1. The default order attribute has been added to the endpoint GET `/admin/customers` endpoint

    ```diff
    +   <attribute name="order">
    +       <attribute name="createdAt">DESC</attribute>
    +   </attribute>
    ```

# UPGRADE FROM `v1.13.3` TO `v1.13.4`

1. The `api/v2/shop/adjustments/{id}` endpoint has been disabled.

# UPGRADE FROM `v1.12.x` TO `v1.13.0`

ApiBundle is no longer experimental and is now following the same support policy as the rest of Sylius.

### Classes and services changes

1. The following Command constructor signatures have changed:

   `Sylius\Bundle\ApiBundle\Command\Account\ChangeShopUserPassword`:
    ```diff
        public function __construct(
    -       public ?string $newPassword, 
    +       public string $newPassword, 
    -       public ?string $confirmNewPassword,
    +       public string $confirmNewPassword,
    -       public ?string $currentPassword,
    +       public string $currentPassword,
        )
    ```

   `Sylius\Bundle\ApiBundle\Command\Catalog\AddProductReview`:
    ```diff
        public function __construct(
    -       public ?string $title,
    +       public string $title,
    -       public ?int $rating,
    +       public int $rating,
    -       public ?string $comment,
    +       public string $comment,
            public string $productCode,
            public ?string $email = null,
        )
    ```

   `Sylius\Bundle\ApiBundle\Command\Account\ResetPassword`
    ```diff
    public function __construct(
    -      public string $resetPasswordToken,
    +      public string $token,
           public ?string $newPassword = null,
           public ?string $confirmNewPassword = null,
    )
    ```

    `Sylius\Bundle\ApiBundle\Command\Account\VerifyCustomerAccount`:
    ```diff
        public function __construct(
    -       public $token,
    +       public string $token,
    +       public ?string $channelCode = null,
    +       public ?string $localeCode = null,
        )

1. The following Command Handlers constructor signatures have changed:

   `Sylius\Bundle\ApiBundle\CommandHandler\Account\SendAccountRegistrationEmailHandler`:
    ```diff
    use Sylius\Bundle\CoreBundle\Mailer\AccountRegistrationEmailManagerInterface;
    use Sylius\Component\Mailer\Sender\SenderInterface;
   
        public function __construct(
            private UserRepositoryInterface $shopUserRepository,
            private ChannelRepositoryInterface $channelRepository,
    -       private SenderInterface $emailSender,
    +       private AccountRegistrationEmailManagerInterface $accountRegistrationEmailManager,
        )
    ```

   `Sylius\Bundle\ApiBundle\CommandHandler\Account\SendAccountVerificationEmailHandler`:
    ```diff
    use Sylius\Bundle\CoreBundle\Mailer\AccountVerificationEmailManagerInterface;
    use Sylius\Component\Mailer\Sender\SenderInterface;
   
        public function __construct(
            private UserRepositoryInterface $shopUserRepository,
            private ChannelRepositoryInterface $channelRepository,
    -       private SenderInterface $emailSender,
    +       private AccountVerificationEmailManagerInterface $accountVerificationEmailManager,
        )
    ```

   `Sylius\Bundle\ApiBundle\CommandHandler\Account\SendResetPasswordEmailHandler`:
    ```diff
    use Sylius\Bundle\CoreBundle\Mailer\ResetPasswordEmailManagerInterface;
    use Sylius\Component\Mailer\Sender\SenderInterface;
   
        public function __construct(
    -       private SenderInterface $emailSender,
            private UserRepositoryInterface $shopUserRepository,
            private ChannelRepositoryInterface $channelRepository,
    +       private ResetPasswordEmailManagerInterface $resetPasswordEmailManager,
        )
    ```

   `Sylius\Bundle\ApiBundle\CommandHandler\Checkout\SendOrderConfirmationHandler`:
    ```diff
    use Sylius\Bundle\CoreBundle\Mailer\OrderEmailManagerInterface;
    use Sylius\Component\Mailer\Sender\SenderInterface;
   
        public function __construct(
    -       private SenderInterface $emailSender,
            private OrderRepositoryInterface $orderRepository,
    +       private OrderEmailManagerInterface $orderEmailManager,
        )
    ```

   `Sylius\Bundle\ApiBundle\CommandHandler\Checkout\SendShipmentConfirmationEmailHandler`:
    ```diff
    use Sylius\Bundle\CoreBundle\Mailer\ShipmentEmailManagerInterface;
    use Sylius\Component\Mailer\Sender\SenderInterface;

        public function __construct(
    -       private SenderInterface $emailSender,
            private ShipmentRepositoryInterface $shipmentRepository,
    +       private ShipmentEmailManagerInterface $shipmentEmailManager,
        )
    ```

   `Sylius\Bundle\ApiBundle\CommandHandler\SendContactRequestHandler`:
    ```diff
    use Sylius\Bundle\CoreBundle\Mailer\ContactEmailManagerInterface;
    use Sylius\Component\Mailer\Sender\SenderInterface;

        public function __construct(
    -       private SenderInterface $emailSender,
            private ChannelRepositoryInterface $channelRepository,
    +       private ContactEmailManagerInterface $contactEmailManager,
        )
    ```

1. The constructor signature of `Sylius\Bundle\ApiBundle\Serializer\ChannelDenormalizer` has been changed:

    ```diff
    use Sylius\Component\Resource\Factory\FactoryInterface;
    
        public function __construct(
            private FactoryInterface $channelPriceHistoryConfigFactory,
    +       private FactoryInterface $shopBillingDataFactory
        )
    ```

1. The constructor signature of `Sylius\Bundle\ApiBundle\EventSubscriber\TaxonDeletionEventSubscriber` has been changed:

    ```diff
    use Sylius\Component\Core\Promotion\Checker\TaxonInPromotionRuleCheckerInterface;
   
        public function __construct(
            private ChannelRepositoryInterface $channelRepository,
    +       private TaxonInPromotionRuleCheckerInterface $taxonInPromotionRuleChecker,
        )
    ```

1. The `Sylius\Bundle\ApiBundle\ApiPlatform\Bridge\Symfony\Bundle\Action\SwaggerUiAction` controller has been removed.
   Therefore, the `api_platform.swagger.action.ui` service ID points to the API Platform's `SwaggerUiAction` controller.

1. The following services have been removed:
    * `Sylius\Bundle\ApiBundle\Swagger\AdminAuthenticationTokenDocumentationNormalizer`
    * `Sylius\Bundle\ApiBundle\Swagger\ShopAuthenticationTokenDocumentationNormalizer`
    * `Sylius\Bundle\ApiBundle\Swagger\ProductDocumentationNormalizer`
    * `Sylius\Bundle\ApiBundle\Swagger\ProductImageDocumentationNormalizer`
    * `Sylius\Bundle\ApiBundle\Swagger\ProductSlugDocumentationNormalizer`
    * `Sylius\Bundle\ApiBundle\Swagger\ProductVariantDocumentationNormalizer`
    * `Sylius\Bundle\ApiBundle\Swagger\ShippingMethodDocumentationNormalizer`
    * `Sylius\Bundle\ApiBundle\Swagger\PathHiderDocumentationNormalizer`
    * `Sylius\Bundle\ApiBundle\Swagger\AcceptLanguageHeaderDocumentationNormalizer`

   Responsibility of these services has been moved to the corresponding services tagged with `sylius.open_api.modifier`:
    * `Sylius\Bundle\ApiBundle\OpenApi\Documentation\AdminAuthenticationTokenDocumentationModifier`
    * `Sylius\Bundle\ApiBundle\OpenApi\Documentation\ShopAuthenticationTokenDocumentationModifier`
    * `Sylius\Bundle\ApiBundle\OpenApi\Documentation\ProductDocumentationModifier`
    * `Sylius\Bundle\ApiBundle\OpenApi\Documentation\ProductImageDocumentationModifier`
    * `Sylius\Bundle\ApiBundle\OpenApi\Documentation\ProductSlugDocumentationModifier`
    * `Sylius\Bundle\ApiBundle\OpenApi\Documentation\ProductVariantDocumentationModifier`
    * `Sylius\Bundle\ApiBundle\OpenApi\Documentation\ShippingMethodDocumentationModifier`
    * `Sylius\Bundle\ApiBundle\OpenApi\Documentation\PathHiderDocumentationModifier`
    * `Sylius\Bundle\ApiBundle\OpenApi\Documentation\AcceptLanguageHeaderDocumentationModifier`

1. All usages of `ApiPlatform\Core\Api\IriConverterInterface` have been switched to its non-deprecated
   counterpart `ApiPlatform\Api\IriConverterInterface`.
   Due to that, the constructor and usage in the following classes have been changed accordingly:
    * `Sylius\Bundle\ApiBundle\Controller\GetProductBySlugAction`
    * `Sylius\Bundle\ApiBundle\Controller\UploadAvatarImageAction`
    * `Sylius\Bundle\ApiBundle\EventListener\AdminAuthenticationSuccessListener`
    * `Sylius\Bundle\ApiBundle\EventListener\AuthenticationSuccessListener`
    * `Sylius\Bundle\ApiBundle\Filter\Doctrine\CatalogPromotionChannelFilter`
    * `Sylius\Bundle\ApiBundle\Filter\Doctrine\ProductVariantCatalogPromotionFilter`
    * `Sylius\Bundle\ApiBundle\Filter\Doctrine\ProductVariantOptionValueFilter`
    * `Sylius\Bundle\ApiBundle\Filter\Doctrine\ProductVariantOptionValueFilter`
    * `Sylius\Bundle\ApiBundle\Filter\Doctrine\TaxonFilter`
    * `Sylius\Bundle\ApiBundle\Serializer\ChannelPriceHistoryConfigDenormalizer`
    * `Sylius\Bundle\ApiBundle\Serializer\ProductNormalizer`
    * `Sylius\Bundle\ApiBundle\Serializer\ProductVariantNormalizer`
    * `Sylius\Bundle\ApiBundle\Serializer\ZoneDenormalizer`

1. The `Sylius\Bundle\ApiBundle\Filter\Doctrine\CatalogPromotionChannelFilter` service and class has been renamed
   to `Sylius\Bundle\ApiBundle\Filter\Doctrine\ChannelsAwareChannelFilter`.

### Resources changes

1. The following endpoints short names have been changed:

    * `AdminResetPasswordRequest` -> `Administrator`
    * `AccountResetPasswordRequest` -> `Customer`
    * `ShopUserToken` -> `Customer` and `Security`
    * `AdminUserToken` -> `Administrator` and `Security`
    * `ContactRequest` -> `Contact`
    * `VerifyCustomerAccount` -> `Customer`

1. The item operation path for ProductVariantTranslation resource has been changed:
    - `GET` `/admin/product-variant-translation/{id}` -> `GET` `/admin/product-variant-translations/{id}`

1. The following shop endpoints for getting the translation resources have been removed:
    * `GET` `/shop/taxon-translations/{id}`
    * `GET` `/shop/product-translations/{id}`
    * `GET` `/shop/product-variant-translations/{id}`
    * `GET` `/shop/shipping-method-translations/{id}`

   The fields those endpoint were exposing are available on their respective translation subject resources.

1. The following endpoints regarding authentication tokens have been changed:
    * `POST` `/admin/authentication-tokens` -> `POST` `/admin/administrators/token`
    * `POST` `/shop/authentication-tokens` -> `POST` `/shop/customers/token`

1. The following endpoints regarding password reset have been changed:
    * `POST` `/admin/reset-password-requests` -> `POST` `/admin/administrators/reset-password`
    * `PATCH` `/admin/reset-password-requests/{resetPasswordToken}` -> `PATCH` `/admin/administrators/reset-password/{token}`
    * `POST` `/shop/reset-password-requests` -> `POST` `/shop/customers/reset-password`
    * `PATCH` `/shop/reset-password-requests/{resetPasswordToken}` -> `PATCH` `/shop/customers/reset-password/{token}`

1. The following endpoints regarding customer account verification have been changed:
    * `POST` `/shop/account-verification-requests` -> `POST` `/shop/customers/verify`
    * `PATCH` `/shop/account-verification-requests/{token}` -> `PATCH` `/shop/customers/verify/{token}`

1. The following endpoints regarding contact requests have been changed:
    * `POST` `/shop/contact-requests` -> `POST` `/shop/contact`

1. Update in Translations Handling

   The process for creating or updating translations via the API has been refined. Now, the locale for each translation
   is determined directly from its key, making the explicit transmission of the `locale` field redundant. Although the
   API continues to support the explicit sending of the `locale` field but in cases of a mismatch between the key and an
   explicitly sent locale, the API will respond with
   the `Sylius\Bundle\ApiBundle\Exception\TranslationLocaleMismatchException`.

1. Product and taxon editing at `/admin/product-taxons/{id}` operation has been disabled to improve data integrity. To
   modify a product-taxon relationship remove the existing one and create a new one.

1. The keys for adjustment endpoints' responses have been changed:

    ```diff
    {
      ...
    - "order_item": "string",
    + "orderItem": "string",
    - "order_item_unit": "string",
    + "orderItemUnit": "string",
      ...
    }
    ```

1. The `customer` key has been removed from admin address endpoints' responses.

1. The `status` key has been removed from shop product review endpoints' responses.

1. The `author` key has been changed in shop product review endpoints' responses:

    ```diff
    {
      ...
    - "author": "string(IRI)",
    + "author": {
    +   "@id": "string(IRI)",
    +   "@type": "string",
    +   "firstName": "string",
    + },
    }
    ```

1. Resolving the `path` property of every resource implementing `Sylius\Component\Core\Model\ImageInterface` has been changed to always rely on filters.
   The default filter has been set to `sylius_original`, but can be changed via configuration:

    ```yaml
    sylius_api:
        default_image_filter: custom_filter
    ```

   Due to this change the previously used configuration node `product_image_prefix` and its corresponding parameter `sylius_api.product_image_prefix` have been removed.

### Miscellaneous

1. All the `:read` serialization groups are now split to `index` and `show`.
   The `:read` serialization groups are now considered deprecated and will no longer be used in the future.
   There is a BC layer that will allow you to use the `:read` serialization
   group `Sylius\Bundle\ApiBundle\SerializerContextBuilder\ReadOperationContextBuilder` by adding the `read`
   serialization group to your context.
   To configure skipping adding the index and show or read serialization groups to the context, add the following
   configuration to your `config/packages/_sylius.yaml` file:

    ```yaml
    sylius_api:
        serialization_groups:
            skip_adding_index_and_show_groups: true
            skip_adding_read_group: true
    ```

1. All Sylius serialization groups have been prefixed with `sylius:`.
   If you extend any Sylius resources, you should update your serialization groups to use the new prefix.
   Non-prefix serialization groups are considered deprecated and will be removed in Sylius 2.0.

1. Typo in the constraint validator's alias returned
   by `Sylius\Bundle\ApiBundle\Validator\Constraints\ChangedItemQuantityInCart::validatedBy` has been fixed.
   Previously it was `sylius_api_validator_changed_item_guantity_in_cart` and now it
   is `sylius_api_validator_changed_item_quantity_in_cart`.

1. The `sylius.api.product_taxon_filter` filter has been removed and its functionality has been superseded by
   the `sylius.api.search_filter.taxon.code` filter. The usage stays the same.

1. Due to changes with images' `path` property resolving the following services have been changed to make their context broader:
    * `Sylius\Bundle\ApiBundle\Serializer\ProductImageNormalizer` -> `Sylius\Bundle\ApiBundle\Serializer\ImageNormalizer`
    * `Sylius\Bundle\ApiBundle\OpenApi\Documentation\ProductImageDocumentationModifier` -> `Sylius\Bundle\ApiBundle\OpenApi\Documentation\ImageDocumentationModifier`
    * `Sylius\Bundle\ApiBundle\Provider\ProductImageFilterProviderInterface` -> `Sylius\Bundle\ApiBundle\Provider\ImageFiltersProviderInterface`
    * `Sylius\Bundle\ApiBundle\Provider\LiipProductImageFilterProvider` -> `Sylius\Bundle\ApiBundle\Provider\LiipImageFiltersProvider`
