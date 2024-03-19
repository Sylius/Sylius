How to configure tax rates to be based on shipping address?
===========================================================

The default configuration of Sylius tax calculation is based on billing address but there are situations where we would
like to use a shipping address to be used in this process. This may be useful to anyone who uses Sylius in European Union
as from 1st July 2021 the new taxation rules will be applied.

.. note::

    You can learn more about new EU taxation rules `here <https://european-union.europa.eu/priorities-and-actions/actions-topic/taxation_en>`_.

To change the way how the taxes are calculated: by billing or by shipping address, you need to add a parameter to your config:

.. code-block:: yaml

    # config/packages/_sylius.yaml
    sylius_core:
        # resources definitions
        shipping_address_based_taxation: true

And with this change, the way how taxes are calculated is now based on shipping address.
