How to disable localised URLs?
==============================

URLs in Sylius are localised, this means they contain the ``/locale`` prefix with the current locale.
For example when the ``English (United States)`` locale is currently chosen in the channel, the URL of homepage will
look like that ``localhost:8000/en_US/``.

If you do not need localised URLs, this guide will help you to disable this feature.

**1.** Customise the application routing in the ``config/routes/sylius_shop.yaml``.

Replace:

.. code-block:: yaml

    # config/routes/sylius_shop.yaml
    
    sylius_shop:
        resource: "@SyliusShopBundle/Resources/config/routing.yml"
        prefix: /{_locale}
        requirements:
            _locale: ^[a-z]{2}(?:_[A-Z]{2})?$

    sylius_shop_payum:
        resource: "@SyliusShopBundle/Resources/config/routing/payum.yml"

    sylius_shop_default_locale:
        path: /
        methods: [GET]
        defaults:
            _controller: sylius.controller.shop.locale_switch:switchAction

With:

.. code-block:: yaml

    # config/routes/sylius_shop.yaml    
    sylius_shop:
        resource: "@SyliusShopBundle/Resources/config/routing.yml"

    sylius_shop_payum:
        resource: "@SyliusShopBundle/Resources/config/routing/payum.yml"

**2.** Customise the security settings in the ``config/packages/security.yml``.

Replace:

.. code-block:: yaml

    # config/packages/security.yml

    parameters:
        # ...
        sylius.security.shop_regex: "^/(?!admin|api/.*|api$|media/.*)[^/]++""

With:

.. code-block:: yaml

    # config/packages/security.yml

    parameters:
        # ...
        sylius.security.shop_regex: "^"

**3.** Customise SyliusShopBundle to use storage-based locale switching in the ``config/packages/_sylius.yml``.

Replace :

.. code-block:: yaml

    # config/packages/_sylius.yml

    sylius_shop:
        product_grid:
            include_all_descendants: true

With:

.. code-block:: yaml

    # config/packages/_sylius.yml

    sylius_shop:
        product_grid:
            include_all_descendants: true
        locale_switcher: storage
