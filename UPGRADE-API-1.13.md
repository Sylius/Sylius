# UPGRADE FROM `v1.12.x` TO `v1.13.0`

1. The constructor of 'Sylius\Bundle\ApiBundle\EventSubscriber\TaxonDeletionEventSubscriber' has changed:

````diff
    public function __construct(
        private ChannelRepositoryInterface $channelRepository,
+       private TaxonInPromotionRuleCheckerInterface $taxonInPromotionRuleChecker,
    ) {
    }
````

1. The signature of constructor of 'Sylius\Bundle\ApiBundle\Command\Cart\ChangeItemQuantityInCart' command changed:

````diff
    public function __construct(
-       public int $quantity,
+       public ?int $quantity,
    ) {
    } 
````

1. The constructor signature of 'Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart' changed:

````diff
    public function __construct(
-       public string $productCode,
+       public ?string $productCode,
-       public int $quantity,
+       public ?int $quantity,
    ) {
    }
````

1. The constructor signature of `Sylius\Bundle\ApiBundle\Command\Catalog\AddProductReview` changed:

````diff
    public function __construct(
        public ?string $title,
        public ?int $rating,
        public ?string $comment,
-       public string $productCode,
+       public ?string $productCode,
        public ?string $email = null,
    ) {
    }
````

1. The constructor signature of `Sylius\Bundle\ApiBundle\Command\Account\VerifyCustomerAccount` changed:

````diff
    public function __construct(
-       public $token,
+       public string $token,
+       public ?string $channelCode = null,
+       public ?string $localeCode = null,
    ) {
    }
````

1. The item operation paths for ProductVariantTranslation resource changed:

- `GET /admin/product-variant-translation/{id}` -> `GET /admin/product-variant-translations/{id}`
- `GET /shop/product-variant-translation/{id}` -> `GET /shop/product-variant-translations/{id}`

1. Typo in the constraint validator's alias returned by `Sylius\Bundle\ApiBundle\Validator\Constraints\ChangedItemQuantityInCartValidator::validatedBy` has been fixed.
   Previously it was `sylius_api_validator_changed_item_guantity_in_cart` and now it is `sylius_api_validator_changed_item_quantity_in_cart`.

1. The `ApiPlatform\Core\Bridge\Symfony\Bundle\Action\SwaggerUiAction` controller has been removed.
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
