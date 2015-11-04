Calculators
===========

FlatRateCalculator
------------------

**FlatRateCalculator** class charges a flat rate per shipment.
It has the following configuration:

+---------------------+---------------------------------+
| Option              | Description                     |
+=====================+=================================+
| amount              | Amount of flat rate for subject |
+---------------------+---------------------------------+

.. code-block:: php

    <?php

    use Sylius\Component\Shipping\Model\Shipment;
    use Sylius\Component\Shipping\Calculator\FlatRateCalculator;
    use Sylius\Component\Shipping\Model\ShipmentItem;

    $shipmentItem = new ShipmentItem();
    $shipment = new Shipment();
    $shipment->addItem($shipmentItem);

    $flatRateCalculator = new FlatRateCalculator();
    // this configuration should be defined in shipping method allowed for shipment
    $configuration = array('amount' => 1500);

    $flatRateCalculator->calculate($shipment, $configuration); // returns 1500
    $configuration = array('amount' => 500);
    $flatRateCalculator->calculate($shipment, $configuration); // returns 500

FlexibleRateCalculator
----------------------

**FlexibleRateCalculator** calculates a shipping charge, where first item has different cost that other items.
It has the following configuration:

+-----------------------+------------------------------------------------------------+
| Option                | Description                                                |
+=======================+============================================================+
| additional_item_limit | Limit of additional items in a shipment (set default to 0) |
+-----------------------+------------------------------------------------------------+
| first_item_cost       | Cost for first item in shipment                            |
+-----------------------+------------------------------------------------------------+
| additional_item_cost  | Cost for additional items                                  |
+-----------------------+------------------------------------------------------------+

.. code-block:: php

    <?php

    use Sylius\Component\Shipping\Model\Shipment;
    use Sylius\Component\Shipping\Calculator\FlexibleRateCalculator;

    $shipment = new Shipment();
    $shipmentItem = new ShipmentItem();
    $shipmentItem2 = new ShipmentItem();
    $shipmentItem3 = new ShipmentItem();
    $shipmentItem4 = new ShipmentItem();

    // this configuration should be defined in shipping method allowed for shipment
    $configuration = array(
        'first_item_cost'       => 1000,
        'additional_item_cost'  => 200,
        'additional_item_limit' => 2
    );

    $flexibleRateCalculator = new FlexibleRateCalculator();

    $shipment->addItem($shipmentItem);
    $flexibleRateCalculator->calculate($shipment, $configuration); // returns 1000

    $shipment->addItem($shipmentItem2);
    $shipment->addItem($shipmentItem3);
    $flexibleRateCalculator->calculate($shipment, $configuration); // returns 1400

    $shipment->addItem($shipmentItem4);
    $flexibleRateCalculator->calculate($shipment, $configuration);
    // returns 1400, because additional item limit is 3

PerItemRateCalculator
---------------------

**PerItemRateCalculator** charges a flat rate per item.
It has the following configuration:

+--------+-----------------------------------+
| Option | Description                       |
+========+===================================+
| amount | Amount of flat rate for one item  |
+--------+-----------------------------------+

.. code-block:: php

    <?php

    use Sylius\Component\Shipping\Model\Shipment;
    use Sylius\Component\Shipping\Model\ShipmentItem;
    use Sylius\Component\Shipping\Calculator\PerItemRateCalculator;

    // this configuration should be defined in shipping method allowed for shipment
    $configuration = array('amount' => 200);
    $perItemRateCalculator = new PerItemRateCalculator();

    $shipment = new Shipment();
    $shipmentItem = new ShipmentItem();
    $shipmentItem2 = new ShipmentItem();

    $perItemRateCalculator->calculate($shipment, $configuration); // returns 0

    $shipment->addItem($shipmentItem);
    $perItemRateCalculator->calculate($shipment, $configuration); // returns 200

    $shipment->addItem($shipmentItem2);
    $perItemRateCalculator->calculate($shipment, $configuration); // returns 400


VolumeRateCalculator
--------------------

**VolumeRateCalculator** charges amount rate per volume.
It has the following configuration:

+----------+-----------------------------------------+
| Option   | Description                             |
+==========+=========================================+
| division | Division for volume of shippable object |
+----------+-----------------------------------------+
| amount   | Amount of flat rate for subject         |
+----------+-----------------------------------------+

.. code-block:: php

    <?

    use Sylius\Component\Shipping\Model\Shipment;
    use Sylius\Component\Shipping\Model\ShipmentItem;
    use Sylius\Component\Shipping\Calculator\VolumeRateCalculator;

    $shippable = new ShippableObject();

    $shipmentItem = new ShipmentItem();
    $shipmentItem->setShippable($shippable);

    $shipment = new Shipment();
    $shipment->addItem($shipmentItem);

    $configuration = array('amount' => 200, 'division' => 5);
    // this configuration should be defined in shipping method allowed for shipment
    $volumeRateCalculator = new VolumeRateCalculator();

    shippable->setShippingVolume(100);
    $volumeRateCalculator->calculate($shipment, $configuration); // returns 4000

    $shippable->setShippingVolume(20);
    $volumeRateCalculator->calculate($shipment, $configuration); // returns  800

WeightRateCalculator
--------------------

**WeightRateCalculator** charges amount rate per weight.
It has the following configuration:

+----------+-------------------------------------------+
| Option   | Description                               |
+==========+===========================================+
| division | Division for weight of shippable object   |
+----------+-------------------------------------------+
| fixed    | Fixed rate for subject (set default to 0) |
+----------+-------------------------------------------+
| variable | Rate for some weight                      |
+----------+-------------------------------------------+

.. code-block:: php

    <?php

    use Sylius\Component\Shipping\Model\Shipment;
    use Sylius\Component\Shipping\Model\ShipmentItem;
    use Sylius\Component\Shipping\Calculator\WeightRateCalculator;

    $configuration = array('fixed' => 200, 'variable' => 500, 'division' => 5);
    // this configuration should be defined in shipping method allowed for shipment
    $weightRateCalculator = new WeightRateCalculator();

    $shippable = new ShippableObject();

    $shipmentItem = new ShipmentItem();
    $shipmentItem->setShippable($shippable);

    $shipment = new Shipment();
    $shipment->addItem($shipmentItem);

    $shippable->setShippingWeight(100);
    $weightRateCalculator->calculate($shipment, $configuration); // returns 10200

    $shippable->setShippingWeight(10);
    $weightRateCalculator->calculate($shipment, $configuration); // returns 1200

CalculatorRegistry
------------------

 This service keeps all calculators registered inside container. Allows to retrieve them by name.

.. code-block:: php

    <?

    use Sylius\Component\Shipping\Calculator\DefaultCalculators;
    use Sylius\Component\Shipping\Calculator\FlexibleRateCalculator;
    use Sylius\Component\Shipping\Calculator\Registry\CalculatorRegistry;

    $flexibleRateCalculator = new FlexibleRateCalculator();

    $calculatorRegistry = new CalculatorRegistry();
    $calculatorRegistry->registerCalculator(DefaultCalculators::FLEXIBLE_RATE, $flexibleRateCalculator);
    $calculatorRegistry->hasCalculator(DefaultCalculators::FLAT_RATE); // return false
    $calculatorRegistry->getCalculator(DefaultCalculators::FLEXIBLE_RATE); // returns $flexibleRateCalculator
    $calculatorRegistry->getCalculators(); // returns collection of calculators
    $calculatorRegistry->unregisterCalculator(DefaultCalculators::FLEXIBLE_RATE);

.. caution::
    The method ``->registerCalculator()`` throws `ExistingCalculatorException`_  when calculator with given name already exists.
    The method ``->unregisterCalculator`` throws `NonExistingCalculatorException`_ when  calculator with given name does not exist.
      All of above exceptions extends the `PHP InvalidArgumentException`_.

.. note::
    This model implements the :ref:`component_shipping_calculator_registry-shipping-method-eligibility-checker-interface`. |br|
    For more detailed information go to `Sylius API CalculatorRegistry`_.

.. _Sylius API CalculatorRegistry: http://api.sylius.org/Sylius/Component/Shipping/Calculator/Registry/CalculatorRegistry.html
.. _ExistingCalculatorException: http://api.sylius.org/Sylius/Component/Shipping/Calculator/Registry/ExistingCalculatorException.html
.. _NonExistingCalculatorException: http://api.sylius.org/Sylius/Component/Shipping/Calculator/Registry/NonExistingCalculatorException.html
.. _PHP InvalidArgumentException: http://php.net/manual/en/class.invalidargumentexception.php
