.. rst-class:: outdated

Calculating shipping cost
=========================

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

Calculating shipping cost is as simple as using the ``sylius.shipping_calculator`` service and calling ``calculate`` method on ``ShippingSubjectInterface``.

Let's calculate the cost of existing shipment.

.. code-block:: php

    public function myAction()
    {
        $calculator = $this->get('sylius.shipping_calculator');
        $shipment = $this->get('sylius.repository.shipment')->find(5);

        echo $calculator->calculate($shipment); // Returns price in cents. (integer)
    }

What has happened?

* The delegating calculator gets the **ShippingMethod** from the **ShippingSubjectInterface** (Shipment).
* Appropriate **Calculator** instance is loaded, based on the **ShippingMethod.calculator** parameter.
* The ``calculate(ShippingSubjectInterface, array $configuration)`` is called, where configuration is taken from **ShippingMethod.configuration** attribute.
