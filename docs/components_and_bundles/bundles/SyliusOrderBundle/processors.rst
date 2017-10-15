Processors
==========

Order processors are responsible of manipulating the orders to apply different predefined adjustments or other modifications based on order state.

Registering custom processors
-----------------------------

Once you have your own :ref:`component_order_processors_order-processor-interface` implementation you need to register it as a service.

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8"?>

    <container xmlns="http://symfony.com/schema/dic/services"
               xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
               xsi:schemaLocation="http://symfony.com/schema/dic/services
                                   http://symfony.com/schema/dic/services/services-1.0.xsd">

        <services>
            <service id="acme.order_processor.custom" class="Acme\ShopBundle\OrderProcessor\CustomOrderProcessor">
                <tag name="sylius.order_processor" priority="0" />
            </service>
        </services>
    </container>

.. note::

    You can add your own processor to the :ref:`component_order_processors_composite_order_processor` using `sylius.order_processor`

Using CompositeOrderProcessor
-----------------------------

All processor services containing `sylius.order_processor` tag can be launched as follows:

In a controller:

.. code-block:: php

    <?php

    // Fetch order from DB
    $order = ...;

    // Get the processor from the container or inject the service
    $orderProcessor = ...;

    $orderProcessor->process($order);

.. note::

    The `CompositeOrderProcessor` is named as ` sylius.order_processing.order_processor` in the container and contains all services tagged as `sylius.order_processor`
