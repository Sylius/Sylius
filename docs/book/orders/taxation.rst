.. index::
   single: Taxation

Taxation
========

Sylius' taxation system allows you to apply appropriate taxes for different items, billing zones and using custom calculators.

Tax Categories
--------------

In order to process taxes in your store, you need to configure at least one **TaxCategory**, which represents a specific type of merchandise.
If all your items are taxed with the same rate, you can have a simple "Taxable Goods" category assigned to all items.

If you sell various products and some of them have different taxes applicable, you could create multiple categories. For example, "Clothing", "Books" and "Food".

Additionally to tax categories, you can have different zones, in order to apply correct taxes for customers coming from any country in the world.

How to create a TaxCategory programmatically?
'''''''''''''''''''''''''''''''''''''''''''''

In order to create a TaxCategory use the dedicated factory. Your TaxCategory requires a ``name`` and a ``code``.

.. code-block:: php

   /** @var TaxCategoryInterface $taxCategory */
   $taxCategory = $this->container->get('sylius.factory.tax_category')->createNew();

   $taxCategory->setCode('taxable_goods');
   $taxCategory->setName('Taxable Goods');

   $this->container->get('sylius.repository.tax_category')->add($taxCategory);

Since now you will have a new TaxCategory available.

How to set a TaxCategory on a ProductVariant?
'''''''''''''''''''''''''''''''''''''''''''''

In order to have taxes calculated for your products you have to set TaxCategories for each ProductVariant you create.
Read more about Products and Variants :doc:`here </book/products/products>`.

.. code-block:: php

   /** @var TaxCategoryInterface $taxCategory */
   $taxCategory = $this->container->get('sylius.repository.tax_category')->findOneBy(['code' => 'taxable_goods']);

   /** @var ProductVariantInterface $variant */
   $variant = $this>container->get('sylius.repository.product_variant')->findOneBy(['code' => 'mug']);

   $variant->setTaxCategory($taxCategory);

Tax Rates
---------

A tax rate is essentially a percentage amount charged based on the sales price. Tax rates also contain other important information:

* Whether product prices are inclusive of this tax
* The zone in which the order address must fall within
* The tax category that a product must belong to in order to be considered taxable
* Calculator to use for computing the tax

TaxRates included in price
''''''''''''''''''''''''''

The **TaxRate** entity has a field for configuring if you would like to have taxes included in the price of a subject or not.

If you have a TaxCategory with a 23% VAT TaxRate *includedInPrice* (``$taxRate->isIncludedInPrice()`` returns ``true``),
then the price shown on the ProductVariant in that TaxCategory will be increased by 23% all the time. See the Behat scenario below:

.. code-block:: text

   Given the store has included in price "VAT" tax rate of 23%
   And the store has a product "T-Shirt" priced at "$10.00"
   When I add product "T-Shirt" to my cart
   Then my cart total should be "$10.00"
   And my cart taxes should be "$1.87"

If the TaxRate *will not be included* (``$taxRate->isIncludedInPrice()`` returns ``false``)
then the price of ProductVariant will be shown without taxes, but when this ProductVariant will be added to cart taxes will be shown in the Taxes Total in the cart.
See the Behat scenario below:

.. code-block:: text

   Given the store has excluded from price "VAT" tax rate of 23%
   And the store has a product "T-Shirt" priced at "$10.00"
   When I add product "T-Shirt" to my cart
   Then my cart total should be "$12.30"
   And my cart taxes should be "$2.30"

How to create a TaxRate programmatically?
'''''''''''''''''''''''''''''''''''''''''

.. note::

   Before creating a tax rate you need to now that you can have different tax zones, in order to apply correct taxes for customers coming from any country in the world.
   To understand how zones work, please refer to the `Zones <http://docs.sylius.org/en/latest/book/addresses.html#zones>`_ chapter of this book.

Use a factory to create a new, empty TaxRate. Provide a ``code``, a ``name``. Set the amount of charge in float.
Then choose a calculator and zone (retrieved from the repository beforehand).

Finally you can set the TaxCategory of your new TaxRate.

.. code-block:: php

   /** @var TaxRateInterface $taxRate */
   $taxRate = $this->container->get('sylius.factory.tax_rate')->createNew();

   $taxRate->setCode('7%');
   $taxRate->setName('7%');
   $taxRate->setAmount(0.07);
   $taxRate->setCalculator('default');

   // Get a Zone from the repository, for example the 'US' zone
   /** @var ZoneInterface $zone */
   $zone = $this->container->get('sylius.repository.zone')->findOneBy(['code' => 'US']);

   $taxRate->setZone($zone);

   // Get a TaxCategory from the repository, for example the 'alcohol' category
   /** @var TaxCategoryInterface $taxCategory */
   $taxCategory = $this->container->get('sylius.repository.tax_category')->findOneBy(['code' => 'alcohol']);

   $taxRate->setCategory($taxCategory);

   $this->container->get('sylius.repository.tax_rate')->add($taxRate);

Default Tax Zone
----------------

The **default tax zone** concept is used for situations when we want to show taxes included in price even when we do not know the
address of the Customer, therefore we cannot choose a proper Zone, which will have proper TaxRates.

Since we are using the concept of :doc:`Channels </book/configuration/channels>`, we will use **the Zone assigned to the Channel as default Zone for Taxation**.

.. note::

   To understand how zones work, please refer to the `Zones <http://docs.sylius.org/en/latest/book/addresses.html#zones>`_ chapter of this book.

Applying Taxes
--------------

For applying Taxes **Sylius** is using the `OrderTaxesProcessor <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Component/Core/OrderProcessing/OrderTaxesProcessor.php>`_,
which has the services that implement the `OrderTaxesApplicatorInterface <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Component/Core/Taxation/Applicator/OrderTaxesApplicatorInterface.php>`_ inside.

Calculators
'''''''''''

For calculating Taxes **Sylius** is using the `DefaultCalculator <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Component/Taxation/Calculator/DefaultCalculator.php>`_.
You can create your custom calculator for taxes by creating a class that implements
the `CalculatorInterface <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Component/Taxation/Calculator/CalculatorInterface.php>`_
and registering it as a ``sylius.tax_calculator.your_calculator_name`` service.

Learn more
----------

* :doc:`Taxation - Bundle Documentation </bundles/SyliusTaxationBundle/index>`
* :doc:`taxation - Component Documentation </components/Taxation/index>`