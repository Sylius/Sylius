.. rst-class:: outdated

Basic Usage
===========

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

LocaleContext
-------------

In the Locale component there are three LocaleContexts defined:
* ``CompositeLocaleContext``
* ``ImmutableLocaleContext``
* ``ProviderBasedLocaleContext``

CompositeLocaleContext
~~~~~~~~~~~~~~~~~~~~~~

It is a composite of different contexts available in your application, which are prioritized while being injected here (the one with highest priority is used).
It has the ``getLocaleCode()`` method available, that helps you to get the currently used locale.

LocaleProvider
--------------

The **LocaleProvider** allows you to get all available locales.

.. code-block:: php

    <?php

    use Sylius\Component\Locale\Provider\LocaleProvider;

    $locales = new InMemoryRepository();

    $localeProvider = new LocaleProvider($locales);

    $localeProvider->getAvailableLocalesCodes(); //Output will be a collection of available locales
    $localeProvider->isLocaleAvailable('en'); //It will check if that locale is enabled
