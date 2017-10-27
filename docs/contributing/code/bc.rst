Backward Compatibility Promise
==============================

Sylius follows a versioning strategy called `Semantic Versioning`_. It means that
only major releases include BC breaks, whereas minor releases include new features
without breaking backwards compatibility.

Since Sylius is based on Symfony, our BC promise extends `Symfony's Backward Compatibility Promise`_
with a few new rules and exceptions stated in this document.

Minor and patch releases
------------------------

Patch releases (such as 1.0.x, 1.1.x, etc.) do not require any additional work
apart from cleaning the Symfony cache.

Minor releases (such as 1.1.0, 1.2.0, etc.) require to run database migrations.

Code covered
------------

This BC promise applies to all of Sylius' PHP code except for:

    - code tagged with ``@internal`` tags
    - event listeners
    - model and repository interfaces
    - PHPUnit tests (located at ``tests/``, ``src/**/Tests/``)
    - PHPSpec tests (located at ``src/**/spec/``)
    - Behat tests (located at ``src/Sylius/Behat/``)

Additional rules
----------------

Models & model interfaces
~~~~~~~~~~~~~~~~~~~~~~~~~

In order to fulfill the constant Sylius' need to evolve, model interfaces are excluded from this BC promise.
Methods may be added to the interface, but backwards compatibility is promised as long as your custom model
extends the one from Sylius, which is true for most cases.

Repositories & repository interfaces
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Following the reasoning same as above and due to technological constraints, repository interfaces are also
excluded from this BC promise.

Event listeners
~~~~~~~~~~~~~~~

They are excluded from this BC promise, but they should be as simple as possible and always call another service.
Behaviour they're providing (the end result) is still included in BC promise.

Routing
~~~~~~~

The currently present routes cannot have their name changed, but optional parameters might be added to them.
All the new routes will start with ``sylius_`` prefix in order to avoid conflicts.

Services
~~~~~~~~

Services names cannot change, but new services might be added with ``sylius.`` prefix.

Templates
~~~~~~~~~

Neither template events, block or templates themselves cannot be deleted or renamed.

.. _Semantic Versioning: http://semver.org/
.. _Symfony's Backward Compatibility Promise: https://symfony.com/doc/current/contributing/code/bc.html
