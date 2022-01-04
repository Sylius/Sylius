Backward Compatibility Promise
==============================

Sylius follows a versioning strategy called `Semantic Versioning`_. It means that
only major releases include BC breaks, whereas minor releases include new features
without breaking backwards compatibility.

Since Sylius is based on Symfony, our BC promise extends `Symfony's Backward Compatibility Promise`_
with a few new rules and exceptions stated in this document. We also follow `Symfony's Experimental Features`_
process to be able to innovate safely.

Minor and patch releases
------------------------

Patch releases (such as 1.0.x, 1.1.x, etc.) do not require any additional work
apart from cleaning the Symfony cache.

Minor releases (such as 1.1.0, 1.2.0, etc.) require to run database migrations.

Code covered
------------

This BC promise applies to all of Sylius' PHP code except for:

    - code tagged with ``@internal`` or ``@experimental`` tags
    - event listeners
    - model and repository interfaces
    - PHPUnit tests (located at ``tests/``, ``src/**/Tests/``)
    - PHPSpec tests (located at ``src/**/spec/``)
    - Behat tests (located at ``src/Sylius/Behat/``)
    - final controllers (their service name is still covered with BC promise)

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

Final controllers
~~~~~~~~~~~~~~~~~

It is allowed to change their dependencies, but the behaviour they're providing is still included in BC promise.
The service name and class name will not change.

Routing
~~~~~~~

The currently present routes cannot have their name changed, but optional parameters might be added to them.
All the new routes will start with ``sylius_`` prefix in order to avoid conflicts.

Services
~~~~~~~~

Services names cannot change, but new services might be added with ``sylius.`` or ``Sylius\\`` prefix.

Templates
~~~~~~~~~

Neither template events, block or templates themselves cannot be deleted or renamed.

Deprecations
------------

Before we remove or replace code covered by this backwards compatibility promise, it is
first deprecated in the next minor release before being removed in the next major release.

A code is marked as deprecated by adding a ``@deprecated`` PHPDoc to
relevant classes, methods, properties:

.. code-block:: php

    /**
     * @deprecated Deprecated since version 1.X. Use XXX instead.
     */

The deprecation message should indicate the version in which the class/method was
deprecated and how the feature was replaced (whenever possible).

A PHP ``\E_USER_DEPRECATED`` error must also be triggered to help people with
the migration:

.. code-block:: php

    @trigger_error(
        'XXX() is deprecated since version 2.X and will be removed in 2.Y. Use XXX instead.',
        \E_USER_DEPRECATED
    );

.. _Semantic Versioning: https://semver.org/
.. _Symfony's Backward Compatibility Promise: https://symfony.com/doc/current/contributing/code/bc.html
.. _Symfony's Experimental Features: https://symfony.com/doc/current/contributing/code/experimental.html
