Basic Usage
===========

LocaleContext
-------------

CompositeLocaleContext
~~~~~~~~~~~~~~~~~~~~~~
	. getLocaleCode() method, a composite of different contexts which are prioritized in it (the one with highest priority is used if it exists). Returns a string (exception: LocaleNotFoundException)
	. _construct()
	. addContext(LocaleContextInterface $localeContext, int $priority)

ImmutableLocaleContext
~~~~~~~~~~~~~~~~~~~~~~
	. _construct(string $localeCode)
	. string getLocaleCode(), see above for details

LocaleContextInterface
~~~~~~~~~~~~~~~~~~~~~~
	. string getLocaleCode(), see above for details

ProviderBasedLocaleContext
~~~~~~~~~~~~~~~~~~~~~~~~~~
	. _construct(LocalProviderInterface $localeProvider)
	. string getLocaleCode(), see above for details

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
