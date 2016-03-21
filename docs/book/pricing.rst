.. index::
   single: Pricing

Pricing
=======

Pricing is the part of Sylius responsible for calculating the product prices for the cart. This functionality comes from the *SyliusPricingBundle*.

*ProductVariant* implements the *PriceableInterface* and has following attributes available:

* pricingCalculator
* pricingConfiguration

.. note::

    All prices in Sylius are represented by integers, so 14.99$ is stored as 1499 in the database.

First parameter holds the name of calculator type and second contains the configuration array for this particular calculator.

For example, if you have a product without any variations, its *master variant* can have a simple setup with:

* price = 2099
* pricingCalculator = *group_based*
* pricingConfiguration = [23 => 2499, 85 => 2999]

Calculators
-----------

A calculator is a very simple service implement *PriceCalculatorInterface* and has a very important **calculate(priceable, configuration, context)** method.

Every time product is added, removed to cart and generally on every cart modification event, this method is used to calculate the unit price.
It takes the appropriate calculator configured on the product, together with the configuration and context. Context is taken from the product as well, but context is coming from the cart.

Currently the context is really simple and contains the following details:

* quantity of the cart item
* user
* groups

You can easily [implement your own pricing calculator] and allow store managers to define complex pricing rules for any merchandise.

Final Thoughts
--------------

...

Learn more
----------

* ...
