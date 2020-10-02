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
   
1. All consts classes has been changed from final classes to interfaces. As a result initialization of `\Sylius\Bundle\UserBundle\UserEvents` is not longer possible. The whole list of changed classes can be found [here](https://github.com/Sylius/Sylius/pull/11347).

1. Service alias `Sylius\Component\Channel\Context\ChannelContextInterface` was changed from `sylius.context.channel.composite` to `sylius.context.channel`.
The later is being decorated by `sylius.context.channel.cached` which caches the channel per request and reduces the amount of database queries.

1. A serialization group has been added to the route `sylius_admin_ajax_product_index` to avoid an infinite loop, or a
time out during this ajax request (previously no serialization group was defined on this route).

1. We now use the parameter `sylius_admin.path_name` to retrieve the admin routes prefix. If you used the `/admin` prefix
in some admin URLs you can now replace `/admin` by `/%sylius_admin.path_name%`.  
Also the route is now dynamic. You can change the `SYLIUS_ADMIN_ROUTING_PATH_NAME` environment variable to custom the admin's URL.

## Special attention

### Migrations

As we switched to the `3.0` version of Doctrine Migrations, there are some things that need to be changed in the application that is upgraded to Sylius 1.8:

1. Replace the DoctrineMigrationsBundle configuration in `config/packages/doctrine_migrations.yaml`:

   ```
   doctrine_migrations:
   -    dir_name: "%kernel.project_dir%/src/Migrations"
   -
   -    # Namespace is arbitrary but should be different from App\Migrations as migrations classes should NOT be autoloaded
   -    namespace: DoctrineMigrations
   +    storage:
   +        table_storage:
   +            table_name: sylius_migrations
   +    migrations_paths:
   +        'DoctrineMigrations': 'src/Migrations'
   ``` 

1. Remove all the legacy Sylius-Standard migrations (they're not needed anymore)

   ```bash
   rm "src/Migrations/Version20170912085504.php"
   #...
   ```

1. Mark all migrations from **@SyliusCoreBundle** and **@SyliusAdminApiBundle** as executed
(they're the same as legacy ones, just not recognized by the doctrine _yet_)

   ```bash
   bin/console doctrine:migrations:version "Sylius\Bundle\CoreBundle\Migrations\Version20161202011555" --add --no-interaction
   #...
   ```

    > BEWARE!

    If you're using some Sylius plugins you'll probably need to handle their migrations as well.
    You can either leave them in your `src/Migrations/` catalog and treat as _your own_ or use the migrations from the vendors.
    However, it assumes that plugin provides a proper integration with Doctrine Migrations 3.0 - as, for example,
    [Invoicing Plugin](https://github.com/Sylius/InvoicingPlugin/blob/master/src/DependencyInjection/SyliusInvoicingExtension.php#L33).

    > TIP

    Take a look at [etc/migrations-1.8.sh](etc/migrations-1.8.sh) script - it would execute points 2) and 3) automatically.

1. Do the same for your own migrations. Assuming your migrations namespace is `DoctrineMigrations` and
migrations catalog is `src/Migrations`, you can run such a script:

    ```bash
    #!/bin/bash
   
    for file in $(ls -1 src/Migrations/ | sed -e 's/\..*$//')
    do
        bin/console doctrine:migrations:version "DoctrineMigrations\\${file}" --add --no-interaction
    done
    ```
   
1. Run `bin/console doctrine:migrations:migrate` to apply migrations added in Sylius 1.8

### Translations

Some translations have changed, you may want to search for them in your project:

- `sylius.email.shipment_confirmation.tracking_code` has been removed.
- `sylius.email.shipment_confirmation.you_can_check_its_location` has been removed.
- `sylius.email.shipment_confirmation.you_can_check_its_location_with_the_tracking_code` has been added instead of the two above.

