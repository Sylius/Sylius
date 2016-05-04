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
                    model:      Sylius\Component\Product\Model\Product
                    interface:  Sylius\Component\Product\Model\ProductInterface
                    controller: Sylius\Bundle\ProductBundle\Controller\ProductController
                    repository: ~
                    factory:    Sylius\Component\Resource\Factory\Factory
                    form:
                        default: Sylius\Bundle\ProductBundle\Form\Type\ProductType
                        choice:  Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType
                validation_groups:
                    default: [ sylius ]
                translation:
                    classes:
                        model:      Sylius\Component\Product\Model\ProductTranslation
                        interface:  Sylius\Component\Product\Model\ProductTranslationInterface
                        controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                        repository: ~
                        factory:    Sylius\Component\Resource\Factory\Factory
                        form:
                            default: Sylius\Bundle\ProductBundle\Form\Type\Product\TranslationType
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
