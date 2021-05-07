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
   
1. We've removed DoctrineCacheBundle from our required packages while upgrading to the next major version of DoctrineBundle (v2).
   
1. We've upgraded SyliusThemeBundle to the next major version (v2.1).
   
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
