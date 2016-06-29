Summary
=======

Configuration reference
-----------------------

.. code-block:: yaml

    sylius_product:
        driver: ~ # The driver used for persistence layer.
        engine: twig # Templating engine to use by default.
        resources:
            product:
                classes:
                    model:      Sylius\Product\Model\Product
                    interface:  Sylius\Product\Model\ProductInterface
                    controller: Sylius\ProductBundle\Controller\ProductController
                    repository: ~
                    factory:    Sylius\Resource\Factory\Factory
                    form:
                        default: Sylius\ProductBundle\Form\Type\ProductType
                        choice:  Sylius\ResourceBundle\Form\Type\ResourceChoiceType
                validation_groups:
                    default: [ sylius ]
                translation:
                    classes:
                        model:      Sylius\Product\Model\ProductTranslation
                        interface:  Sylius\Product\Model\ProductTranslationInterface
                        controller: Sylius\ResourceBundle\Controller\ResourceController
                        repository: ~
                        factory:    Sylius\Resource\Factory\Factory
                        form:
                            default: Sylius\ProductBundle\Form\Type\Product\TranslationType
                    validation_groups:
                        default: [ sylius ]

Tests
-----

.. code-block:: bash

    $ composer install
    $ bin/phpspec run -fpretty --verbose

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.
