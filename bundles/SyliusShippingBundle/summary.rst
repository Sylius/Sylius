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
                model: Sylius\Component\Shipping\Model\Shipment
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\ShippingBundle\Form\Type\ShipmentType
            shipment_item:
                model: Sylius\Component\Shipping\Model\ShipmentItem
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\ShippingBundle\Form\Type\ShipmentItemType
            shipping_method:
                model: Sylius\Component\Shipping\Model\ShippingMethod
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodType
            shipping_method_rule:
                model: Sylius\Component\Shipping\Model\ShippingMethodRule
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodRuleType
            shipping_method_rule:
                model: Sylius\Component\Shipping\Model\Rule
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\ShippingBundle\Form\Type\RuleType

        validation_groups:
            shipping_category: [sylius]
            shipping_method: [sylius]
            shipping_rule_item_count_configuration: [sylius]
            shipping_calculator_flat_rate_configuration: [sylius]
            shipping_calculator_per_item_rate_configuration: [sylius]
            shipping_calculator_flexible_rate_configuration: [sylius]
            shipping_calculator_weight_rate_configuration: [sylius]

Tests
-----

.. code-block:: bash

    $ composer install --dev --prefer-dist
    $ bin/phpspec run -fpretty --verbose

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.