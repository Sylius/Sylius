Customizing Translations
========================

.. note::

    We've adopted a convention of overriding translations in the ``app\Resources\translations`` directory.

Why would you customize a translation?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

If you would like to change any of the translation keys defined in Sylius in any desired language.

For example:

* change "Last name" into "Surname"
* change "Add to cart" into "Buy"

There are many other places where you can customize the text content of pages.

How to customize a translation?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

In order to customize a translation in your project:

**1.** If you don't have it yet, create ``app\Resources\translations\messages.en.yml`` for English translations.

.. note::

    You can create different files for different locales (languages). For example ``messages.pl.yml`` should hold only Polish translations,
    as they will be visible when the current locale is ``PL``. Check the :doc:`Locales </book/configuration/locales>` docs for more information.

**2.** In this file, configure the desired key and give it a translation.

If you would like to change the translation of "Email" into "Username" on the login form you have to
override its translation key which is ``sylius.form.customer.email``.

.. code-block:: yaml

    sylius:
        form:
            customer:
                email: Username

.. tip::

    **How to check what the proper translation key is for your message:**
    When you are on the page where you are trying to customize a translation, click the Translations icon in the Symfony Profiler.
    In this section you can see all messages with their associated keys on that page.

    .. image:: ../_images/translations.png
        :align: center
