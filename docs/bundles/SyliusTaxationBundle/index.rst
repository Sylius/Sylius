SyliusTaxationBundle
====================

Calculating and applying taxes is a common task for most of ecommerce applications. **SyliusTaxationBundle** is a reusable taxation component for Symfony.
You can integrate it into your existing application and enable the tax calculation logic for any model implementing the ``TaxableInterface``.

It supports different tax categories and customizable tax calculators - you're able to easily implement your own calculator services.
The default implementation handles tax included in and excluded from the price.

As with any Sylius bundle, you can override all the models, controllers, repositories, forms and services.

.. toctree::
   :numbered:

   installation
   taxable_interface
   configuring_taxation
   calculating_taxes
   custom_calculators
   summary

Learn more
----------

* :doc:`Taxation in the Sylius platform </book/orders/taxation>` - concept documentation
