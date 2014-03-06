Conventions
===========

This document describes coding standards and conventions used in 
the Sylius codebase to make it more consistent and predictable. 

Method Names
------------

When an object has a "main" many relation with related "things"
(objects, parameters, ...), the method names are normalized:

  * ``get()``
  * ``set()``
  * ``has()``
  * ``all()``
  * ``replace()``
  * ``remove()``
  * ``clear()``
  * ``isEmpty()``
  * ``add()``
  * ``register()``
  * ``count()``
  * ``keys()``

The usage of these methods are only allowed when it is clear that there
is a main relation:

* a ``CookieJar`` has many ``Cookie`` objects;

* a Service ``Container`` has many services and many parameters (as services
  is the main relation, the naming convention is used for this relation);

* a Console ``Input`` has many arguments and many options. There is no "main"
  relation, and so the naming convention does not apply.

For many relations where the convention does not apply, the following methods
must be used instead (where ``XXX`` is the name of the related thing):

+----------------+-------------------+
| Main Relation  | Other Relations   |
+================+===================+
| ``get()``      | ``getXXX()``      |
+----------------+-------------------+
| ``set()``      | ``setXXX()``      |
+----------------+-------------------+
| n/a            | ``replaceXXX()``  |
+----------------+-------------------+
| ``has()``      | ``hasXXX()``      |
+----------------+-------------------+
| ``all()``      | ``getXXXs()``     |
+----------------+-------------------+
| ``replace()``  | ``setXXXs()``     |
+----------------+-------------------+
| ``remove()``   | ``removeXXX()``   |
+----------------+-------------------+
| ``clear()``    | ``clearXXX()``    |
+----------------+-------------------+
| ``isEmpty()``  | ``isEmptyXXX()``  |
+----------------+-------------------+
| ``add()``      | ``addXXX()``      |
+----------------+-------------------+
| ``register()`` | ``registerXXX()`` |
+----------------+-------------------+
| ``count()``    | ``countXXX()``    |
+----------------+-------------------+
| ``keys()``     | n/a               |
+----------------+-------------------+

.. note::

    While "setXXX" and "replaceXXX" are very similar, there is one notable
    difference: "setXXX" may replace, or add new elements to the relation.
    "replaceXXX", on the other hand, cannot add new elements. If an unrecognized
    key is passed to "replaceXXX" it must throw an exception.

.. _contributing-code-conventions-deprecations:

Deprecations
------------

.. warning::

    Sylius is the pre-alpha development stage.
    We release minor version before every larger change, but be prepared for BC breaks to happen until 1.0.0 release.

From time to time, some classes and/or methods are deprecated in the
framework; that happens when a feature implementation cannot be changed
because of backward compatibility issues, but we still want to propose a
"better" alternative. In that case, the old implementation can simply be
**deprecated**.

A feature is marked as deprecated by adding a ``@deprecated`` phpdoc to
relevant classes, methods, properties, ...::

    /**
     * @deprecated Deprecated since version 1.X, to be removed in 1.Y. Use XXX instead.
     */

The deprecation message should indicate the version when the class/method was
deprecated, the version when it will be removed, and whenever possible, how
the feature was replaced.

A PHP ``E_USER_DEPRECATED`` error must also be triggered to help people with
the migration starting one or two minor versions before the version where the
feature will be removed (depending on the criticality of the removal)::

    trigger_error(
        'XXX() is deprecated since version 2.X and will be removed in 2.Y. Use XXX instead.',
        E_USER_DEPRECATED
    );

