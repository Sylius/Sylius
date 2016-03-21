Calculators
===========

FlatRateCalculator
------------------

**FlatRateCalculator** class charges a flat rate per shipment.

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

.. code-block:: php

    <?

    use Sylius\Component\Shipping\Model\Shipment;
    use Sylius\Component\Shipping\Model\ShipmentItem;
    use Sylius\Component\Shipping\Calculator\VolumeRateCalculator;

    $wardrobe = new Wardrobe();

    $shipmentItem = new ShipmentItem();
    $shipmentItem->setShippable($wardrobe);

    $shipment = new Shipment();
    $shipment->addItem($shipmentItem);

    $configuration = array('amount' => 200, 'division' => 5);
    // this configuration should be defined in shipping method allowed for shipment
    $volumeRateCalculator = new VolumeRateCalculator();

    $wardrobe->setShippingVolume(100);
    $volumeRateCalculator->calculate($shipment, $configuration); // returns 4000

    $wardrobe->setShippingVolume(20);
    $volumeRateCalculator->calculate($shipment, $configuration); // returns  800

.. hint::
    To see implementation of Wardrobe class please go to :ref:`basic_usage`.

WeightRateCalculator
--------------------

**WeightRateCalculator** charges amount rate per weight.

.. code-block:: php

    <?php

    use Sylius\Component\Shipping\Model\Shipment;
    use Sylius\Component\Shipping\Model\ShipmentItem;
    use Sylius\Component\Shipping\Calculator\WeightRateCalculator;

    $configuration = array('fixed' => 200, 'variable' => 500, 'division' => 5);
    // this configuration should be defined in shipping method allowed for shipment
    $weightRateCalculator = new WeightRateCalculator();

    $wardrobe = new Wardrobe();

    $shipmentItem = new ShipmentItem();
    $shipmentItem->setShippable($wardrobe);

    $shipment = new Shipment();
    $shipment->addItem($shipmentItem);

    $wardrobe->setShippingWeight(100);
    $weightRateCalculator->calculate($shipment, $configuration); // returns 10200

    $wardrobe->setShippingWeight(10);
    $weightRateCalculator->calculate($shipment, $configuration); // returns 1200

.. hint::
    To see implementation of Wardrobe class please go to :ref:`basic_usage`.