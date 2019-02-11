.. index::
   single: Cart flow

Cart flow
======

**Cart** in Sylius is an **Order** in the state ``cart``.
It represents an order that is in progress (not placed yet).

.. note::
    Each user has their own cart when logged into Sylius. It can be cleared either by placing an order or removing items manually.

There are several cart flows, depending on the user being logged in or what items are currently placed in the cart.

* Not logged in user adds some items to the cart and then logs in as a customer whose cart had not been empty. **It will result in dropping the cart created by an unknown user and load the cart previously created by the logged in customer.**
* Not logged in user adds some items to the cart and then logs in as a customer whose cart had been empty. **It will result in setting the cart created by an unknown user as a cart that belongs to a logged in customer.**
* Logged in user adds some items to the cart and then logs out. **Once customer logs out, their cart is not transferred to a session that belongs to an unknown user.**

Learn more
----------

* :doc:`Carts API </api/carts>`
