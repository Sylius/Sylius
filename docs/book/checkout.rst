.. index::
   single: Checkout

Checkout
========

**Checkout** is a process that begins when the Customer decides to finish his shopping and transform his **Cart** into an **Order**.

Checkout in Sylius is divided into steps:

* addressing - where the customer provides both shipping and billing addresses,
* selecting shipping - when the customer seletc the way his order will be shipped to him,
* selecting payment - where the customer chooses how is he willing to pay for his order,
* finalizing - when the customer gets an order summary

Checkout State Machine
----------------------

The Order Checkout state machine has 5 states available: ``cart, addressed, shipping_selected, payment_selected, completed``
and a set of defined transitions between them.

