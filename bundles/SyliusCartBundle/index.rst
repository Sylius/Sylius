SyliusCartBundle
================

A generic solution for cart system inside Symfony2 application. 

It doesn't matter if you are starting new project or you need to implement this feature for existing system - this bundle should be helpful.
Currently only the Doctrine ORM driver is implemented, so we'll use it here as example.

There are two main models inside the bundle, `Cart` and `CartItem`.

There are also 2 main services, **Provider** and **ItemResolver**.
You'll get familiar with them in further parts of this documentation.

.. toctree::
   :numbered:

   installation
   models
   actions
   services
   templating
   summary
