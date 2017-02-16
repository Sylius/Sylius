How to disable localised URLs?
==============================

URLs in Sylius are localised, this means they contain the ``/locale`` prefix with the current locale.
For example when the ``English (United States)`` locale is currently chosen in the channel, the URL of homepage will
look like that ``localhost:8000/en_US/``.

If you do not need localised URLs, this guide will help you to disable this feature.

**1.** Customise the application routing in the ``app/config/routing.yml``.

Replace:

.. code-block:: yaml

    # app/config/routing.yml

    sylius_shop:
        resource: "@SyliusShopBundle/Resources/config/routing.yml"
        prefix: /{_locale}
        requirements:
            _locale: ^[a-z]{2}(?:_[A-Z]{2})?$

    sylius_shop_default_locale:
        path: /
        methods: [GET]
        defaults:
            _controller: sylius.controller.shop.locale_switch:switchAction

With:

.. code-block:: yaml

    # app/config/routing.yml

    sylius_shop:
        resource: "@SyliusShopBundle/Resources/config/routing.yml"

**2.** Customise the security settings in the ``app/config/security.yml``.

Replace:

.. code-block:: yaml

    # app/config/security.yml

    parameters:
        # ...
        sylius.security.shop_regex: "^/(?!admin|api)[^/]++"

With:

.. code-block:: yaml

    # app/config/security.yml

    parameters:
        # ...
        sylius.security.shop_regex: "^"

**3.** Customise SyliusShopBundle to use storage-based locale switching by adding the following lines at the end of the ``app/config/config.yml``.

.. code-block:: yaml

    # app/config/config.yml

    sylius_shop:
        locale_switcher: storage
