.. rst-class:: outdated

Summary
=======

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

Configuration Reference
-----------------------

.. code-block:: yaml

    sylius_taxation:
        # The driver used for persistence layer.
        driver: ~
        resources:
            tax_category:
                classes:
                    model:      Sylius\Component\Taxation\Model\TaxCategory
                    interface:  Sylius\Component\Taxation\Model\TaxCategoryInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory:    Sylius\Component\Resource\Factory\Factory
                    form:       Sylius\Bundle\TaxationBundle\Form\Type\TaxCategoryType
            tax_rate:
                classes:
                    model:      Sylius\Component\Taxation\Model\TaxRate
                    interface:  Sylius\Component\Taxation\Model\TaxRateInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory:    Sylius\Component\Resource\Factory\Factory
                    form:       Sylius\Bundle\TaxationBundle\Form\Type\TaxRateType

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.
