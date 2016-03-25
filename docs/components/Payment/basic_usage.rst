Basic Usage
===========

.. _component_payment_calculator_fixed-fee-calculator:

FixedFeeCalculator
------------------

This calculator is used to get the amount
of fee set at the ``amount`` key in the configuration array.

.. code-block:: php

   <?php

   use Sylius\Component\Payment\Calculator\FixedFeeCalculator;
   use Sylius\Component\Payment\Model\Payment;

   $fixedFeeCalculator = new FixedFeeCalculator();

   $payment = new Payment();
   $payment->setAmount(500);

   $configuration = array('amount' => 10);

   $fixedFeeCalculator->calculate($payment, $configuration); // returns value of the 'amount' key
                                                             // so in this case 10

.. note::
   This service implements the :ref:`component_payment_calculator_fee-calculator-interface`.

.. _component_payment_calculator_percent-fee-calculator:

PercentFeeCalculator
--------------------

This calculator uses the ``percent`` key's value of the
configuration array in order to calculate a payment's fee.

.. code-block:: php

   <?php

   use Sylius\Component\Payment\Calculator\PercentFeeCalculator;
   use Sylius\Component\Payment\Model\Payment;

   $percentFeeCalculator = new PercentFeeCalculator();

   $payment = new Payment();
   $payment->setAmount(200);

   $configuration = array('percent' => 10);

   $percentFeeCalculator->calculate($payment, $configuration); // returns 20

.. note::
   This service implements the :ref:`component_payment_calculator_fee-calculator-interface`.

.. _component_payment_delegating-fee-calculator:

DelegatingFeeCalculator
-----------------------

This calculator doesn't do any calculations itself, instead it uses
a specified calculator to do the work and just returns the result.

.. code-block:: php

   <?php

   use Sylius\Component\Payment\Model\Payment;
   use Sylius\Component\Payment\Model\PaymentMethod;
   use Sylius\Component\Payment\Calculator\DelegatingFeeCalculator;
   use Sylius\Component\Registry\ServiceRegistry;

   $registry = new ServiceRegistry('Sylius\Component\Payment\Calculator\FeeCalculatorInterface');

   $registry->register(DefaultFeeCalculators::FIXED, new FixedFeeCalculator());
   $registry->register(DefaultFeeCalculators::PERCENT, new PercentFeeCalculator());

   $calculator = DelegatingFeeCalculator($registry);

   $configurations = array('amount' => 14, 'percent' => 23);

   $paymentMethod = new PaymentMethod();
   $paymentMethod->setFeeCalculatorConfiguration($configurations);

   $payment = new Payment();
   $payment->setAmount(200);
   $payment->setMethod($paymentMethod);

   $calculator->calculate($payment); // returns 14 as the FixedFeeCalculator
                                     // is set by default

   $paymentMethod->setFeeCalculator(DefaultFeeCalculators::PERCENT);

   $calculator->calculate($payment); // now it returns 46
                                     // because we changed to the PercentFeeCalculator

.. hint::
   All the default calculator types are available via the :doc:`default_fee_calculators` class.

.. note::
   This service implements the :ref:`component_payment_calculator_delegating-fee-calculator-interface`.
