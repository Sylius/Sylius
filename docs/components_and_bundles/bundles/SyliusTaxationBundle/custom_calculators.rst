.. rst-class:: outdated

Using custom tax calculators
============================

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

Every **TaxRate** model holds a *calculator* variable with the name of the tax calculation service, used to compute the tax amount.
While the default calculator should fit for most common use cases, you're free to define your own implementation.

Creating the calculator
-----------------------

All tax calculators implement the ``CalculatorInterface``. In our example we'll create a simple fee calculator. First, you need to create a new class.

.. code-block:: php

    <?php
    # src/Taxation/Calculator/FeeCalculator.php

    declare(strict_types=1);

    namespace App\Taxation\Calculator;

    use Sylius\Component\Taxation\Calculator\CalculatorInterface;
    use Sylius\Component\Taxation\Model\TaxRateInterface;

    final class FeeCalculator implements CalculatorInterface
    {
        /**
         * {@inheritdoc}
         */
        public function calculate(float $base, TaxRateInterface $rate): float
        {
            return $base * ($rate->getAmount() + 0.15 * 0.30);
        }
    }

Now, you need to register your new service in container and tag it with ``sylius.tax_calculator``.

.. code-block:: yaml

    services:
        app.tax_calculator.fee:
            class: App\Taxation\Calculator\FeeCalculator
            tags:
                - { name: sylius.tax_calculator, calculator: fee, label: "Fee" }

That would be all. This new option ("Fee") will appear on the **TaxRate** creation form, in the "calculator" field.
