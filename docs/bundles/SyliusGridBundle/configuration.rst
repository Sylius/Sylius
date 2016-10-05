Configuration Reference
=======================

.. code-block:: yaml

    sylius_grid:
        grids:
            app_user: # Your grid name
                driver: doctrine/orm # Data source driver
                resource: app.user # Resource name
                sorting:
                    name: asc
                fields:
                    name:
                        type: twig # Type of field
                        label: Name # Label
                        path: name
                        sortable: true
                        options:
                            template: :Grid/Column:_name.html.twig # Only twig column
                filters:
                    name:
                        type: string # Type of filter
                actions:
                    main:
                        create:
                            type: create
                    item:
                        update:
                            type: update
                        delete:
                            type: delete
                        show:
                            type: show
