Interfaces
==========

Model Interfaces
----------------

Taxable Interface
~~~~~~~~~~~~~~~~~

To create taxable object which has specific type of tax category, the object class needs to implement
**TaxableInterface**.

.. note::
    For more detailed information go to `Sylius API's Taxable Interface`_.

.. _Sylius API's Taxable Interface: http://api.sylius.org/Sylius/Component/Taxation/Model/TaxableInterface.html

Tax Category Interface
~~~~~~~~~~~~~~~~~~~~~~

To create object which provides information about tax category, the object class needs to implement
**TaxCategoryInterface**.

.. note::
    This interface extends :ref:`component_resource_model_code-aware-interface` and :ref:`component_resource_model_timestampable-interface`. |br|
    For more detailed information go to `Sylius API's Tax Category Interface`_.

.. _Sylius API's Tax Category Interface: http://api.sylius.org/Sylius/Component/Taxation/Model/TaxCategoryInterface.html

Tax Rate Interface
~~~~~~~~~~~~~~~~~~

To create object which provides information about tax rate, the object class needs to implement
**TaxCategoryInterface**.

.. note::
    This interface extends :ref:`component_resource_model_code-aware-interface` and :ref:`component_resource_model_timestampable-interface`. |br|
    For more detailed information go to `Sylius API's Tax Rate Interface`_.

.. _Sylius API's Tax Rate Interface: http://api.sylius.org/Sylius/Component/Taxation/Model/TaxCategoryInterface.html

Calculator Interfaces
---------------------

CalculatorInterface
~~~~~~~~~~~~~~~~~~~

To make the calculator able to calculate the tax amount for given base amount and tax rate,
the calculator class needs implement the **CalculatorInterface**.

.. note::
    For more detailed information about the interfaces go to `Sylius API's Calculator Interface`_.

.. _Sylius API's Calculator Interface: http://api.sylius.org/Sylius/Component/Taxation/Calculator/CalculatorInterface.html

Resolver Interfaces
-------------------

TaxRateResolverInterface
~~~~~~~~~~~~~~~~~~~~~~~~

To create class which provides information about tax rate for given taxable object and specific criteria, the class needs to
implement **TaxRateResolverInterface**. The criteria describes tax rate object.

.. note::
    For more detailed information about the interfaces go to `Sylius API's Tax Rate Resolver Interface`_.

.. _Sylius API's Tax Rate Resolver Interface: http://api.sylius.org/Sylius/Component/Taxation/Resolver/TaxRateResolverInterface.html
