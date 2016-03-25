Interfaces
==========

Calculators Interfaces
----------------------

CalculatorInterface
~~~~~~~~~~~~~~~~~~~

To make the calculator able to calculate the price of subject, it needs to implement the **CalculatorInterface**.

.. note::
    For more detailed information go to `Sylius API's CalculatorInterface`_.

.. _Sylius API's CalculatorInterface: http://api.sylius.org/Sylius/Component/Pricing/Calculator/CalculatorInterface.html

DelegatingCalculatorInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

To delegate the calculation of charge for particular subject to a correct calculator instance
it is needed to create class which implements **DelegatingCalculatorInterface**.

.. note::
    For more detailed information go to `Sylius API's DelegatingCalculatorInterface`_.

.. _Sylius API's DelegatingCalculatorInterface: http://api.sylius.org/Sylius/Component/Pricing/Calculator/DelegatingCalculatorInterface.html

Model Interfaces
----------------

PriceableInterface
~~~~~~~~~~~~~~~~~~

To calculate price of a subject, the subject class needs to implement the **PriceableInterface**, |br|
which provides methods to describe price, type of calculator and configuration for subject.

.. note::
    For more detailed information go to `Sylius API's PriceableInterface`_.

.. _Sylius API's PriceableInterface: http://api.sylius.org/Sylius/Component/Pricing/Model/PriceableInterface.html
