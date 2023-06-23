# UPGRADE FROM `v1.12.x` TO `v1.13.0`

1. The item operation paths for ProductVariantTranslation resource changed:

- `GET /admin/product-variant-translation/{id}` -> `GET /admin/product-variant-translations/{id}`
- `GET /shop/product-variant-translation/{id}` -> `GET /shop/product-variant-translations/{id}`

2. Typo in the constraint validator's alias returned by `Sylius\Bundle\ApiBundle\Validator\Constraints\ChangedItemQuantityInCartValidator::validatedBy` has been fixed.
    Previously it was `sylius_api_validator_changed_item_guantity_in_cart` and now it is `sylius_api_validator_changed_item_quantity_in_cart`.
