# UPGRADE FROM `v1.7.X` TO `v1.8.0`

1. Add new bundles to your list of used bundles in `config/bundles.php` if you are not using it apart from Sylius:

    ```diff
    +   ApiPlatform\Core\Bridge\Symfony\Bundle\ApiPlatformBundle::class => ['all' => true],
    +   Sylius\Bundle\ApiBundle\SyliusApiBundle::class => ['all' => true],
    +   Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle::class => ['all' => true],
    ```

1. Add configuration of ApiBundle in your `config/packages/_sylius.yaml` file:

    ```diff
        imports:
    +       - { resource: "@SyliusApiBundle/Resources/config/app/config.yaml" }
    ```

1. Add configuration of new ApiBundle in your `config/packages/security.yaml` file:

    ```diff
        parameters:
    +       sylius.security.new_api_route: "/new-api"
    +       sylius.security.new_api_regex: "^%sylius.security.new_api_route%"
        
        security:
            providers:
    +           sylius_api_admin_user_provider:
    +               id: sylius.admin_user_provider.email_or_name_based
    +           sylius_api_shop_user_provider:
    +               id: sylius.shop_user_provider.email_or_name_based
    +           sylius_api_chain_provider:
    +               chain:
    +                   providers: [sylius_api_shop_user_provider, sylius_api_admin_user_provider]
            
            firewalls:
    +           new_api_admin_user:
    +               pattern: "%sylius.security.new_api_route%/admin-user-authentication-token"
    +               provider: sylius_admin_user_provider
    +               stateless: true
    +               anonymous: true
    +               json_login:
    +                   check_path: "%sylius.security.new_api_route%/admin-user-authentication-token"
    +                   username_path: email
    +                   password_path: password
    +                   success_handler: lexik_jwt_authentication.handler.authentication_success
    +                   failure_handler: lexik_jwt_authentication.handler.authentication_failure
    +               guard:
    +                   authenticators:
    +                       - lexik_jwt_authentication.jwt_token_authenticator
    +   
    +           new_api_shop_user:
    +               pattern: "%sylius.security.new_api_route%/shop-user-authentication-token"
    +               provider: sylius_shop_user_provider
    +               stateless: true
    +               anonymous: true
    +               json_login:
    +                   check_path: "%sylius.security.new_api_route%/shop-user-authentication-token"
    +                   username_path: email
    +                   password_path: password
    +                   success_handler: lexik_jwt_authentication.handler.authentication_success
    +                   failure_handler: lexik_jwt_authentication.handler.authentication_failure
    +               guard:
    +                   authenticators:
    +                       - lexik_jwt_authentication.jwt_token_authenticator
    +   
    +           new_api:
    +               pattern: "%sylius.security.new_api_regex%/*"
    +               provider: sylius_api_chain_provider
    +               stateless: true
    +               anonymous: lazy
    +               guard:
    +                   authenticators:
    +                       - lexik_jwt_authentication.jwt_token_authenticator
    ```

1. Add `sylius_api.yaml` file to `config/routes/` directory:

    ```yaml
       sylius_api:
           resource: "@SyliusApiBundle/Resources/config/routing.yml"
           prefix: "%sylius.security.new_api_route%"
    ```

1. All consts classes has been changed from final classes to interfaces. As a result initialization of `\Sylius\Bundle\UserBundle\UserEvents` is not longer possible. The whole list of changed classes can be found [here](https://github.com/Sylius/Sylius/pull/11347).

1. Service alias `Sylius\Component\Channel\Context\ChannelContextInterface` was changed from `sylius.context.channel.composite` to `sylius.context.channel`.
The later is being decorated by `sylius.context.channel.cached` which caches the channel per request and reduces the amount of database queries.
