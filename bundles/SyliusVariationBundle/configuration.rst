Configuration reference
=======================

.. code-block:: yaml

    sylius_variation:
          driver: ~ # The driver used for persistence layer. Currently only `doctrine/orm` is supported.
          classes:
              # `variation_name` can be any name, for example `product`, `ad`, or `blog_post`
              variation_name:
                  variable: ~ # Required: The variable model class implementing `VariableInterface`
                              # of which variants can be created from
                  variant:
                      model:      ~ # Required: The variant model class implementing `VariantInterface`
                      repository: ~ # Required: The repository class for the variant
                      controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                      form:       Sylius\Bundle\VariationBundle\Form\Type\VariantType
                  option:
                      model:      ~ # Required: The option model class implementing `OptionInterface`
                      repository: ~ # Required: The repository class for the option
                      controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                      form:       Sylius\Bundle\VariationBundle\Form\Type\OptionType
                  option_value:
                      model:      ~ # Required: The option value model class implementing `OptionValueInterface`
                      repository: ~ # Required: The repository class for the option value
                      controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                      form:       Sylius\Bundle\VariationBundle\Form\Type\OptionValueType
          validation_groups:
              # `variation_name` should be same name as the name key defined for the classes section above.
              variation_name:
                  variant:      [ sylius ]
                  option:       [ sylius ]
                  option_value: [ sylius ]
