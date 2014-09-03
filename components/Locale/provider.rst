LocaleProvider
==============

The **LocaleProvider** allows you to get the available locales, it implements the ``LocaleProviderInterface``.

.. code-block:: php

    $localeRepository = new EntityRepository();
    $localeProvider = new LocaleProvider($localeRepository);
    $availableLocales = $localeProvider->getAvailableLocales();

    foreach ($availableLocales as $locale) {
        echo $locale->getCode();
    }