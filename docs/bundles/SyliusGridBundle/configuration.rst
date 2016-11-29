Configuration Reference
=======================

Here you will find all configuration options of ``sylius_grid``.

.. code-block:: yaml

    sylius_grid:
        grids:
            app_user: # Your grid name
                driver:
                    name: doctrine/orm # Data source driver
                    options:
                        class: AppBundle\Entity\user
                resource: app.user # Resource name
                sorting:
                    name:
                        path: name
                        direction: asc
                fields:
                    name:
                        type: twig # Type of field
                        label: Name # Label
                        path: . # dot means a whole object
                        sortable: true
                        options:
                            template: :Grid/Column:_name.html.twig # Only twig column
                            vars:
                                labels: # a template of how does the label look like
                        enabled: true
                filters:
                    name:
                        type: string # Type of filter
                        label: app.ui.name
                        options: { }
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
