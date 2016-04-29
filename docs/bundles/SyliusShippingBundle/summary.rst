Summary
=======

Configuration Reference
-----------------------

.. code-block:: yaml

    sylius_shipping:
        # The driver used for persistence layer.
        driver: ~
        classes:
            shipment:
                classes:
                    model:      Sylius\Component\Shipping\Model\Shipment
                    interface:      Sylius\Component\Shipping\Model\ShipmentInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory:    Sylius\Component\Resource\Factory\Factory
                    form:
                        default: Sylius\Bundle\ShippingBundle\Form\Type\ShipmentType
                validation_groups:
                    default: [ sylius ]
            shipment_item:
                classes:
                    model:      Sylius\Component\Shipping\Model\ShipmentItem
                    interface:      Sylius\Component\Shipping\Model\ShipmentItemInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory:    Sylius\Component\Resource\Factory\Factory
                    form:
                        default: Sylius\Bundle\ShippingBundle\Form\Type\ShipmentItemType
            shipping_method:
                classes:
                    model:      Sylius\Component\Shipping\Model\ShippingMethod
                    interface:      Sylius\Component\Shipping\Model\ShippingMethodInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory:    Sylius\Component\Resource\Factory\Factory
                    form:
                        default: Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodType
                        choice:  Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType
                validation_groups:
                    default: [ sylius ]
                translation:
                    classes:
                        model:      Sylius\Component\Shipping\Model\ShippingMethodTranslation
                        interface:  Sylius\Component\Shipping\Model\ShippingMethodTranslationInterface
                        controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                        repository: ~
                        factory:    Sylius\Component\Resource\Factory\Factory
                        form:
                            default: Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodTranslationType
                    validation_groups:
                        default: [ sylius ]
            shipping_category:
                classes:
                    model:      Sylius\Component\Shipping\Model\ShippingCategory
                    interface:  Sylius\Component\Shipping\Model\ShippingCategoryInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory:    Sylius\Component\Resource\Factory\Factory
                    form:
                        default: Sylius\Bundle\ShippingBundle\Form\Type\ShippingCategoryType
                        choice:  Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType
                validation_groups:
                    default: [ sylius ]
            shipping_method_rule:
                classes:
                    model:      Sylius\Component\Shipping\Model\Rule
                    interface:  Sylius\Component\Shipping\Model\RuleInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory:    Sylius\Component\Resource\Factory\Factory
                    form:
                        default: Sylius\Bundle\ShippingBundle\Form\Type\RuleType
                validation_groups:
                    default: [ sylius ]

        validation_groups:
            shipping_rule_item_count_configuration: [sylius]
            shipping_calculator_flat_rate_configuration: [sylius]
            shipping_calculator_per_item_rate_configuration: [sylius]
            shipping_calculator_flexible_rate_configuration: [sylius]
            shipping_calculator_weight_rate_configuration: [sylius]
            shipping_calculator_volume_rate_configuration: [sylius]

Tests
-----

.. code-block:: bash

    $ composer install
    $ bin/phpspec run -fpretty --verbose

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.
