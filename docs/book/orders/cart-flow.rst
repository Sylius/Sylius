.. index::
   single: Cart flow

Cart flow
=========

Picture the following situation - a user comes to a Sylius shop and they say:
**"Someone's been using my cart! And they filled it all up with some items!"** Let's avoid such moments of surprise
by shedding some light on Sylius cart flow, shall we?

**Cart** in Sylius represents an **Order** that is not placed yet.
It represents an order that is in progress (not placed yet).

.. note::
    In Sylius each visitor has their own cart. It can be cleared either by placing an order, removing items manually
    or using cart clearing command.

There are several cart flows, depending on the user being logged in or what items are currently placed in the cart.

First scenario::

    Given there is a not logged in user
    And this user adds a blue T-Shirt to the cart
    And this user adds a red cap to the cart
    And there is a customer identified by email "sylius@example.com" with not empty cart
    When the not logged in user logs in using "sylius@example.com" email
    Then the cart created by a not logged in user should be dropped
    And the cart previously created by the user identified by "sylius@example.com" should be set as the current one

Second scenario::

    Given there is a not logged in user
    And this user adds a blue T-Shirt to the cart
    And this user adds a red cap to the cart
    And there is a customer identified by email "sylius@example.com" with an empty cart
    When the not logged in user logs in using "sylius@example.com" email
    Then the cart created by a not logged in user should not be dropped
    And it should be set as the current cart

Third scenario::

    Given there is a customer identified by email "sylius@example.com" with an empty cart
    And this user adds a blue T-Shirt to the cart
    And this user adds a red cap to the cart
    When the user logs out
    And views the cart
    Then the cart should be empty

.. note::
    The cart mentioned in the last scenario will we available once you log in again.

Learn more
----------
