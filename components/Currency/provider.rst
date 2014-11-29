Currency provider
=================

The **CurrencyProvider** allows you to get the available currencies, it implements the ``CurrencyProviderInterface``.

.. code-block:: php

    $currencyRepository = new EntityRepository();
    $currencyProvider = new CurrencyProvider($currencyRepository);
    $availableCurrencies = $currencyProvider->getAvailableCurrencies();

    foreach ($availableCurrencies as $currency) {
        echo $currency->getCode();
    }