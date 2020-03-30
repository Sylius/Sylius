# UPGRADE FROM `v1.7.X` TO `v1.8.0`

1. Add new bundles to your list of used bundles in `config/bundles.php` if you are not using it apart from Sylius:

    ```diff
    +   ApiPlatform\Core\Bridge\Symfony\Bundle\ApiPlatformBundle::class => ['all' => true],
    +   Sylius\Bundle\ApiBundle\SyliusApiBundle::class => ['all' => true],
    +   Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle::class => ['all' => true],
    ```

2. Add configuration of ApiBundle in your `config/packages/_sylius.yaml` file:

    ```diff
        imports:
    +       - { resource: "@SyliusApiBundle/Resources/config/app/config.yaml" }
    ```

3. Add configuration of new ApiBundle in your `config/packages/security.yaml` file:

    ```diff
        parameters:
    +       sylius.security.new_api_route: "/new-api"
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
            
            firewalls:
    +           new_api_admin:
    +               pattern: "%sylius.security.new_api_admin_regex%/.*"
    +               stateless: true
    +               anonymous: true
    +               provider: sylius_api_admin_user_provider
    +               json_login:
    +                   check_path: "%sylius.security.new_api_admin_route%/authentication-token"
    +                   username_path: email
    +                   password_path: password
    +                   success_handler: lexik_jwt_authentication.handler.authentication_success
    +                   failure_handler: lexik_jwt_authentication.handler.authentication_failure
    +               guard:
    +                   authenticators:
    +                       - lexik_jwt_authentication.jwt_token_authenticator
                
    +           new_api_shop:
    +               pattern: "%sylius.security.new_api_shop_regex%/.*"
    +               stateless: true
    +               anonymous: true
    +               provider: sylius_api_shop_user_provider
    +               json_login:
    +                   check_path: "%sylius.security.new_api_shop_route%/authentication-token"
    +                   username_path: email
    +                   password_path: password
    +                   success_handler: lexik_jwt_authentication.handler.authentication_success
    +                   failure_handler: lexik_jwt_authentication.handler.authentication_failure
    +               guard:
    +                   authenticators:
    +                       - lexik_jwt_authentication.jwt_token_authenticator
            
            access_control:
    +           - { path: "%sylius.security.new_api_admin_regex%/authentication-token", role: IS_AUTHENTICATED_ANONYMOUSLY }
    +           - { path: "%sylius.security.new_api_admin_regex%/.*", role: ROLE_API_ACCESS }
    +           - { path: "%sylius.security.new_api_shop_regex%/authentication-token", role: IS_AUTHENTICATED_ANONYMOUSLY }
    +           - { path: "%sylius.security.new_api_shop_regex%/account", role: ROLE_USER }
    +           - { path: "%sylius.security.new_api_route%/docs", role: IS_AUTHENTICATED_ANONYMOUSLY }
    ```

4. Add `sylius_api.yaml` file to `config/routes/` directory:

    ```yaml
       sylius_api:
           resource: "@SyliusApiBundle/Resources/config/routing.yml"
           prefix: "%sylius.security.new_api_route%"
    ```
