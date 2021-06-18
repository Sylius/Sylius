How to configure tax rates to be based on shipping address?
===========================================================

The default configuration of Sylius tax calculation is based on billing address but there are situations where we would
like to use a shipping address to be used in this process. This could become necessary for anyone who uses Sylius in European Union
as from 1st July 2021 the new taxation rules will be applied.

.. note::

    You can learn more about new EU taxation rules `here <https://ec.europa.eu/taxation_customs/business/vat/modernising-vat-cross-border-ecommerce_en>`_.

To change the way how the taxes are calculated: by billing or by shipping address, we have prepared a simple parameter
that you need to change `sylius_core.taxation.default_strategy` in your configs:

.. code-block:: yaml

    # app/config/config.yml
    parameters:
        sylius_core.taxation.default_strategy: false

And with this change, the way how taxes are calculated is based on shipping address.
