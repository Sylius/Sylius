.. rst-class:: outdated

Processors
==========

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

Order processors are responsible for manipulating the orders to apply different predefined adjustments or other modifications based on order state.

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
                $discount10Percent->setAmount($order->getTotal() / 100 * 10);
                $discount10Percent->setType('Percent Discount');
                // It would be good practice to set `label` but it's not mandatory
                $discount10Percent->setLabel('10% discount');
                $order->addAdjustment($discount10Percent);
            }
        }
    }

.. _component_order_processors_composite_order_processor:

CompositeOrderProcessor
-----------------------

Composite order processor works as a registry of processors, allowing to run multiple processors in priority order.

