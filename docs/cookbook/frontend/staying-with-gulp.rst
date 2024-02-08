How to stay with Gulp after Sylius 1.12 update
==============================================

If you want to stay with Gulp (what we do not recommend), that is the guide made just for you.

.. warning::

    This guide should be used only for **existing projects**. We consider using gulp as deprecated and we do not recommend using it for new projects.

**1.** Update your dependencies version in ``package.json`` file to the latest version. You can copy the ``package.json`` content from
`Sylius/Sylius repository <https://github.com/Sylius/Sylius/blob/1.12/package.json>`_.

.. note::

    This step is required even if you do not want to use Webpack. In ``Sylius 1.12`` we have bumped all of our JS dependencies, what forced use to adjust our Gulp configs to the new versions of libraries.

**2.** Revert changes in ``package.json`` file scripts section:

.. code-block:: diff

    - "watch": "encore dev --watch",
    - "build": "encore dev",
    - "build:prod": "encore production",
    +"watch": "gulp watch",
    +"build": "gulp build",

**3.** Update ``.babelrc`` file:

.. code-block:: diff

    {
      "presets": [
    -    ["env", {
    +    ["@babel/preset-env", {
          "targets": {
            "node": "6"
    -     },
    +     }
    -      "useBuiltIns": true
        }]
      ],
      "plugins": [
    -    ["transform-object-rest-spread", {
    +    ["@babel/plugin-proposal-object-rest-spread", {
          "useBuiltIns": true
        }]
      ]
    }

**4.** Disable Webpack in ``config/packages/_sylius.yaml`` file:

.. code-block:: diff

    +sylius_ui:
    +    use_webpack: false
