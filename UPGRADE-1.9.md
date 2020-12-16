# UPGRADE FROM `v1.8.X` TO `v1.9.0`

1. Add new parameters, new access control configuration and reorder it:

    ```diff
        parameters:
    +       sylius.security.new_api_user_account_route: "%sylius.security.new_api_shop_route%/account"
    +       sylius.security.new_api_user_account_regex: "^%sylius.security.new_api_user_account_route%"

        security:
            access_control:
    +           - { path: "%sylius.security.new_api_user_account_regex%/.*", role: ROLE_USER }
    -           - { path: "%sylius.security.new_api_shop_regex%/.*", role: IS_AUTHENTICATED_ANONYMOUSLY }
                - { path: "%sylius.security.new_api_route%/shop/authentication-token", role: IS_AUTHENTICATED_ANONYMOUSLY }
    +           - { path: "%sylius.security.new_api_shop_regex%/.*", role: IS_AUTHENTICATED_ANONYMOUSLY }
        ```

1. Unified API registration path in shop has been changed from `/new-api/shop/register` to `/new-api/shop/customers/`. 
 
1. Identifier needed to retrieve a product in shop API endpoint (`/new-api/shop/products/{id}`) has been changed 
from `slug` to `code`. 

1. The `CoreBundle/Migrations/Version20201208105207.php` migration was added which extends existing adjustments with additional details(context). Depending on the type of adjustment, additionally defined information are:
 * Taxation details (percentage and relation to tax rate)
 * Shipping details (shipping relation)
 * Taxation for shipping (combined details of percentage and shipping relation)
 
 This data is fetched based on two assumptions:
 * Order level taxes relates to shipping only (default Sylius behaviour)
 * Tax rate name has not change since the time, the first order has been placed
 
 If these are not true, please adjust migration accordingly to your need. To exclude following migration from execution run following code: 
    ```
    bin/console doctrine:migrations:version 'CoreBundle/Migrations/Version20201208105207' --add
    ```

1. The base of `Adjustment` class has changed. If you extend your adjustments already(or have them overridden by default, because of Sylius-Standard usage), you should base your Adjustment class on `AdjustmentFQCN`

    ```diff
    -       use Sylius\Component\Order\Model\Adjustment as BaseAdjustment;
    +       use Sylius\Component\Core\Model\Adjustment as BaseAdjustment;
    ```
