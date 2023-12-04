Processors
==========

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

.. note::

    If services autoconfiguration is enabled, you should register your own processor by adding the ``Sylius\Bundle\OrderBundle\Attribute\AsOrderProcessor`` attribute
    on the top of the processor class.

    .. code-block:: php

        <?php

        namespace App\OrderProcessor;

        use Sylius\Bundle\OrderBundle\Attribute\AsOrderProcessor;
        use Sylius\Component\Order\Model\OrderInterface;
        use Sylius\Component\Order\Processor\OrderProcessorInterface;

        #[AsOrderProcessor(priority: 10)] //priority is optional
        //#[AsOrderProcessor] can be used as well
        final class CustomOrderProcessor implements OrderProcessorInterface
        {
            public function process(OrderInterface $order): void
            {
                 // ...
            }
        }

    Then you should enable autoconfiguring with attributes in your ``config/packages/_sylius.yaml`` file:

    .. code-block:: yaml

        sylius_order:
            autoconfigure_with_attributes: true

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
