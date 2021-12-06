# UPGRADE FROM `v1.8.4` TO `v1.8.6`

1. Api is disabled by default, to enable it you need to set flag ``sylius_api.enabled`` to ``true`` in ``config/packages/_sylius.yaml``.

1. Change configuration of new ApiBundle in your `config/packages/security.yaml` file:

    ```diff
         security:
             firewalls:
                 new_api_admin_user:
                     json_login:
    -                    check_path: "%sylius.security.new_api_route%/admin/authentication-token"
    +                    check_path: "%sylius.security.new_api_admin_route%/authentication-token"
                 new_api_shop_user:
                     json_login:
    -                    check_path: "%sylius.security.new_api_route%/shop/authentication-token"
    +                    check_path: "%sylius.security.new_api_shop_route%/authentication-token"
             access_control:
    -            - { path: "%sylius.security.new_api_route%/admin/authentication-token", role: IS_AUTHENTICATED_ANONYMOUSLY }
    +            - { path: "%sylius.security.new_api_admin_route%/authentication-token", role: IS_AUTHENTICATED_ANONYMOUSLY }
    -            - { path: "%sylius.security.new_api_route%/shop/authentication-token", role: IS_AUTHENTICATED_ANONYMOUSLY }
    +            - { path: "%sylius.security.new_api_shop_route%/authentication-token", role: IS_AUTHENTICATED_ANONYMOUSLY }
    ```

# UPGRADE FROM `v1.8.0` TO `v1.8.1`

1. Change configuration of new ApiBundle in your `config/packages/security.yaml` file:

    ```diff
        security:
            providers:
    -           sylius_api_chain_provider:
    -               chain:
    -                   providers: [sylius_api_shop_user_provider, sylius_api_admin_user_provider]
            
            firewalls:
                new_api_admin_user:
    -               pattern: "%sylius.security.new_api_route%/admin-user-authentication-token"
    -               provider: sylius_admin_user_provider
    +               pattern: "%sylius.security.new_api_admin_regex%/.*"
    +               provider: sylius_api_admin_user_provider
                    json_login:
    -                   check_path: "%sylius.security.new_api_route%/admin-user-authentication-token"
    +                   check_path: "%sylius.security.new_api_route%/admin/authentication-token"

                new_api_shop_user:
    -               pattern: "%sylius.security.new_api_route%/shop-user-authentication-token"
    -               provider: sylius_shop_user_provider
    +               pattern: "%sylius.security.new_api_shop_regex%/.*"
    +               provider: sylius_api_shop_user_provider
                    json_login:
    -                   check_path: "%sylius.security.new_api_route%/shop-user-authentication-token"
    +                   check_path: "%sylius.security.new_api_route%/shop/authentication-token"

    -           new_api:
    -               pattern: "%sylius.security.new_api_regex%/*"
    -               provider: sylius_api_chain_provider
    -               stateless: true
    -               anonymous: lazy
    -               guard:
    -                   authenticators:
    -                       - lexik_jwt_authentication.jwt_token_authenticator

            access_control:
    +            - { path: "%sylius.security.new_api_route%/admin/authentication-token", role: IS_AUTHENTICATED_ANONYMOUSLY }
    +            - { path: "%sylius.security.new_api_route%/shop/authentication-token", role: IS_AUTHENTICATED_ANONYMOUSLY }
    ```

# UPGRADE FROM `v1.7.X` TO `v1.8.0`

1. Add new bundles to your list of used bundles in `config/bundles.php` if you are not using it apart from Sylius:

    ```diff
    +   ApiPlatform\Core\Bridge\Symfony\Bundle\ApiPlatformBundle::class => ['all' => true],
    +   Sylius\Bundle\ApiBundle\SyliusApiBundle::class => ['all' => true],
    +   Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle::class => ['all' => true],
    +   SyliusLabs\DoctrineMigrationsExtraBundle\SyliusLabsDoctrineMigrationsExtraBundle::class => ['all' => true],
    ```

1. Add configuration of new ApiBundle in your `config/packages/_sylius.yaml` file:

    ```diff
        imports:
    +       - { resource: "@SyliusApiBundle/Resources/config/app/config.yaml" }
    ```

1. Add configuration of new ApiBundle in your `config/packages/security.yaml` file:

    ```diff
        parameters:
    -       sylius.security.admin_regex: "^/admin"
    -       sylius.security.shop_regex: "^/(?!admin|api/.*|api$|media/.*)[^/]++"
    +       sylius.security.admin_regex: "^/%sylius_admin.path_name%"
    +       sylius.security.shop_regex: "^/(?!%sylius_admin.path_name%|new-api|api/.*|api$|media/.*)[^/]++"
    +       sylius.security.new_api_route: "/new-api"
    +       sylius.security.new_api_regex: "^%sylius.security.new_api_route%"
    +       sylius.security.new_api_admin_route: "%sylius.security.new_api_route%/admin"
    +       sylius.security.new_api_admin_regex: "^%sylius.security.new_api_admin_route%"
    +       sylius.security.new_api_shop_route: "%sylius.security.new_api_route%/shop"
    +       sylius.security.new_api_shop_regex: "^%sylius.security.new_api_shop_route%"
        
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
                admin:
                    remember_me:
    -                   path: /admin
    +                   path: "/%sylius_admin.path_name%"
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
    + 
            access_control:
    +            - { path: "%sylius.security.new_api_admin_regex%/.*", role: ROLE_API_ACCESS }
    +            - { path: "%sylius.security.new_api_shop_regex%/.*", role: IS_AUTHENTICATED_ANONYMOUSLY }
    ```
1. Add `sylius_api.yaml` file to `config/routes/` directory:

    ```yaml
       sylius_api:
           resource: "@SyliusApiBundle/Resources/config/routing.yml"
           prefix: "%sylius.security.new_api_route%"
    ```

1. Add `lexik_jwt_authentication.yaml` file to `config/packages/` directory:

    ```yaml
       lexik_jwt_authentication:
         secret_key: '%env(resolve:JWT_SECRET_KEY)%'
         public_key: '%env(resolve:JWT_PUBLIC_KEY)%'
         pass_phrase: '%env(JWT_PASSPHRASE)%'
    ```

1. Add configuration in your `.env` file:

    ```diff
    +       ###> lexik/jwt-authentication-bundle ###
    +       JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
    +       JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
    +       JWT_PASSPHRASE=YOUR_SECRET_PASSPHRASE
    +       ###< lexik/jwt-authentication-bundle ###

1. Add configuration in your `.env.test` file:

    ```diff
    +       ###> lexik/jwt-authentication-bundle ###
    +       JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private-test.pem
    +       JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public-test.pem
    +       JWT_PASSPHRASE=ALL_THAT_IS_GOLD_DOES_NOT_GLITTER_NOT_ALL_THOSE_WHO_WANDER_ARE_LOST
    +       ###< lexik/jwt-authentication-bundle ###

1. Add configuration in your `.env.test_cached` file:

    ```diff
    +       ###> lexik/jwt-authentication-bundle ###
    +       JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private-test.pem
    +       JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public-test.pem
    +       JWT_PASSPHRASE=ALL_THAT_IS_GOLD_DOES_NOT_GLITTER_NOT_ALL_THOSE_WHO_WANDER_ARE_LOST
    +       ###< lexik/jwt-authentication-bundle ###

1. Sample JWT token generation is available [here](https://api-platform.com/docs/core/jwt/)
