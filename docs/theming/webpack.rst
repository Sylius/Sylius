.. raw:: html

    <div style="padding: 20px; background: #ffedcc; border-radius: 4px; margin-bottom: 20px;">
    <strong>EXPERIMENTAL.</strong> This guide is based on "Sylius" and "Sylius-Standard" master branches.
    </div>

Using Webpack Encore in Sylius
------------------------------

This is a simple guide on how to start using webpack in Sylius apps. Webpack finally lets us easily customize Sylius assets.

**1.** Install webpack-encore-bundle:

.. code-block:: bash

    $ composer require symfony/webpack-encore-bundle

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

.. code-block:: php

    // templates/bundles/SyliusAdminBundle/_scripts.html.twig
    {{ encore_entry_script_tags('admin-entry', null, 'admin') }}

    // templates/bundles/SyliusAdminBundle/_styles.html.twig
    {{ encore_entry_link_tags('admin-entry', null, 'admin') }}

    // templates/bundles/SyliusAdminBundle/_logo.html.twig
    {{ asset('build/admin/images/admin-logo.svg', 'admin') }}

    // templates/bundles/SyliusShopBundle/_scripts.html.twig
    {{ encore_entry_script_tags('shop-entry', null, 'shop') }}

    // templates/bundles/SyliusShopBundle/_styles.html.twig
    {{ encore_entry_link_tags('shop-entry', null, 'shop') }}

    // templates/bundles/SyliusShopBundle/_header.html.twig
    {{ asset('build/shop/images/logo.png', 'shop') }}

.. warning::

    The paths should be changed for each asset you use.

**5.** To build the assets, run:

.. code-block:: bash

    $ yarn encore dev
    # or
    $ yarn encore production
    # or
    $ yarn encore dev-server

.. tip::

    When compiling assets, errors may appear (they don't break the build), due to different babel configuration for gulp
    and webpack. Once you decide to use the webpack you can delete the ``gulpfile.babel.js`` and ``.babelrc`` from the root
    directory - then the errors will stop appearing.

Learn more
----------

* `Webpack Encore Documentation <https://symfony.com/doc/current/frontend.html#webpack-encore>`_
