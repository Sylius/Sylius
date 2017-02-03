Configuration Reference
=======================

Here you will find all configuration options of ``sylius_grid``.

.. code-block:: yaml

    sylius_grid:
        grids:
            app_user: # Your grid name
                driver:
                    name: doctrine/orm
                    options:
                        class: "%app.model.user%"
                sorting:
                    name: asc
                limits: [10, 25, 50, 100]
                fields:
                    name:
                        type: twig # Type of field
                        label: Name # Label
                        path: . # dot means a whole object
                        sortable: true
                        position: 100
                        options:
                            template: :Grid/Column:_name.html.twig # Only twig column
                            vars:
                                labels: # a template of how does the label look like
                        enabled: true
                filters:
                    name:
                        type: string # Type of filter
                        label: app.ui.name
                        enabled: true
                        template: ~
                        position: 100
                        options:
                            fields: { }
                        form_options: { }
                        default_value: ~
                    enabled:
                        type: boolean # Type of filter
                        label: app.ui.enabled
                        enabled: true
                        template: ~
                        position: 100
                        options:
                            field: enabled
                        form_options: { }
                        default_value: ~
                    date:
                        type: date # Type of filter
                        label: app.ui.created_at
                        enabled: true
                        template: ~
                        position: 100
                        options:
                            field: createdAt
                        form_options: { }
                        default_value: ~
                    channel:
                        type: entity # Type of filter
                        label: app.ui.channel
                        enabled: true
                        template: ~
                        position: 100
                        options:
                            fields: [channel]
                        form_options:
                            class: "%app.model.channel%"
                        default_value: ~
                actions:
                    main:
                        create:
                            type: create
                            label: sylius.ui.create
                            enabled: true
                            icon: ~
                            position: 100
                            options: { }
                    item:
                        update:
                            type: update
                            label: sylius.ui.edit
                            enabled: true
                            icon: ~
                            position: 100
                            options: { }
                        delete:
                            type: delete
                            label: sylius.ui.delete
                            enabled: true
                            icon: ~
                            position: 100
                            options: { }
                        show:
                            type: show
                            label: sylius.ui.show
                            enabled: true
                            icon: ~
                            position: 100
                            options: { }
                        archive:
                            type: archive
                            label: sylius.ui.archive
                            enabled: true
                            icon: ~
                            position: 100
                            options:
                                restore_label: sylius.ui.restore
