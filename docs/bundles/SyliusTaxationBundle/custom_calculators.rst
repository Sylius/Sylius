Using custom tax calculators
============================

Every **TaxRate** model holds a *calculator* variable with the name of the tax calculation service, used to compute the tax amount.
While the default calculator should fit for most common use cases, you're free to define your own implementation.

Creating the calculator
-----------------------

All calculators implement the **TaxCalculatorInterface**. First, you need to create a new class.

.. code-block:: php

    namespace Acme\Bundle\ShopBundle\TaxCalculator;

    use Sylius\Bundle\TaxationBundle\Calculator\TaxCalculatorInterface;
    use Sylius\Bundle\TaxationBundle\Model\TaxRateInterface;

    class FeeCalculator implements TaxCalculatorInterface
    {
        public function calculate($amount, TaxRate $rate)
        {
            return $amount * ($rate->getAmount() + 0,15 * 0,30);
        }
    }
