Catalog promotion performance
=============================

In case of looking for improving performance of processing your `catalog promotions`

Change default batch size:
--------------------------

By default ``batch_size`` in Sylius is fixed at processing ``100`` variants per batch.
In order if you want to increase or decrease it's value, you have to overwrite it.
You can achieve that by adding to your ``_sylius.yaml`` configuration file this config with changed ``batch_size`` value:

.. code-block:: yaml

    sylius_core:
        catalog_promotions:
            batch_size: 100

Then your batch size should be fixed to passed value.
