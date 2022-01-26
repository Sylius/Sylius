How to configure tax rates to be based on shipping address?
===========================================================

The default configuration of Sylius tax calculation is based on billing address but there are situations where we would
like to use a shipping address to be used in this process. This may be useful to anyone who uses Sylius in European Union
as from 1st July 2021 the new taxation rules will be applied.

.. note::

    You can learn more about new EU taxation rules `here <https://ec.europa.eu/taxation_customs/business/vat/modernising-vat-cross-border-ecommerce_en>`_.

To change the way how the taxes are calculated; by billing or by shipping address, you need to override the service called
``OrderTaxesProcessor.php`` from ``Sylius/Component/Core/OrderProcessing``.

First let's copy code from original Processor to our service
from ``%kernel.project_dir%/vendor/sylius/sylius/src/Sylius/Component/Core/OrderProcessing/OrderTaxesProcessor.php`` to ``src/OrderProcessing/OrderTaxesProcessor.php``

Then register our new service:

.. code-block:: yaml

    # app/config/services.yaml
    App\OrderProcessing\OrderTaxesProcessor:
        arguments:
            - '@sylius.provider.channel_based_default_zone_provider'
            - '@sylius.zone_matcher'
            - '@sylius.registry.tax_calculation_strategy'
        tags:
            - { name: sylius.order_processor, priority: 10 }

Now we need to change the method ``getTaxZone`` to be using the shipping address:

.. code-block:: php

    //...
    private function getTaxZone(OrderInterface $order): ?ZoneInterface
    {
        $shippingAddress = $order->getShippingAddress();
        $zone = null;

        if (null !== $shippingAddress) {
            $zone = $this->zoneMatcher->match($shippingAddress, Scope::TAX);
        }

        return $zone ?: $this->defaultTaxZoneProvider->getZone($order);
    }
    //...

And with this change, the way how taxes are calculated will be based on shipping address.
