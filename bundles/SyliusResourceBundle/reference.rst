Configuration Reference
=======================

.. code-block:: yaml

    sylius_resource:
        resources:
            app.book
                driver: doctrine/orm
                classes:
                    model: # Required!
                    interface: ~
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory: Sylius\Component\Resource\Factory\Factory
                    form:
                        default: ~
                        choice: ~
                        foo: ~
                        bar: ~
                options:
                    object_manager: default
                validation_groups:
                    default: [sylius]
                translation:
                    classes:
                        model: ~
                        interface: ~
                        controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                        repository: ~
                        factory: Sylius\Component\Resource\Factory\Factory
                        form:
                            default: ~
                            choice: ~
                            foo: ~
                            bar: ~
                    validation_groups:
                        default: [sylius]

Routing Generator Configuration Reference
-----------------------------------------

.. code-block:: yaml

    app_book:
        resource: |
            alias: app.book
            path: library
            section: admin
            templates: :Book
            form: app_book_simple
            redirect: create
            except: ['show']
            only: ['create', 'index']
        type: sylius.resource

