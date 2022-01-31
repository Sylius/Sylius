.. rst-class:: outdated

Models
======

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

.. _component_locale_model_locale:

Locale
------

**Locale** represents one locale available in the application.
It uses `Symfony Intl component`_ to return locale name.
Locale has the following properties:

+-------------+-----------------------------------------+
| Property    | Description                             |
+=============+=========================================+
| id          | Unique id of the locale                 |
+-------------+-----------------------------------------+
| code        | Locale's code                           |
+-------------+-----------------------------------------+
| createdAt   | Date when locale was created            |
+-------------+-----------------------------------------+
| updatedAt   | Date of last change                     |
+-------------+-----------------------------------------+

.. hint::
    This model has one const ``STORAGE_KEY`` it is key used to store the locale in storage.

.. note::
    This model implements the :ref:`component_locale_model_locale-interface`

.. _Symfony Intl component: https://symfony.com/doc/current/components/intl.html
