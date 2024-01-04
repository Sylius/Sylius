Theming with BootstrapTheme
---------------------------

This tutorial will guide you on how to create your own theme based on `BootstrapTheme <https://github.com/Sylius/BootstrapTheme>`_ using Webpack.

Tutorial is divided into 3 parts:

1. :ref:`Creating a new theme based on BootstrapTheme <creating-a-new-theme-based-on-bootstraptheme>`
2. :ref:`Webpack Encore configuration <webpack-encore-configuration>`
3. :ref:`Customization <customization>`

.. _creating-a-new-theme-based-on-bootstraptheme:

1. Creating a new theme based on BootstrapTheme
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Install BootstrapTheme

.. code-block:: bash

    composer require sylius/bootstrap-theme

In the ``config/packages/_sylius.yaml`` file, add the path to the installed package

.. code-block:: yaml

    sylius_theme:
        sources:
            filesystem:
                directories:
                    - "%kernel.project_dir%/vendor/sylius/bootstrap-theme"
                    - "%kernel.project_dir%/themes"

Create your custom theme based on BootstrapTheme. In the ``themes`` directory, create a new folder
- name it as you like, e.g. ``BootstrapChildTheme`` and create ``composer.json`` with basic information

.. code-block:: JSON

    {
        "name": "acme/bootstrap-child-theme",
        "description": "Bootstrap child theme",
        "license": "MIT",
        "authors": [
            {
                "name": "James Potter",
                "email": "prongs@example.com"
            }
        ],
        "extra": {
            "sylius-theme": {
                "title": "Bootstrap child theme",
                "parents": [ "sylius/bootstrap-theme" ]
            }
        }
    }

Now you can go to the channel settings in the admin panel and select the created theme as default.

.. _webpack-encore-configuration:

2. Webpack Encore configuration
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

You need to prepare a new theme for working with webpack and include it in the build process.

Install missing BootstrapTheme dependencies

.. code-block:: bash

    yarn add sass-loader@^7.0.0 node-sass lodash.throttle -D
    yarn add bootstrap bootstrap.native glightbox axios form-serialize @fortawesome/fontawesome-svg-core @fortawesome/free-brands-svg-icons @fortawesome/free-regular-svg-icons @fortawesome/free-solid-svg-icons

in ``theme/BootstrapChildTheme/assets`` create 2 files: ``entry.js`` and ``scss/index.scss``

``entry.js`` is the main file for your theme. All files used in the theme will be imported here.
First, add the files used in the BootstrapTheme and your newly created scss file

.. code-block:: javascript

    import '../../../vendor/sylius/bootstrap-theme/assets/js/index';
    import './scss/index.scss';
    import '../../../vendor/sylius/bootstrap-theme/assets/media/sylius-logo.png';
    import '../../../vendor/sylius/bootstrap-theme/assets/js/fontawesome';

``index.scss`` is the main file for styles, import styles used in the BootstrapTheme

.. code-block:: css

    @import '../../../../vendor/sylius/bootstrap-theme/assets/scss/index';

In the ``webpack.config.js`` file, add configurations for the new theme

.. code-block:: javascript

    Encore.reset();
    Encore
      .setOutputPath('public/bootstrap-theme')
      .setPublicPath('/bootstrap-theme')
      .addEntry('app', './themes/BootstrapChildTheme/assets/entry.js')
      .disableSingleRuntimeChunk()
      .cleanupOutputBeforeBuild()
      .enableSassLoader()
      .enableSourceMaps(!Encore.isProduction())
      .enableVersioning(Encore.isProduction());

    const bootstrapThemeConfig = Encore.getWebpackConfig();
    bootstrapThemeConfig.name = 'bootstrapTheme';

Also add ``bootstrapThemeConfig`` to export at the end of the file.

In the app config, add paths where the compiled files will be located:

In the ``config/packages/assets.yaml`` add:

.. code-block:: yaml

    framework:
        assets:
            packages:
                bootstrapTheme:
                    json_manifest_path: '%kernel.project_dir%/public/bootstrap-theme/manifest.json'

in the ``config/packages/webpack_encore.yaml`` add:

.. code-block:: yaml

    webpack_encore:
        output_path: '%kernel.project_dir%/public/build/default'
        builds:
            bootstrapTheme: '%kernel.project_dir%/public/bootstrap-theme'

finally in the ``config/packages/_sylius.yaml`` add:

.. code-block:: yaml

    sylius_theme:
        legacy_mode: true # for sylius 1.9, 1.10, 1.11, 1.12

Now you can use one of the commands ``yarn encore dev``, ``yarn encore production`` or ``yarn encore dev-server``
to compile all assets. Open the page - everything should work.

.. _customization:

3. Customization
^^^^^^^^^^^^^^^^

Changing styles
~~~~~~~~~~~~~~~

To add new styles, create a new scss file in your theme's ``assets`` folder, and then import it into the
``index.scss``. After compilation, new styles should appear on the page.

You can also override the default styles used in BootstrapTheme by changing some variables. To do that,
create a file ``_variables.scss`` in the ``assets`` folder, change e.g. primary color by typing
``$primary: blue;``, and then import this file into ``index.scss``.

.. tip::

    Variables should be overwritten before importing styles from BootstrapTheme, so the ``_variables.scss``
    file should be imported at the beginning of the ``index.scss`` file.

Adding new assets
~~~~~~~~~~~~~~~~~

To add new assets to the theme, such as scripts or images, simply place them in your theme's directory
and then import them into the file ``entry.js``

Overwriting templates
~~~~~~~~~~~~~~~~~~~~~

To overwrite the template, copy the selected twig file from BootstrapTheme and paste it into the same place
in your theme. For example, if you want to change something in the ``layout.html.twig`` file,
copy it to ``themes/BootstrapChildTheme/templates/bundles/SyliusShopBundle``
