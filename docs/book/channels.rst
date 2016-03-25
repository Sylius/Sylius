.. index::
   single: Channels

Channels
========

In the modern world of e-commerce your website is no longer the only point of sale for your goods.
Sylius supports multiple-channels and in this guide you will understand them from a technical point of view.

**Channel** model represents a single sales channel, which can be one of the following things:

* Webstore
* Mobile application
* Cashier in your physical store

Or pretty much any other channel type you can imagine.

The default model has the following basic properties:

code
    An unique code identifying this channel
name
    The human readable name of the channel
description
    Short description
color:
    Color representation
url:
    The url pattern used to identify the channel
enabled:
    Is the channel currently enabled?
createdAt
    Date of creation
updateAt
    Timestamp of the most recent update

Channel configuration also allows you to configure several important aspects:

locales
    You can select one or more locales available in this particular store
currencies
    Every channel operates only on selected currencies
paymentMethods
    You can define which payment methods are available in this channel
shippingMethods
    Channel must have shipping methods configured

Final Thoughts
--------------

...

Learn more
----------

* ...
