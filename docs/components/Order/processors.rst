Processors
==========

Order processors are responsible of manipulating the orders to apply different predefined adjustments or other modifications based on order state.

.. _component_order_processors_order-processor-interface:

OrderProcessorInterface
-----------------------

You can use it when you want to create your own custom processor.

The following code applies 10% discount adjustment to orders above 100€.

.. code-block:: php

    <?php

    use Sylius\Component\Order\Processor\OrderProcessorInterface;
    use Sylius\Component\Order\Model\OrderInterface;
    use Sylius\Component\Order\Model\Adjustment;

    class DiscountByPriceOrderProcessor implements OrderProcessorInterface
    {
        public function process(OrderInterface $order)
        {
            if($order->getTotal() > 10000) {
                $discount10Percent = new Adjustment();
                $discount10Percent->setAmount(-$order->getTotal() / 100 * 10);
                $order->addAdjustment($discount10Percent);
            }
        }
    }

.. _component_order_processors_composite_order_processor:

CompositeOrderProcessor
-----------------------

Composite order processor works as a registry of processors, allowing to run multiple processors in priority order.

