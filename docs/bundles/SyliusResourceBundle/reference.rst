Configuration Reference
=======================

.. code-block:: yaml

    sylius_resource:
        resources:
            app.book:
                driver: doctrine/orm
                classes:
                    model: # Required!
                    interface: ~
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory: Sylius\Component\Resource\Factory\Factory
                    form: Sylius\Bundle\ResourceBundle\Form\Type\DefaultResourceType
                        validation_groups: [sylius]
                options:
                    object_manager: default
                templates:
                    form: Book/_form.html.twig
                translation:
                    classes:
                        model: ~
                        interface: ~
                        controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                        repository: ~
                        factory: Sylius\Component\Resource\Factory\Factory
                        form: Sylius\Bundle\ResourceBundle\Form\Type\DefaultResourceType
                            validation_groups: [sylius]
                    templates:
                        form: Book/Translation/_form.html.twig
                    options: ~


Routing Generator Configuration Reference
-----------------------------------------

.. code-block:: yaml

    app_book:
        resource: |
            alias: app.book
            path: library
            identifier: code
            criteria:
                code: $code
            section: admin
            templates: :Book
            form: AppBundle/Form/Type/SimpleBookType
            redirect: create
            except: ['show']
            only: ['create', 'index']
            serialization_version: 1
        type: sylius.resource

