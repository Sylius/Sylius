Summary
=======

Configuration Reference
-----------------------

.. code-block:: yaml

    sylius_taxation:
        # The driver used for persistence layer.
        driver: ~
        resources:
            tax_category:
                classes:
                    model: Sylius\Taxation\Model\TaxCategory
                    interface: Sylius\Taxation\Model\TaxCategoryInterface
                    controller: Sylius\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory:    Sylius\Resource\Factory\Factory
                    form:
                        default: Sylius\TaxationBundle\Form\Type\TaxCategoryType
                        choice: Sylius\ResourceBundle\Form\Type\ResourceChoiceType
                validation_groups:
                    default: [ sylius ]
            tax_rate:
                classes:
                    model: Sylius\Taxation\Model\TaxRate
                    interface: Sylius\Taxation\Model\TaxRateInterface
                    controller: Sylius\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory:    Sylius\Resource\Factory\Factory
                    form:
                        default: Sylius\TaxationBundle\Form\Type\TaxRateType
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
