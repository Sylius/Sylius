How to disable default shop, admin or API of Sylius?
====================================================

When you are using Sylius as a whole you may be needing to remove some of its parts. It is possible to remove
for example Sylius shop to have only administration panel and API. Or the other way, remove API if you do not need it.

Therefore you have this guide that will help you when wanting to disable shop, admin or API of Sylius.

How to disable Sylius shop?
---------------------------

**1.** Remove SyliusShopBundle from ``config/bundles.php``.

.. code-block:: php

    // # config/bundles.php

    return [
        ...

        // Sylius\Bundle\ShopBundle\SyliusShopBundle::class => ['all' => true], // - remove or leave this line commented

        ...
    ];

**2.** Remove SyliusShopBundle's config import from ``config/packages/_sylius.yaml``.

Here you've got the line that should disappear from imports:

.. code-block:: yaml

    imports:
    #    - { resource: "@SyliusShopBundle/Resources/config/app/config.yml" } # remove or leave this line commented

**3.** Remove SyliusShopBundle routing configuration file ``config/routes/sylius_shop.yaml``.

**4.** Remove security configuration from ``config/packages/security.yaml``.

The part that has to be removed from this file is shown below:

.. code-block:: yaml

    parameters:
        # sylius.security.shop_regex: "^/(?!admin|api/.*|api$)[^/]++"

    security:
        firewalls:
    # Delete or leave this part commented
    #        shop:
    #            switch_user: { role: ROLE_ALLOWED_TO_SWITCH }
    #            context: shop
    #            pattern: "%sylius.security.shop_regex%"
    #            form_login:
    #                success_handler: sylius.authentication.success_handler
    #                failure_handler: sylius.authentication.failure_handler
    #                provider: sylius_shop_user_provider
    #                login_path: sylius_shop_login
    #                check_path: sylius_shop_login_check
    #                failure_path: sylius_shop_login
    #                default_target_path: sylius_shop_homepage
    #                use_forward: false
    #                use_referer: true
    #                csrf_token_generator: security.csrf.token_manager
    #                csrf_parameter: _csrf_shop_security_token
    #                csrf_token_id: shop_authenticate
    #            remember_me:
    #                secret: "%secret%"
    #                name: APP_SHOP_REMEMBER_ME
    #                lifetime: 31536000
    #                remember_me_parameter: _remember_me
    #            logout:
    #                path: sylius_shop_logout
    #                target: sylius_shop_login
    #                invalidate_session: false
    #                success_handler: sylius.handler.shop_user_logout
    #            anonymous: true

    access_control:
    #    - { path: "%sylius.security.shop_regex%/_partial", role: IS_AUTHENTICATED_ANONYMOUSLY, ips: [127.0.0.1, ::1] }
    #    - { path: "%sylius.security.shop_regex%/_partial", role: ROLE_NO_ACCESS }

    #    - { path: "%sylius.security.shop_regex%/login", role: IS_AUTHENTICATED_ANONYMOUSLY }

    #    - { path: "%sylius.security.shop_regex%/register", role: IS_AUTHENTICATED_ANONYMOUSLY }
    #    - { path: "%sylius.security.shop_regex%/verify", role: IS_AUTHENTICATED_ANONYMOUSLY }

    #    - { path: "%sylius.security.shop_regex%/account", role: ROLE_USER }
    #    - { path: "%sylius.security.shop_regex%/seller/register", role: ROLE_USER }

**Done!** There is no shop in Sylius now, just admin and API.

How to disable Sylius Admin?
----------------------------

**1.** Remove SyliusAdminBundle from ``config/bundles.php``.

.. code-block:: php

    // # config/bundles.php

    return [
        ...

        // Sylius\Bundle\AdminBundle\SyliusAdminBundle::class => ['all' => true], // - remove or leave this line commented

        ...
    ];

**2.** Remove SyliusAdminBundle's config import from ``config/packages/_sylius.yaml``.

Here you've got the line that should disappear from imports:

.. code-block:: yaml

    imports:
    #    - { resource: "@SyliusAdminBundle/Resources/config/app/config.yml" } # remove or leave this line commented

**3.** Remove SyliusAdminBundle routing configuration from ``config/routes/sylius_admin.yaml``.

**4.** Remove security configuration from ``config/packages/security.yaml``.

The part that has to be removed from this file is shown below:

.. code-block:: yaml

    parameters:
    # Delete or leave this part commented
    #    sylius.security.admin_regex: "^/admin"
        sylius.security.shop_regex: "^/(?!api/.*|api$)[^/]++" # Remove `admin|` from the pattern

    security:
        firewalls:
    # Delete or leave this part commented
    #        admin:
    #            switch_user: true
    #            context: admin
    #            pattern: "%sylius.security.admin_regex%"
    #            form_login:
    #                provider: sylius_admin_user_provider
    #                login_path: sylius_admin_login
    #                check_path: sylius_admin_login_check
    #                failure_path: sylius_admin_login
    #                default_target_path: sylius_admin_dashboard
    #                use_forward: false
    #                use_referer: true
    #                csrf_token_generator: security.csrf.token_manager
    #                csrf_parameter: _csrf_admin_security_token
    #                csrf_token_id: admin_authenticate
    #            remember_me:
    #                secret: "%secret%"
    #                path: /admin
    #                name: APP_ADMIN_REMEMBER_ME
    #                lifetime: 31536000
    #                remember_me_parameter: _remember_me
    #            logout:
    #                path: sylius_admin_logout
    #                target: sylius_admin_login
    #            anonymous: true

    access_control:
    # Delete or leave this part commented
    #    - { path: "%sylius.security.admin_regex%/_partial", role: IS_AUTHENTICATED_ANONYMOUSLY, ips: [127.0.0.1, ::1] }
    #    - { path: "%sylius.security.admin_regex%/_partial", role: ROLE_NO_ACCESS }

    #    - { path: "%sylius.security.admin_regex%/login", role: IS_AUTHENTICATED_ANONYMOUSLY }

    #    - { path: "%sylius.security.admin_regex%", role: ROLE_ADMINISTRATION_ACCESS }

**Done!** There is no admin in Sylius now, just api and shop.

How to disable Sylius API?
--------------------------

**1.** Remove SyliusAdminApiBundle & FOSOAuthServerBundle from ``config/bundles.php``.

.. code-block:: php

    // # config/bundles.php

    return [
        ...

        // FOS\OAuthServerBundle\FOSOAuthServerBundle::class => ['all' => true],
        // Sylius\Bundle\AdminApiBundle\SyliusAdminApiBundle::class => ['all' => true], // - remove or leave this line commented

        ...
    ];

**2.** Remove SyliusAdminApiBundle's config import from ``config/packages/_sylius.yaml``.

Here you've got the line that should disappear from imports:

.. code-block:: yaml

    imports:
    #    - { resource: "@SyliusAdminApiBundle/Resources/config/app/config.yml" } # remove or leave this line commented

**3.** Remove SyliusAdminApiBundle routing configuration from ``config/routes/sylius_admin_api.yaml``.

**4.** Remove security configuration from ``config/packages/security.yaml``.

The part that has to be removed from this file is shown below:

.. code-block:: yaml

    parameters:
    # Delete or leave this part commented
    #   sylius.security.api_regex: "^/api"
        sylius.security.shop_regex: "^/(?!admin$)[^/]++" # Remove `|api/.*|api` from the pattern

    security:
        firewalls:
    # Delete or leave this part commented
    #        oauth_token:
    #            pattern: "%sylius.security.api_regex%/oauth/v2/token"
    #            security: false
    #        api:
    #           pattern:    "%sylius.security.api_regex%/.*"
    #           fos_oauth:  true
    #           stateless:  true
    #           anonymous:  true

    access_control:
    # Delete or leave this part commented
    #    - { path: "%sylius.security.api_regex%/login", role: IS_AUTHENTICATED_ANONYMOUSLY }

    #    - { path: "%sylius.security.api_regex%/.*", role: ROLE_API_ACCESS }

**5.** Remove fos_rest config from ``app/config/config.yml``.

.. code-block:: yaml

    fos_rest:
        format_listener:
            rules:
            #    - { path: '^/api', priorities: ['json', 'xml'], fallback_format: json, prefer_extension: true } # remove or leave this line commented

**Done!** There is no API in Sylius now, just admin and shop.

Learn more
----------

* :ref:`Architecture: Division into Core, Shop, Admin and API <division-into-core-shop-admin-api>`
