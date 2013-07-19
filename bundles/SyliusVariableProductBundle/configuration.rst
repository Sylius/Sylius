Configuration reference
=======================

.. code-block:: yaml

    sylius_variable_product:
        classes:
            variant:
                model: Sylius\Bundle\VariableProductBundle\Model\Variant
                controller: Sylius\Bundle\VariableProductBundle\Controller\VariantController
                repository: ~
                form: Sylius\Bundle\VariableProductBundle\Form\Type\ProductType
            option:
                model: Sylius\Bundle\VariableProductBundle\Model\Option
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\VariableProductBundle\Form\Type\PropertyType
            option_value:
                model: Sylius\Bundle\VariableProductBundle\Model\OptionValue
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\VariableProductBundle\Form\Type\OptionValueType
        validation_groups:
            variant: [sylius]
            option: [sylius]
            option_value: [sylius]
