How to configure tax rates to be based on shipping address?
===========================================================

The default configuration of Sylius tax calculation is based on billing address but there are situations where we would
like to use a shipping address to be used in this process. This may be useful to anyone who uses Sylius in European Union
as from 1st July 2021 the new taxation rules will be applied.

.. note::

    You can learn more about new EU taxation rules `here <https://ec.europa.eu/taxation_customs/business/vat/modernising-vat-cross-border-ecommerce_en>`_.

To change the way how the taxes are calculated: by billing or by shipping address, you need to declare ``OrderTaxesProcessor`` with
additional argument in your config file:

.. code-block:: yaml

    # app/config/services.yaml
        App\OrderProcessing\OrderTaxesProcessor:
        arguments:
            - '@sylius.provider.channel_based_default_zone_provider'
            - '@sylius.zone_matcher'
            - '@sylius.registry.tax_calculation_strategy'
            - '@sylius.taxation_address_resolver'
        tags:
            - { name: sylius.order_processor, priority: 10 }

And add a parameter to your config:

.. code-block:: yaml

    # app/config/packages/_sylius.yaml
    parameters:
        sylius_core.public_dir: '%kernel.project_dir%/public'
        sylius_core.taxation.shipping_address_based_taxation: false

And with this change, the way how taxes are calculated is based on shipping address.
