SyliusCartBundle
================

A generic solution for a cart system inside a Symfony2 application.

It doesn't matter if you are starting a new project or you need to implement this feature for an existing system - this bundle should be helpful.
Currently only the Doctrine ORM driver is implemented, so we'll use it here as an example.

There are two main models inside the bundle, `Cart` and `CartItem`.

There are also 2 main services, **Provider** and **ItemResolver**.
You'll get familiar with them in further parts of the documentation.

.. toctree::
   :numbered:

   installation
   models
   actions
   services
   templating
   summary
