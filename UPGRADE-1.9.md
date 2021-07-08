# UPGRADE FROM `v1.9.5` TO `v1.9.6`

1. API is disabled by default, to enable it you need to set flag to ``true`` in ``app/config/packages/_sylius.yaml``:

    ```yaml
    sylius_api:
        enabled: true
    ```

# UPGRADE FROM `v1.9.3` TO `v1.9.4`

1. `Sylius\Bundle\ApiBundle\DataProvider\OrderCollectionDataProvider` has been removed and the same logic 
is now implemented in `Sylius\Bundle\ApiBundle\Doctrine\QueryCollectionExtension\OrdersByLoggedInUserExtension`

1. The service `Sylius\Bundle\ApiBundle\Serializer\ProductVariantSerializer` has been changed to `Sylius\Bundle\ApiBundle\Serializer\ProductVariantNormalizer`
and its first argument `NormalizerInterface $objectNormalizer` has been removed from constructor.

# UPGRADE FROM `v1.9.2` TO `v1.9.3`

1. The endpoint `GET api/v2/order-items/{id}/adjustments` has been changed to `GET api/v2/admin/order-items/{id}/adjustments`

# UPGRADE FROM `v1.8.X` TO `v1.9.0`

### Package upgrades

1. Upgrade the version of `friendsofsymfony/oauth-server-bundle` by:

    ```bash
    composer require "friendsofsymfony/oauth-server-bundle":">2.0.0-alpha.0 ^2.0@dev"
    ```

1. We've upgraded Sylius' ResourceBundle and GridBundle packages which forced us to upgrade major versions of our dependencies.
   
    Please follow [ResourceBundle's upgrade instructions](https://github.com/Sylius/SyliusResourceBundle/blob/master/UPGRADE.md#from-16x-to-17x).
   
    Apart from that, JMS Serializer major version upgrade requires to replace `array` type to `iterable` when serializing Doctrine Collections.

    Due to FOS Rest Bundle major version upgrade, the JSON error responses might have changed. If your tests stop passing,
    you can bring back old behaviour by overriding `error.json.twig` and `exception.json.twig` templates. You can check
    how we've done that in Sylius by looking into vendor code in `templates/bundles/TwigBundle/Exception/` directory. 

1. We've replaced deprecated Doctrine Persistence API with the new one.
   
    Replace `Doctrine\Common\Persistence` namespace in your codebase to `Doctrine\Persistence`.
   
1. **We've removed DoctrineCacheBundle from our required packages while upgrading to the next major version of DoctrineBundle (v2).**
   
1. **We've upgraded SyliusThemeBundle to the next major version (v2.1).**
   
    Please follow [SyliusThemeBundle's upgrade instructions](https://github.com/Sylius/SyliusThemeBundle/blob/master/UPGRADE.md).
   
1. We've replaced deprecated Symfony Translator API with the new one.
   
    Replace `Symfony\Component\Translation\TranslatorInterface` with `Symfony\Contracts\Translation\TranslatorInterface` in your codebase.

1. `/new-api` prefix has been changed to `/api/v2`. Please adjust your routes accordingly.
   Admin API is hardcoded to `/api/v1` instead of `/api/v{version}`.

1. Add proper redirect to changing password page in your `config/routes/sylius_shop.yaml` file:

    ```diff
    +   # see https://web.dev/change-password-url/
    +   sylius_shop_request_password_reset_token_redirect:
    +       path: /.well-known/change-password
    +       methods: [GET]
    +       controller: Symfony\Bundle\FrameworkBundle\Controller\RedirectController::redirectAction
    +       defaults:
    +           route: sylius_shop_request_password_reset_token
    +           permanent: false
    ```

1. Add new bundles to your list of used bundles in `config/bundles.php` if they are not already there:

    ```diff
    +   BabDev\PagerfantaBundle\BabDevPagerfantaBundle::class => ['all' => true],
    +   SyliusLabs\Polyfill\Symfony\Security\Bundle\SyliusLabsPolyfillSymfonySecurityBundle::class => ['all' => true],
    ```

1. Remove `getContainerLoader` method from `src/Kernel.php` class if you did not customise it.

#### Upgrades Symfony to v5.2

1. Upgrade Symfony dependencies by:

    ```bash
    composer config extra.symfony.require "^5.2"
    composer require --dev "symfony/browser-kit":"^5.2" --no-update --no-scripts
    composer require --dev "symfony/debug-bundle":"^5.2" --no-update --no-scripts
    composer require --dev "symfony/intl":"^5.2" --no-update --no-scripts
    composer require --dev "symfony/web-profiler-bundle":"^5.2" --no-update --no-scripts
    composer update
    ```

1. We've removed the support for Symfony's Templating component (which is removed in Symfony 5). 

    * Remove `templating` from framework's configuration:
        
        ```diff
        # config/packages/framework.yaml
       
        framework:
            # ...
        -    templating: { engines: ["twig"] }
        ```
      
    * Replace any usages of `Symfony\Bundle\FrameworkBundle\Templating\EngineInterface` with `Twig\Environment`.
   
        Inject `twig` service into your controllers instead of `templating` or `templating.engine.twig`.
      
        `$templating->renderResponse(...)` might be replaced with `new Response($twig->render(...))`.

1. Remove Twig route configuration from your `config/routes/dev/twig.yaml`:

    ```diff
    -   _errors:
    -       resource: '@TwigBundle/Resources/config/routing/errors.xml'
    -       prefix: /_error
    ```

### New API

1. Adjust your `config/packages/security.yaml`.

    * Parameters from `config/packages/security.yaml` has been moved to separated bundles. 
    You may delete them if you are using the default values:
      
        ```diff
        - parameters:
        -     sylius.security.admin_regex: "^/%sylius_admin.path_name%"
        -     sylius.security.api_regex: "^/api/v1"
        -     sylius.security.shop_regex: "^/(?!%sylius_admin.path_name%|api/.*|api$|media/.*)[^/]++"
        -     sylius.security.new_api_route: "/api/v2"
        -     sylius.security.new_api_regex: "^%sylius.security.new_api_route%"
        -     sylius.security.new_api_admin_route: "%sylius.security.new_api_route%/admin"
        -     sylius.security.new_api_admin_regex: "^%sylius.security.new_api_admin_route%"
        -     sylius.security.new_api_shop_route: "%sylius.security.new_api_route%/shop"
        -     sylius.security.new_api_shop_regex: "^%sylius.security.new_api_shop_route%"
        ```

    * If you are not using the default values, you may need to add and change parameters:
    
        ```diff
            parameters:
        -       sylius.security.api_regex: "^/api"
        -       sylius.security.shop_regex: "^/(?!%sylius_admin.path_name%|new-api|api/.*|api$|media/.*)[^/]++"
        -       sylius.security.new_api_route: "/new-api"
        +       sylius.security.api_regex: "^/api/v1"
        +       sylius.security.shop_regex: "^/(?!%sylius_admin.path_name%|api/.*|api$|media/.*)[^/]++"
        +       sylius.security.new_api_route: "/api/v2"
        +       sylius.security.new_api_user_account_route: "%sylius.security.new_api_shop_route%/account"
        +       sylius.security.new_api_user_account_regex: "^%sylius.security.new_api_user_account_route%"
        ```
    
    * Add new access control configuration and reorder it:
    
        ```diff
            security:
                access_control:
        +           - { path: "%sylius.security.new_api_admin_regex%/.*", role: ROLE_API_ACCESS }
        -           - { path: "%sylius.security.new_api_route%/admin/authentication-token", role: IS_AUTHENTICATED_ANONYMOUSLY }
        +           - { path: "%sylius.security.new_api_admin_route%/authentication-token", role: IS_AUTHENTICATED_ANONYMOUSLY }
        +           - { path: "%sylius.security.new_api_user_account_regex%/.*", role: ROLE_USER }
        -           - { path: "%sylius.security.new_api_route%/shop/authentication-token", role: IS_AUTHENTICATED_ANONYMOUSLY }
        +           - { path: "%sylius.security.new_api_shop_route%/authentication-token", role: IS_AUTHENTICATED_ANONYMOUSLY }
        -           - { path: "%sylius.security.new_api_admin_regex%/.*", role: ROLE_API_ACCESS }
                    - { path: "%sylius.security.new_api_shop_regex%/.*", role: IS_AUTHENTICATED_ANONYMOUSLY }
        ```

1. Unified API registration path in shop has been changed from `/new-api/shop/register` to `/new-api/shop/customers/`. 
 
1. Identifier needed to retrieve a product in shop API endpoint (`/new-api/shop/products/{id}`) has been changed from `slug` to `code`. 

1. Replace and add new keys in `config/packages/dev/jms_serializer.yaml`:

    ```diff
        jms_serializer:
            visitors:
    -           json:
    +           json_serialization:
                   options:
                       - JSON_PRETTY_PRINT
                       - JSON_UNESCAPED_SLASHES
                       - JSON_PRESERVE_ZERO_FRACTION
    +           json_deserialization:
    +              options:
    +                  - JSON_PRETTY_PRINT
    +                  - JSON_UNESCAPED_SLASHES
    +                  - JSON_PRESERVE_ZERO_FRACTION
    ```

1. Replace and add new keys in `config/packages/prod/jms_serializer.yaml`:

    ```diff
        jms_serializer:
            visitors:
    -           json:
    +           json_serialization:
                   options:
                       - JSON_UNESCAPED_SLASHES
                       - JSON_PRESERVE_ZERO_FRACTION
    +           json_deserialization:
    +              options:
    +                  - JSON_UNESCAPED_SLASHES
    +                  - JSON_PRESERVE_ZERO_FRACTION
    ```
   
1. Replace key in `config/packages/jms_serializer.yaml`:
   
   ```diff
       jms_serializer:
           visitors:
   -           xml:
   +           xml_serialization:
   ```

1. `config/packages/fos_rest.yaml` rules have been changed to:

    ```diff
        rules:
    -       - { path: '^/api/.*', priorities: ['json', 'xml'], fallback_format: json, prefer_extension: true }
    +       - { path: '^/api/v1/.*', priorities: ['json', 'xml'], fallback_format: json, prefer_extension: true }
    ```

1. To have better control over the serialization process, we introduced `shop` and `admin` prefixes 
   to names of serialization groups on `src/Sylius/Bundle/ApiBundle/Resources/config/api_resources/*` and `src/Sylius/Bundle/ApiBundle/Resources/config/serialization/*`.
   Several additional serialization groups have been rephrased, to improve readability and predictability of them.
   If you are using they on your custom entity `api_resource` configuration or serialization groups, you should check 
   if one of these changes may affect on your app. If yes, change all occurs by this pattern:

- created serialization groups for `Locale` resource as: `admin:locale:read` and `admin:locale:create`
- `adjustment:read` changed to: `admin:adjustment:read` and `shop:adjustment:read`
- `admin_user:create` changed to: `admin:admin_user:create`
- `admin_user:read` changed to: `admin:admin_user:read`
- `admin_user:update` changed to: `admin:admin_user:update`
- `avatar_image:read` changed to: `admin:avatar_image:read`
- `cart:add_item` changed to: `shop:cart:add_item`
- `cart:address` changed to: `shop:cart:address`
- `cart:apply_coupon` changed to: `shop:cart:apply_coupon`
- `cart:change_quantity` changed to: `shop:cart:change_quantity`
- `cart:complete` changed to: `shop:cart:complete`
- `cart:remove_item` changed to: `shop:cart:remove_item`
- `cart:select_payment_method` changed to: `shop:cart:select_payment_method`
- `cart:select_shipping_method` changed to: `shop:cart:select_shipping_method`
- `cart:update` changed to: `shop:cart:update`
- `channel:create` changed to: `admin:channel:create`
- `channel:read` changed to: `admin:channel:read`
- `checkout:read` changed to: `shop:cart:read`
- `country:read` changed to: `admin:country:read`
- `currency:read` changed to: `admin:currency:read`
- `customer:password:write` changed to: `shop:customer:password:update`
- `customer:read` changed to: `admin:customer:read` and `shop:customer:read`
- `customer:update` changed to: `shop:customer:update`
- `customer_group:create` changed to: `admin:customer_group:create`
- `customer_group:read` changed to: `admin:customer_group:read`
- `customer_group:update` changed to: `admin:customer_group:update`
- `exchange_rate:create` changed to: `admin:exchange_rate:create`
- `exchange_rate:read` changed to: `admin:exchange_rate:read`
- `exchange_rate:update` changed to: `admin:exchange_rate:update`
- `order:create` changed to: `shop:order:create`
- `order:read` changed to: `admin:order:read`
- `order:update` changed to: `admin:order:update`
- `order_item:read` changed to: `admin:order_item:read` and `shop:order_item:read`
- `order_item_unit:read` changed to: `admin:order_item_unit:read` and `shop:order_item_unit:read`
- `payment:read` changed to: `admin:payment:read` and `shop:payment:read`
- `payment_method:read` changed to: `admin:payment_method:read` and `shop:payment_method:read`
- `product:create` changed to: `admin:product:create`
- `product:read` changed to: `admin:product:read` and `shop:product:read`
- `product:update` changed to: `admin:product:update`
- `province:read` changed to: `admin:province:read`
- `province:update` changed to: `admin:province:update`
- `shipment:read` changed to: `admin:shipment:read` and `shop:shipment:read`
- `shipment:update` changed to: `admin:shipment:update`
- `shipping_category:create` changed to: `admin:shipping_category:create`
- `shipping_category:read` changed to: `admin:shipping_category:read`
- `shipping_category:update` changed to: `admin:shipping_category:update`
- `shipping_method:create` changed to: `admin:shipping_method:create`
- `shipping_method:read` changed to: `admin:shipping_method:read`
- `shipping_method:update` changed to: `admin:shipping_method:update`
- `shop:currencies:read` changed to: `shop:currency:read`
- `shop:customer:write` changed to: `shop:customer:create`
- `shop_billing_data:read` changed to: `admin:shop_billing_data:read`
- `tax_category:read` changed to: `admin:tax_category:read`
- `tax_category:update` changed to: `admin:tax_category:update`
- `tax_category:create` changed to: `admin:tax_category:create`
- `taxon:read` changed to: `admin:taxon:read` and `shop:taxon:read`
- `taxon:update` changed to: `admin:taxon:update`
- `taxon:create` changed to: `admin:taxon:create`
- `zone:read` changed to: `admin:zone:read`
- `zone:update` changed to: `admin:zone:update`
- `zone:create` changed to: `admin:zone:create`
- `zone_member:read` changed to: `admin:zone_member:read`
- removed redundant `zone_member:write`

### Data migrations

1. The `CoreBundle/Migrations/Version20201208105207.php` migration was added which extends existing adjustments with additional details (context). 
   
    Depending on the type of adjustment, additionally defined information are:
    
        * Taxation details (percentage and relation to tax rate)
        * Shipping details (shipping relation)
        * Taxation for shipping (combined details of percentage and shipping relation)
 
    This data is fetched based on two assumptions:
   
        * Order level taxes relates to shipping only (default Sylius behaviour)
        * Tax rate name has not changed since the time, the first order has been placed
 
    If these are not true, please adjust migration accordingly to your need. To exclude following migration from execution run following code: 
    
    ```
    bin/console doctrine:migrations:version 'Sylius\Bundle\CoreBundle\Migrations\Version20201208105207' --add
    ```

1. The base of the `Adjustment` class has changed. If you extend your adjustments already (or have them overridden 
by default, because of Sylius-Standard usage), you should base your Adjustment class 
on `Sylius\Component\Core\Model\Adjustment` instead of `Sylius\Component\Order\Model\Adjustment`.

    ```diff
    -       use Sylius\Component\Order\Model\Adjustment as BaseAdjustment;
    +       use Sylius\Component\Core\Model\Adjustment as BaseAdjustment;
    ```
