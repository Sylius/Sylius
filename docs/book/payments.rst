.. index::
   single: Payments

Payments
========

Sylius contains a very flexible payments management system with support for many gateways. (payment providers)
We are using very powerful payment abstraction library, called [Payum], which handles all sorts of capturing, refunding and recurring payments logic.

On Sylius side, we integrate it into our checkout and manage all the payment data.

Payment
-------

Every payment in Sylius, successful or failed, is represented by *Payment* model, which contains basic information and reference to appropriate order.

Payment has following attributes:

* id
* currency (code)
* amount
* reference to *PaymentMethod*
* reference to *Order*
* state
* reference to [PaymentSource] (optional)
* createdAt
* updatedAt

All these properties are easily accessible through simple API:

.. code-block:: php

    <?php

    echo $payment->getAmount() . $payment->getCurrency();

    $order = $payment->getOrder(); // Get the order.

    echo $payment->getMethod()->getName(); // Get the name of payment method used.

Payment State and Workflow
==========================

We are using [StateMachine] library to manage all payment states, here is the full list of defaults:

* new (initial)
* unknown
* pending
* processing
* completed
* failed
* cancelled
* void
* refunded

Of course, you can define your own states and transitions to create a workflow, that perfectly matches your business. Full configuration can be seen `here <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/PaymentBundle/Resources/config/state-machine.yml>`_.

Changes to payment happen mostly through applying appropriate transitions.

Payment Methods
---------------

A [PaymentMethod] represents a way that your customer pays during the checkout process. It holds a reference to specific *gateway* with custom configuration.
You can have different payment methods using the same gateway, like PayPal or Stripe. Default model of payment method contains the following fields:

* name
* description
* enabled
* gateway
* configuration
* environment
* feeCalculator
* feeCalculatorConfiguration
* createdAt
* updatedAt

Gateway and Configurations
--------------------------

...

Payment Processing
------------------

...

Supported Gateways
------------------

...

Final Thoughts
--------------

...

Learn more
----------

* ...

Troubleshoooting
================

Sylius stores payment output inside the **details** column of the **sylius_payment** table. It can provide valuable info when debugging the payment process.

PayPal Error Code 10409
-------------------------------------------------------------------

Also known as *"Checkout token was issued for a merchant account other than yours"*. You most likely changed the PayPal credentials from *config.yml* during the checkout process. Clear the cache and try again:

.. code-block:: bash

    app/console cache:clear
    
