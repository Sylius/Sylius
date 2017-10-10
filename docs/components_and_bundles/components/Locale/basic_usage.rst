Basic Usage
===========

LocaleContext
-------------

CompositeLocaleContext methods:
	1. getLocaleCode() method, a composite of different contexts which are prioritized in it (the one with highest priority is used if it exists). Returns a string (exception: LocaleNotFoundException)
	2. _construct()
	3. addContext(LocaleContextInterface $localeContext, int $priority)
ImmutableLocaleContext methods:
	1. _construct(string $localeCode)
	2. string getLocaleCode(), see above for details
LocaleContextInterface methods:
	1. string getLocaleCode(), see above for details
ProviderBasedLocaleContext methods:
	1. _construct(LocalProviderInterface $localeProvider)
	2. string getLocaleCode(), see above for details

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
