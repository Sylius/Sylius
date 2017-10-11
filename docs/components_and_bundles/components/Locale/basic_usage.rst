Basic Usage
===========

LocaleContext
-------------

In the Locale component there are three LocaleContexts defined:
* `CompositeLocaleContext`
* `ImmutableLocaleContext`
* `ProviderBasedLocaleContext`

CompositeLocaleContext
~~~~~~~~~~~~~~~~~~~~~~
	It is a composite of different contexts available in your application, which are prioritized while being injected here (the one with highest priority is used).
	It has the `getLocaleCode`, `_construct()`, and `addContext(LocaleContextInterface $localeContext, int $priority)` methods available.

LocaleProvider
--------------

The **LocaleProvider** allows you to get all available locales.

.. code-block:: php

    <?php

    use Sylius\Component\Locale\Provider\LocaleProvider;

    $locales = new InMemoryRepository();

    $localeProvider = new LocaleProvider($locales);

    $localeProvider->getAvailableLocalesCodes() //Output will be a collection of available locales
    $localeProvider->isLocaleAvailable('en') //It will check if that locale is enabled

.. note::
    For more detailed information go to `Sylius API LocaleProvider`_.

.. _Sylius API LocaleProvider: http://api.sylius.org/Sylius/Component/Locale/Provider/LocaleProvider.html
