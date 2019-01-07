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
                    form: Sylius\Bundle\ShippingBundle\Form\Type\ShipmentType
            shipment_item:
                classes:
                    model:      Sylius\Component\Shipping\Model\ShipmentItem
                    interface:      Sylius\Component\Shipping\Model\ShipmentItemInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory:    Sylius\Component\Resource\Factory\Factory
                    form: Sylius\Bundle\ShippingBundle\Form\Type\ShipmentItemType
            shipping_method:
                classes:
                    model:      Sylius\Component\Shipping\Model\ShippingMethod
                    interface:      Sylius\Component\Shipping\Model\ShippingMethodInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory:    Sylius\Component\Resource\Factory\Factory
                    form: Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodType
                translation:
                    classes:
                        model:      Sylius\Component\Shipping\Model\ShippingMethodTranslation
                        interface:  Sylius\Component\Shipping\Model\ShippingMethodTranslationInterface
                        controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                        repository: ~
                        factory:    Sylius\Component\Resource\Factory\Factory
                        form: Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodTranslationType
            shipping_category:
                classes:
                    model:      Sylius\Component\Shipping\Model\ShippingCategory
                    interface:  Sylius\Component\Shipping\Model\ShippingCategoryInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory:    Sylius\Component\Resource\Factory\Factory
                    form: Sylius\Bundle\ShippingBundle\Form\Type\ShippingCategoryType

Tests
-----

.. code-block:: bash

    $ composer install
    $ bin/phpspec run -fpretty --verbose

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.
