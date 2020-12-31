# UPGRADE FROM `v1.8.X` TO `v1.9.0`

### New API

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
 
1. Identifier needed to retrieve a product in shop API endpoint (`/new-api/shop/products/{id}`) has been changed from `slug` to `code`. 

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
    bin/console doctrine:migrations:version 'CoreBundle/Migrations/Version20201208105207' --add
    ```

1. The base of the `Adjustment` class has changed. If you extend your adjustments already(or have them overridden by default, because of Sylius-Standard usage), you should base your Adjustment class on `Sylius\Component\Core\Model\Adjustment` instead of `Sylius\Component\Order\Model\Adjustment`.

    ```diff
    -       use Sylius\Component\Order\Model\Adjustment as BaseAdjustment;
    +       use Sylius\Component\Core\Model\Adjustment as BaseAdjustment;
    ```

### Package upgrades

1. We've removed the support for Symfony's Templating compenent (which is removed in Symfony 5). 

    * Remove `templating` from framework's configration:
        
        ```diff
        # config/packages/framework.yaml
       
        framework:
            # ...
        -    templating: { engines: ["twig"] }
        ```
      
    * Replace any usages of `Symfony\Bundle\FrameworkBundle\Templating\EngineInterface` with `Twig\Environment`.
   
        Inject `twig` service into your controllers instead of `templating`.
      
        `$templating->renderResponse(...)` might be replaced with `new Response($twig->render(...))`.
    
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
