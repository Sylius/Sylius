Custom Bulk Action
==================

There are cases where pressing a button per item in a grid is not suitable. And there are also certain
cases when built-in bulk action types are not enough.

All you need to do is create your own bulk action template and register it for the ``sylius_grid``.

In the template we will specify the button's icon to be ``export`` and its colour to be ``orange``.

.. code-block:: twig

    {% import '@SyliusUi/Macro/buttons.html.twig' as buttons %}

    {% set path = options.link.url|default(path(options.link.route)) %}

    {{ buttons.default(path, action.label, null, 'export', 'orange') }}

Now configure the new action's template like below in the ``app/config/config.yml``:

.. code-block:: yaml

    # app/config/config.yml
    sylius_grid:
        templates:
            bulk_action:
                export: "@App/Grid/BulkAction/export.html.twig"

From now on you can use your new bulk action type in the grid configuration!

Let's assume that you already have a route for exporting by injecting ids, then you can configure the grid action:

.. code-block:: yaml

    sylius_grid:
        grids:
            app_admin_product:
                ...
                actions:
                    bulk:
                        export:
                            type: export
                            label: Export Data
                            options:
                                link:
                                    route: app_admin_product_export
                                    parameters:
                                        format: csv
