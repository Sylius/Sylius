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
                options:
                    object_manager: default
                translation:
                    classes:
                        model: ~
                        interface: ~
                        controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                        repository: ~
                        factory: Sylius\Component\Resource\Factory\Factory

Routing Generator Configuration Reference
-----------------------------------------

.. code-block:: yaml

    app_book:
        resource: |
            alias: app.book
            path: library
            section: admin
            templates: :Book
            form: AppBunle/Form/Type/SimpleBookType
            redirect: create
            except: ['show']
            only: ['create', 'index']
        type: sylius.resource

