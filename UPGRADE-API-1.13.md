# UPGRADE FROM `v1.12.x` TO `v1.13.0`

1. The signature of constructor and `createFromData` method of 'Sylius\Bundle\ApiBundle\Command\Cart\ChangeItemQuantityCart' command changed:

````diff
    public function __construct(
-       public int $quantity,
+       public ?int $quantity,
    ) {
    } 
````

````diff
    public static function createFromData(
        string $tokenValue, 
        string $orderItemId, 
-       int $quantity,
+       ?int $quantity,
    ): self
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

1. The item operation paths for ProductVariantTranslation resource changed:

- `GET /admin/product-variant-translation/{id}` -> `GET /admin/product-variant-translations/{id}`
- `GET /shop/product-variant-translation/{id}` -> `GET /shop/product-variant-translations/{id}`

2. Typo in the constraint validator's alias returned by `Sylius\Bundle\ApiBundle\Validator\Constraints\ChangedItemQuantityInCartValidator::validatedBy` has been fixed.
    Previously it was `sylius_api_validator_changed_item_guantity_in_cart` and now it is `sylius_api_validator_changed_item_quantity_in_cart`.
