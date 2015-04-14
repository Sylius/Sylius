Configuration Reference
=======================

.. code-block:: yaml

    sylius_grid:
        grids:
            app_user: # Your grid name.
                driver: doctrine/orm # Data source driver.
                resource: app.user # Resource name.
                sorting:
                    name: asc
                columns:
                    name:
                        type: twig # Type of column.
                        label: Name # Label.
                        options:
                            template: :Grid/Column:_name.html.twig # Options specific for column type.
                filters:
                    group:
                        type: entity
                        label: Group
                        options:
                            entity: AppBundle:Group
                actions:
                    edit:
                        type: link
                        options:
                            route: app_user_update
                bulk_actions:
                    delete:
                        type: delete
                    copy:
                        type: copy

