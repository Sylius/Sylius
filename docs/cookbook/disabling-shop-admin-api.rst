How to disable default shop, admin or API of Sylius?
====================================================

When you are using Sylius as a whole you may be needing to remove some of its parts. It is possible to remove
for example Sylius shop to have only administration panel and API. Or the other way, remove API if you do not need it.

Therefore you have this guide that will help you when wanting to disable shop, admin or API of Sylius.

How to disable Sylius shop?
---------------------------

**1.** Remove SyliusShopBundle from ``app/AppKernel``.

.. code-block:: php

    // # app/AppKernel.php

    public function registerBundles()
    {
        $bundles = [
            new \Sylius\Bundle\AdminBundle\SyliusAdminBundle(),
            // new \Sylius\Bundle\ShopBundle\SyliusShopBundle(), // - remove or leave this line commented

            new \FOS\OAuthServerBundle\FOSOAuthServerBundle(),
            new \Sylius\Bundle\AdminApiBundle\SyliusAdminApiBundle(),

            new \AppBundle\AppBundle(),
        ];

        return array_merge(parent::registerBundles(), $bundles);
    }

**2.** Remove SyliusShopBundle's config import from ``app/config/config.yml``.

Here you've got the line that should disappear from imports:

.. code-block:: yaml

    imports:
    #    - { resource: "@SyliusShopBundle/Resources/config/app/config.yml" } # remove or leave this line commented

**3.** Remove SyliusShopBundle routing configuration from ``app/config/routing.yml``.

.. code-block:: yaml

    # sylius_shop:
    #    resource: "@SyliusShopBundle/Resources/config/routing.yml" # remove or leave these lines commented

**4.** Remove security configuration from ``app/config/security.yml``.

The part that has to be removed from this file is shown below:

.. code-block:: yaml

    security:
        firewalls:
    # Delete or leave this part commented
    #        shop:
    #            switch_user: { role: ROLE_ALLOWED_TO_SWITCH }
    #            context: shop
    #            pattern: /.*
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
    #            remember_me:
    #                secret: "%secret%"
    #                name: APP_REMEMBER_ME
    #                lifetime: 31536000
    #                always_remember_me: true
    #                remember_me_parameter: _remember_me
    #            logout:
    #                path: sylius_shop_logout
    #                target: sylius_shop_login
    #                invalidate_session: false
    #                success_handler: sylius.handler.shop_user_logout
    #            anonymous: true

**Done!** There is no shop in Sylius now, just admin and API.

How to disable Sylius Admin?
----------------------------

**1.** Remove SyliusAdminBundle from ``app/AppKernel``.

.. code-block:: php

    // # app/AppKernel.php

    public function registerBundles()
    {
        $bundles = [
            // new \Sylius\Bundle\AdminBundle\SyliusAdminBundle(), // - remove or leave this line commented
            new \Sylius\Bundle\ShopBundle\SyliusShopBundle(),

            new \FOS\OAuthServerBundle\FOSOAuthServerBundle(),
            new \Sylius\Bundle\AdminApiBundle\SyliusAdminApiBundle(),

            new \AppBundle\AppBundle(),
        ];

        return array_merge(parent::registerBundles(), $bundles);
    }

**2.** Remove SyliusAdminBundle's config import from ``app/config/config.yml``.

Here you've got the line that should disappear from imports:

.. code-block:: yaml

    imports:
    #    - { resource: "@SyliusAdminBundle/Resources/config/app/config.yml" } # remove or leave this line commented

**3.** Remove SyliusAdminBundle routing configuration from ``app/config/routing.yml``.

.. code-block:: yaml

    #    sylius_shop:
    #        resource: "@SyliusAdminBundle/Resources/config/routing.yml"

**4.** Remove security configuration from ``app/config/security.yml``.

The part that has to be removed from this file is shown below:

.. code-block:: yaml

    security:
        firewalls:
    # Delete or leave this part commented
    #       admin:
    #            switch_user: true
    #            context: admin
    #            pattern: /admin(?:/.*)?$
    #            form_login:
    #                provider: sylius_admin_user_provider
    #                login_path: sylius_admin_login
    #                check_path: sylius_admin_login_check
    #                failure_path: sylius_admin_login
    #                default_target_path: sylius_admin_dashboard
    #                use_forward: false
    #                use_referer: true
    #            logout:
    #                path: sylius_admin_logout
    #                target: sylius_admin_login
    #            anonymous: true

**Done!** There is no admin in Sylius now, just api and shop.

How to disable Sylius API?
--------------------------

**1.** Remove SyliusAdminApiBundle from ``app/AppKernel``.

.. code-block:: php

    // # app/AppKernel.php

    public function registerBundles()
    {
        $bundles = [
            new \Sylius\Bundle\AdminBundle\SyliusAdminBundle(),
            new \Sylius\Bundle\ShopBundle\SyliusShopBundle(),

            new \FOS\OAuthServerBundle\FOSOAuthServerBundle(),
            // new \Sylius\Bundle\AdminApiBundle\SyliusAdminApiBundle(), // - remove or leave this line commented

            new \AppBundle\AppBundle(),
        ];

        return array_merge(parent::registerBundles(), $bundles);
    }

**2.** Remove SyliusAdminApiBundle's config import from ``app/config/config.yml``.

Here you've got the line that should disappear from imports:

.. code-block:: yaml

    imports:
    #    - { resource: "@SyliusAdminApiBundle/Resources/config/app/config.yml" } # remove or leave this line commented

**3.** Remove SyliusAdminApiBundle routing configuration from ``app/config/routing.yml``.

.. code-block:: yaml

    # sylius_shop:
    #    resource: "@SyliusAdminApiBundle/Resources/config/routing.yml" # remove or leave these lines commented

**4.** Remove security configuration from ``app/config/security.yml``.

The part that has to be removed from this file is shown below:

.. code-block:: yaml

    security:
        firewalls:
        api:
    #        pattern:    ^/api
    #        fos_oauth:  true
    #        stateless:  true
    #        anonymous:  true

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
