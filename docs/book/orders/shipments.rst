.. index::
   single: Shipments

Shipments
=========

A **Shipment** is a representation of a shipping request for an Order. Sylius can attach multiple shipments to each single Order.
Shipment consists of **ShipmentUnits**, which are a representation of OrderItemUnits from its Order.

How is a Shipment created for an Order?
---------------------------------------

.. warning::

    Read more about creating :doc:`Orders </book/orders/orders>` where the process of assigning Shipments is clarified.

.. rst-class:: plus-doc

Splitting shipments
~~~~~~~~~~~~~~~~~~~

As mentioned in the beginning Sylius Order holds a collection of Shipments. In Sylius Plus edition Orders can be
fulfilled partially, therefore it is possible to split the default Order's shipment.

To do it Sylius Plus provides a UI, where you can choose which items from the initial shipments you'd like to extract to
a new split shipment and send it (providing a tracking code or not). Shipments of an Order can be split as long as
there remains one shipment in state ``ready``.

.. image:: ../../_images/sylius_plus/banner.png
    :align: center
    :target: https://sylius.com/plus/?utm_source=docs

The Shipment State Machine
--------------------------

A Shipment that is attached to an Order will have its own state machine with the following states available:
``cart``, ``ready``, ``cancelled``, ``shipped``.

The allowed transitions between these states are:

.. code-block:: yaml

   transitions:
        create:
            from: [cart]
            to: ready
        ship:
            from: [ready]
            to: shipped
        cancel:
            from: [ready]
            to: cancelled

.. image:: ../../_images/sylius_shipment.png
    :align: center
    :scale: 70%

Shipping Methods
----------------

**ShippingMethod** in Sylius is an entity that represents the way an order can be shipped to a customer.

How to create a ShippingMethod programmatically?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

As usual use a factory to create a new ShippingMethod. Give it a ``code``, set a desired shipping calculator and set a ``zone``.
It also need a configuration, for instance of the amount (cost).
At the end add it to the system using a repository.

.. code-block:: php

    $shippingMethod = $this->container->get('sylius.factory.shipping_method')->createNew();

    $shippingMethod->setCode('DHL');
    $shippingMethod->setCalculator(DefaultCalculators::FLAT_RATE);
    $shippingMethod->setConfiguration(['channel_code' => ['amount' => 50]]);

    $zone = $this->container->get('sylius.repository.zone')->findOneByCode('US');
    $shippingMethod->setZone($zone);

    $this->container->get('sylius.repository.shipping_method')->add($shippingMethod);

In order to have your shipping method available in checkout add it to a desired channel.

.. code-block:: php

    $channel = $this->container->get('sylius.repository.channel')->findOneByCode('channel_code');
    $channel->addShippingMethod($shippingMethod);

Shipping method rules
'''''''''''''''''''''

The shipping method **Rules** restrict in what circumstances a shipping method is available.
An appropriate **RuleChecker** (each Rule type has its own RuleChecker) may check if:

* All products belong to a certain taxon
* The order total is greater than a given amount
* The total weight is below a given number
* The total volume is below a given value

And many more similar, suitable to your needs.

Rule Types
''''''''''

The types of rules that are configured in **Sylius** by default are:

* **Items total greater than or equal** - checks if the items total is greater than or equal to a given amount
* **Items total less than or equal** - checks if the items total is less than or equal to a given amount
* **Total weight greater than or equal** - checks if the total weight of the order is greater than or equal to a given number
* **Total weight less than or equal** - checks if the total weight of the order is less than or equal to a given number

Shipping Zones
--------------

Sylius has an approach of **Zones** used also for shipping. As in each e-commerce you may be willing to ship only to certain countries for example.
Therefore while configuring your **ShippingMethods** pay special attention to the zones you are assigning to them.
You have to prepare methods for each zone, because the available methods are retrieved for the zone the customer has basing on his address.

Shipping Cost Calculators
-------------------------

The shipping cost calculators are services that are used to calculate the cost for a given shipment.

The `CalculatorInterface <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Component/Shipping/Calculator/CalculatorInterface.php>`_
has a method ``calculate()`` that takes object with a configuration and returns *integer* that is the cost of shipping for that subject.
It also has a ``getType()`` method that works just like in the forms.

To select a proper service we have a one that decides for us
- the `DelegatingCalculator <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Component/Shipping/Calculator/DelegatingCalculator.php>`_.
Basing on the **ShippingMethod** assigned on the Shipment it will get its calculator type and configuration and calculate the cost properly.

.. code-block:: php

    $shippingCalculator = $this->container->get('sylius.shipping_calculator');

    $cost = $shippingCalculator->calculate($shipment);

Built-in Calculators
--------------------

The already defined calculators in Sylius are described as constants in the
`Sylius\Component\Shipping\Calculator\DefaultCalculators <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Component/Shipping/Calculator/DefaultCalculators.php>`_

* **FlatRateCalculator** - just returns the ``amount`` from the ShippingMethod's configuration.
* **PerUnitRateCalculator** - returns the ``amount`` from the ShippingMethod's configuration multiplied by the ``units`` count.

Shipment complete events
------------------------

There are two events that are triggered on the shipment ``ship`` action:

+-------------------------------------+
| Event id                            |
+=====================================+
| ``sylius.shipment.pre_ship``        |
+-------------------------------------+
| ``sylius.shipment.post_ship``       |
+-------------------------------------+

Learn more
----------

* :doc:`Shipping - Component Documentation </components_and_bundles/components/Shipping/index>`
* :doc:`How to create a custom shipping method rule? </cookbook/shipping-methods/custom-shipping-method-rule>`
