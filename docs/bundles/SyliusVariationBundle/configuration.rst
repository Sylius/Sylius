Configuration reference
=======================

.. code-block:: yaml

    sylius_variation:
        driver: ~ # The driver used for persistence layer. Currently only `doctrine/orm` is supported.
        resources:
            # `variation_name` can be any name, for example `product`, `ad`, or `blog_post`
            variation_name:
                variable: ~ # Required: The variable model class implementing `VariableInterface`
                          # of which variants can be created from
                variant:
                    classes:
                        model:      ~ # Required: The variant model class implementing `VariantInterface`
                        interface:  ~
                        controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                        repository: ~ # Required: The repository class for the variant
                        factory:    Sylius\Component\Resource\Factory\Factory
                        form:
                            default: Sylius\Bundle\VariationBundle\Form\Type\VariantType
                    validation_groups:
                        default: [ sylius ]
                option:
                    classes:
                        model:      ~ # Required: The option model class implementing `OptionInterface`
                        interface:  ~
                        repository: ~ # Required: The repository class for the option
                        controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                        factory:    Sylius\Component\Resource\Factory\Factory
                        form:
                            default: Sylius\Bundle\VariationBundle\Form\Type\OptionType
                            choice:
                    validation_groups:
                        default: [ sylius ]
                    translation:
                        classes:
                            model:      Sylius\Component\Variation\Model\OptionTranslation
                            interface:  Sylius\Component\Variation\Model\OptionTranslationInterface
                            controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                            repository: ~ # Required: The repository class for the option
                            factory:    Sylius\Component\Resource\Factory\Factory
                            form:
                                default: Sylius\Bundle\VariationBundle\Form\Type\OptionTranslationType
                        validation_groups:
                            default: [ sylius ]
                        fields:
                            default: [ presentation ]
                option_value:
                    model:      ~ # Required: The option value model class implementing `OptionValueInterface`
                    interface:  ~
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~ # Required: The repository class for the option value
                    factory:    Sylius\Component\Resource\Factory\Factory
                    form:
                        default: Sylius\Bundle\VariationBundle\Form\Type\OptionValueType
