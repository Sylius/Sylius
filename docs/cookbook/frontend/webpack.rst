Using Webpack Encore in Sylius
------------------------------

This is a simple guide on how to start using webpack in Sylius apps. Webpack finally lets us easily customize Sylius assets.

**1.** Install webpack-encore-bundle:

.. code-block:: bash

    composer require symfony/webpack-encore-bundle

**2.** Edit the ``config/packages/assets.yaml`` file:

.. code-block:: yaml

    framework:
        assets:
            packages:
                shop:
                    json_manifest_path: '%kernel.project_dir%/public/build/shop/manifest.json'
                admin:
                    json_manifest_path: '%kernel.project_dir%/public/build/admin/manifest.json'

**3.** Edit the ``config/packages/webpack_encore.yaml`` file:

.. code-block:: yaml

    webpack_encore:
        output_path: '%kernel.project_dir%/public/build/default'
        builds:
            shop: '%kernel.project_dir%/public/build/shop'
            admin: '%kernel.project_dir%/public/build/admin'

**4.** Overwrite template files and add new assets paths for admin and shop:

.. code-block:: twig

    // templates/bundles/SyliusAdminBundle/_scripts.html.twig
    {{ encore_entry_script_tags('admin-entry', null, 'admin') }}

    // templates/bundles/SyliusAdminBundle/_styles.html.twig
    {{ encore_entry_link_tags('admin-entry', null, 'admin') }}

    // templates/bundles/SyliusAdminBundle/_logo.html.twig
    <a class="item" href="{{ path('sylius_admin_dashboard') }}" style="padding: 13px 0;">
        <div style="max-width: 90px; margin: 0 auto;">
            <img src="{{ asset('build/admin/images/admin-logo.svg', 'admin') }}" class="ui fluid image">
        </div>
    </a>

    // templates/bundles/SyliusShopBundle/_scripts.html.twig
    {{ encore_entry_script_tags('shop-entry', null, 'shop') }}

    // templates/bundles/SyliusShopBundle/_styles.html.twig
    {{ encore_entry_link_tags('shop-entry', null, 'shop') }}

    // templates/bundles/SyliusShopBundle/Layout/Header/_header.html.twig
    <div class="column">
        <a href="{{ path('sylius_shop_homepage') }}">
            <img src="{{ asset('assets/shop/img/logo.png') }}" alt="Sylius logo" class="ui small image" />
        </a>
    </div>


.. warning::

    The paths should be changed for each asset you use.

**5.** To build the assets, run:

.. code-block:: bash

    yarn encore dev
    # or
    yarn encore production
    # or
    yarn encore dev-server

.. tip::

    When compiling assets, errors may appear (they don't break the build), due to different babel configuration for gulp
    and webpack. Once you decide to use the webpack you can delete the ``gulpfile.babel.js`` and ``.babelrc`` from the root
    directory - then the errors will stop appearing.

Learn more
----------

* `Webpack Encore Documentation <https://symfony.com/doc/current/frontend.html#webpack-encore>`_
