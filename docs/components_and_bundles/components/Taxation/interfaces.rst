.. rst-class:: outdated

Interfaces
==========

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

Model Interfaces
----------------

Taxable Interface
~~~~~~~~~~~~~~~~~

To create taxable object which has specific type of tax category, the object class needs to implement
**TaxableInterface**.

Tax Category Interface
~~~~~~~~~~~~~~~~~~~~~~

To create object which provides information about tax category, the object class needs to implement
**TaxCategoryInterface**.

.. note::
    This interface extends `CodeAwareInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/CodeAwareInterface.php>`_ and `TimestampableInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/TimestampableInterface.php>`_.

Tax Rate Interface
~~~~~~~~~~~~~~~~~~

To create object which provides information about tax rate, the object class needs to implement
**TaxCategoryInterface**.

.. note::
    This interface extends `CodeAwareInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/CodeAwareInterface.php>`_ and `TimestampableInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/TimestampableInterface.php>`_.

Calculator Interfaces
---------------------

CalculatorInterface
~~~~~~~~~~~~~~~~~~~

To make the calculator able to calculate the tax amount for given base amount and tax rate,
the calculator class needs implement the **CalculatorInterface**.

Resolver Interfaces
-------------------

TaxRateResolverInterface
~~~~~~~~~~~~~~~~~~~~~~~~

To create class which provides information about tax rate for given taxable object and specific criteria, the class needs to
implement **TaxRateResolverInterface**. The criteria describes tax rate object.
