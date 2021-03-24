# UPGRADE FROM `v1.9.X` TO `v1.10.0`

### New API

1. API CartShippingMethod key `cost` has been changed to `price`.

2. Command's `ApiBundle/Command/RegisterShopUser.php` constructor parameters has changed from 
   `string $firstName, string $lastName, string $email, string $password, ?string $phoneNumber`
    to
   `string $firstName, string $lastName, string $email, string $password, bool $subscribedToNewsletter, ?string $phoneNumber`
    with default `$subscribedToNewsletter` parameter set to `false`.
