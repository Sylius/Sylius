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


.. note::

    Now we need to register calculator as service.

Remember we set calculator for tax rate as ``app.server_tax``.

.. code-block:: php

    $cloudServerTax = $repository
            ->createNew()
            ->setName('Cloud Server Tax')
            ->setCalculator('app.server_tax')
            ->setAmount(0,15);

Service should be tagged with key value pair ``name: sylius.tax_calculator`` and ``calculator: app.server_tax``.

.. code-block:: yaml

    #
    app.tax_calculator.server_tax_calculator:
        class: AppBundle\Taxes\Calculator\TouristTaxCalculator
        tags:
            - { name: sylius.tax_calculator, calculator: app.server_tax }
