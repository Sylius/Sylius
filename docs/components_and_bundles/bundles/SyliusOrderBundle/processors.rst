.. rst-class:: outdated

Processors
==========

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

Order processors are responsible of manipulating the orders to apply different predefined adjustments or other modifications based on order state.

Registering custom processors
-----------------------------

Once you have your own :ref:`component_order_processors_order-processor-interface` implementation, if services autowiring and auto-configuration are not enabled, you need to register it as a service.

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8"?>

    <container xmlns="http://symfony.com/schema/dic/services"
               xmlns:xsi="https://www.w3.org/2001/XMLSchema-instance"
               xsi:schemaLocation="http://symfony.com/schema/dic/services
                                   https://symfony.com/schema/dic/services/services-1.0.xsd">

        <services>
            <service id="app.order_processor.custom" class="App\OrderProcessor\CustomOrderProcessor">
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
    $order = $this->container->get('sylius.repository.order')->find('$id');

    // Get the processor from the container or inject the service
    $orderProcessor = $this->container->get('sylius.order_processing.order_processor');

    $orderProcessor->process($order);

.. note::

    The `CompositeOrderProcessor` is named as ` sylius.order_processing.order_processor` in the container and contains all services tagged as `sylius.order_processor`
