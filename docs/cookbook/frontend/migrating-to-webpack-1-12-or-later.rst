How to migrate from Gulp to Webpack (Sylius 1.12 or later)
==========================================================

Now we will walk through the process of migrating your project from Gulp to Webpack.

.. note::

    This guide assumes your project is using Gulp as a build tool and you are using Sylius 1.12 or later.
    If you are using Sylius 1.11 or earlier check our :doc:`/cookbook/frontend/migrating-to-webpack-1-11-or-earlier` guide.

.. warning::

    Keep in mind you might have to adjust some steps to your project needs. Every project is different, and we are not able
    to provide a universal solution for every case.

**1.** Update your dependencies version in ``package.json`` file to the latest version. You can copy the ``package.json`` content from
`Sylius/Sylius repository <https://github.com/Sylius/Sylius/blob/1.12/package.json>`_.

**2.** Remove the following files from you project:

* ``.babelrc``
* ``gulpfile.babel.js``
* ``yarn.lock``
* ``public/assets``
* ``node_modules``

**3.** Create a ``webpack.config.js`` file (or if you already have existing one, replace its content) using `webpack.config.js in the Sylius/Sylius-Standard repository <https://github.com/Sylius/Sylius-Standard/blob/1.12/webpack.config.js>`_ as a reference.

**4.** Create assets directory with the following structure:

.. code-block:: text

    <project_root>/
    ├── assets/
    │   ├── admin/
    │   │   ├── entry.js <- this file can be empty for now
    │   ├── shop/
    │   │   ├── entry.js <- this file can be empty for now

**5a.** Create or replace ``config/packages/assets.yaml`` with the following configuration:

.. code-block:: yaml

    framework:
        assets:
            packages:
                admin:
                    json_manifest_path: '%kernel.project_dir%/public/build/admin/manifest.json'
                shop:
                    json_manifest_path: '%kernel.project_dir%/public/build/shop/manifest.json'
                app.admin:
                    json_manifest_path: '%kernel.project_dir%/public/build/app/admin/manifest.json'
                app.shop:
                    json_manifest_path: '%kernel.project_dir%/public/build/app/shop/manifest.json'

**5b.** Create or replace ``config/packages/webpack_encore.yaml`` with the following configuration:

.. code-block:: yaml

    webpack_encore:
        output_path: '%kernel.project_dir%/public/build/default'
        builds:
            admin: '%kernel.project_dir%/public/build/admin'
            shop: '%kernel.project_dir%/public/build/shop'
            app.admin: '%kernel.project_dir%/public/build/app/admin'
            app.shop: '%kernel.project_dir%/public/build/app/shop'

**6a.** Create or override ``templates/bundles/SyliusAdminBundle/_scripts.html.twig`` template with the following content:

.. code-block:: twig

    {{ encore_entry_script_tags('admin-entry', null, 'admin') }}
    {{ encore_entry_script_tags('app-admin-entry', null, 'app.admin') }}

**6b.** Create or override ``templates/bundles/SyliusAdminBundle/_styles.html.twig`` template with the following content:

.. code-block:: twig

    {{ encore_entry_link_tags('admin-entry', null, 'admin') }}
    {{ encore_entry_link_tags('app-admin-entry', null, 'app.admin') }}

**6c.** Create or override ``templates/bundles/SyliusShopBundle/_scripts.html.twig`` template with the following content:

.. code-block:: twig

    {{ encore_entry_script_tags('shop-entry', null, 'shop') }}
    {{ encore_entry_script_tags('app-shop-entry', null, 'app.shop') }}

**6d.** Create or override ``templates/bundles/SyliusShopBundle/_styles.html.twig`` template with the following content:

.. code-block:: twig

    {{ encore_entry_link_tags('shop-entry', null, 'shop') }}
    {{ encore_entry_link_tags('app-shop-entry', null, 'app.shop') }}

.. warning::

    Files mentioned above are the most common ones that need to be overridden. Keep in mind, across your project you might
    have other files using the old paths. You will have to find and adjust them manually.

**7.** Run the following commands:

.. code-block:: bash

    yarn install
    yarn build

**8.** If you have the following entry in your ``config/packages/_sylius.yaml`` file (available from ``Sylius 1.12``) remove it:

.. code-block:: yaml

    sylius_ui:
        use_webpack: false

Remove it or change its value to ``true``.

**9.** If you are using GitHub Actions or any other CI tool, make sure your workflow is using ``yarn build`` or ``yarn build:prod`` command.
