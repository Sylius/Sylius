Interfaces
==========

Calculators Interfaces
----------------------

CalculatorInterface
~~~~~~~~~~~~~~~~~~~

To make the calculator able to calculate the price of subject, it needs to implement the **CalculatorInterface**.

.. note::
    For more detailed information go to `Sylius API CalculatorInterface`_.

.. _Sylius API CalculatorInterface: http://api.sylius.org/Sylius/Component/Pricing/Calculator/CalculatorInterface.html

DelegatingCalculatorInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

To delegate the calculation of charge for particular subject to a correct calculator instance
it is needed to create class which implements **DelegatingCalculatorInterface**.

.. note::
    For more detailed information go to `Sylius API DelegatingCalculatorInterface`_.

.. _Sylius API DelegatingCalculatorInterface: http://api.sylius.org/Sylius/Component/Pricing/Calculator/DelegatingCalculatorInterface.html

Model Interfaces
----------------

PriceableInterface
~~~~~~~~~~~~~~~~~~

To calculate price of a subject, the subject class needs to implement the **PriceableInterface**,

which provides methods to describe price, type of calculator and configuration for subject.

.. note::
    For more detailed information go to `Sylius API PriceableInterface`_.

.. _Sylius API PriceableInterface: http://api.sylius.org/Sylius/Component/Pricing/Model/PriceableInterface.html
