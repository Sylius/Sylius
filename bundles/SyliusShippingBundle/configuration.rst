Configuration reference
=======================

.. code-block:: yaml

    sylius_shipping:
        driver: ~ # The driver used for persistence layer.
        classes:
            shipment:
                model: Sylius\Bundle\ShippingBundle\Model\Shipment
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\ShippingBundle\Form\Type\ShipmentType
            shipment_item:
                model: Sylius\Bundle\ShippingBundle\Model\ShipmentItem
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\ShippingBundle\Form\Type\ShipmentItemType
            shipping_method:
                model: Sylius\Bundle\ShippingBundle\Model\ShippingMethod
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodType
            shipping_method_rule:
                model: Sylius\Bundle\ShippingBundle\Model\ShippingMethodRule
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodRuleType
        validation_groups:
            shipment: [sylius]
            shipment_item: [sylius]
            shipping_method: [sylius]
            shipping_method_rule: [sylius]
