Configuration reference
=======================

.. code-block:: yaml

    sylius_taxation:
        driver: ~ # The driver used for persistence layer.
        classes:
            tax_category:
                model: Sylius\Bundle\TaxationBundle\Model\TaxCategory
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\TaxationBundle\Form\Type\TaxCategoryType
            tax_rate:
                model: Sylius\Bundle\TaxationBundle\Model\TaxRate
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\TaxationBundle\Form\Type\TaxRateType
        validation_groups:
            tax_category: [sylius]
            tax_rate: [sylius]
