.. rst-class:: outdated

Twig Extension
==============

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

There are two handy twig functions bundled in: `sylius_inventory_is_available` and `sylius_inventory_is_sufficient`.

They are simple proxies to the availability checker, and can be used to show if the stockable object is available/sufficient.

Here is a simple example, note that `product` variable has to be an instance of `StockableInterface`.

.. code-block:: jinja

    {% if not sylius_inventory_is_available(product) %}
        <span class="label label-important">out of stock</span>
    {% endif %}
