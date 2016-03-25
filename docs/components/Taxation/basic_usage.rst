Basic Usage
===========

Tax Rate
--------

Every tax rate has three identifiers, an ID, code and name. You can access those by calling ``->getId()``, ``->getCode()`` and ``getName()``
respectively. The name and code are mutable, so you can change them by calling ``->setCode('X12XW')`` and ``->setName('EU VAT')`` on the tax rate instance.

Setting Tax Amount
~~~~~~~~~~~~~~~~~~

A tax rate has two basic amounts - the *amount* and the *amount as percentage* (by default equal 0).

.. code-block:: php

    <?php

    use Sylius\Component\Taxation\Model\TaxRate;
    use Sylius\Component\Taxation\Model\TaxCategory;

    $taxRate = new TaxRate();
    $taxCategory = new TaxCategory();

    $taxRate->setAmount(0.5);
    $taxRate->getAmount(); // Output will be 0.5
    $taxRate->getAmountAsPercentage(); // Output will be 50


Setting Tax Category
~~~~~~~~~~~~~~~~~~~~

Every tax rate can have a tax category. You can simply set or unset it by calling ``->setCategory()``.

.. code-block:: php

    <?php

    $taxRate->setCategory($taxCategory);
    $taxRate->getCategory(); // Output will be  $taxCategory object
    $taxRate->setCategory();
    $taxRate->getCategory(); // Output will be null


Including tax rate in price
~~~~~~~~~~~~~~~~~~~~~~~~~~~

You can mark a tax rate as included in price by calling ``setIncludedInPrice(true)`` (false by default).
To check if tax rate is included in price call ``isIncludedInPrice()``.

.. hint::
    You can read how this property influences on the tax calculation in chapter :ref:`default-calculator`.

Setting calculator
~~~~~~~~~~~~~~~~~~

To set type of calculator for your tax rate object call ``setCalculator('nameOfCalculator')``. Notice that nameOfCalculator
should be the same as name of your calculator object.

.. hint::
    To understand meaning of this property go to :ref:`delegating-calculator`.

Tax Category
------------

Every tax category  has three identifiers, an ID, code and name. You can access those by calling ``->getId()``, ``->getCode()`` and ``getName()``
respectively. The code and name are mutable, so you can change them by calling ``->setCode('X12X')`` and ``->setName('Clothing')`` on the tax category instance.

Tax Rate Management
~~~~~~~~~~~~~~~~~~~

The collection of tax rates (Implementing the ``Doctrine\Common\Collections\Collection`` interface) can be obtained using
the ``getRates()`` method. To add or remove tax rates, you can use the ``addRate()`` and ``removeRate()`` methods.

.. code-block:: php

    <?php

    use Sylius\Component\Taxation\Model\TaxRate;
    use Sylius\Component\Taxation\Model\TaxCategory;

    $taxCategory = new TaxCategory();

    $taxRate1 = new TaxRate();
    $taxRate1->setName('taxRate1');

    $taxRate2 = new TaxRate();
    $taxRate2->setName('taxRate2');

    $taxCategory->addRate($taxRate1);
    $taxCategory->addRate($taxRate2);
    $taxCategory->getRates();
    //returns a collection of objects that implement the TaxRateInterface
    $taxCategory->removeRate($taxRate1);
    $taxCategory->hasRate($taxRate2); // returns true
    $taxCategory->getRates(); // returns collection with one element

Calculators
-----------

.. _default-calculator:

Default Calculator
~~~~~~~~~~~~~~~~~~

**Default Calculator** gives you the ability to calculate the tax amount for given base amount and tax rate.

.. code-block:: php

    <?php

    use Sylius\Component\Taxation\Model\TaxRate;
    use Sylius\Component\Taxation\Calculator\DefaultCalculator;

    $taxRate = new TaxRate();
    $taxRate->setAmount(0.2);
    $basicPrice = 100;
    $defaultCalculator = new DefaultCalculator();
    $defaultCalculator->calculate($basicPrice, $taxRate); //return 20
    $taxRate->setIncludedInPrice(true);
    $defaultCalculator->calculate($basicPrice, $taxRate);
    // return 17, because the tax is now included in price

.. _delegating-calculator:

Delegating Calculator
~~~~~~~~~~~~~~~~~~~~~

**Delegating Calculator** gives you the ability to delegate the calculation of amount of tax to a correct calculator
instance based on a type defined in an instance of **TaxRate** class.

.. code-block:: php

    <?php

    use Sylius\Component\Taxation\Model\TaxRate;
    use Sylius\Component\Taxation\Calculator\DefaultCalculator;
    use Sylius\Component\Registry\ServiceRegistry;
    use Sylius\Component\Taxation\Calculator\DelegatingCalculator;
    use Sylius\Component\Taxation\Calculator\CalculatorInterface;

    $taxRate = new TaxRate();
    $taxRate->setAmount(0.2);
    $base = 100; //set base price to 100
    $defaultCalculator = new DefaultCalculator();

    $serviceRegistry =
    new ServiceRegistry(CalculatorInterface::class);
    $serviceRegistry->register('default', $defaultCalculator);

    $delegatingCalculator = new DelegatingCalculator($serviceRegistry);
    $taxRate->setCalculator('default');
    $delegatingCalculator->calculate($base, $taxRate); // returns 20

Tax Rate Resolver
-----------------

**TaxRateResolver** gives you ability to get information about tax rate for given taxable object and specific criteria.
The criteria describes tax rate object.

.. code-block:: php

    <?php

    use Sylius\Component\Taxation\Resolver\TaxRateResolver;
    use Sylius\Component\Taxation\Model\TaxCategory;

    $taxRepository = new InMemoryTaxRepository(); // class which implements RepositoryInterface
    $taxRateResolver= new TaxRateResolver($taxRepository);

    $taxCategory = new TaxCategory();
    $taxCategory->setName('TaxableGoods');

    $taxableObject = new TaxableObject(); // class which implements TaxableInterface
    $taxableObject->setTaxCategory($taxCategory);

    $criteria = array('name' => 'EU VAT');
    $taxRateResolver->resolve($taxableObject, $criteria);
    // returns instance of class TaxRate, which has name 'EU VAT' and category 'TaxableGoods'
