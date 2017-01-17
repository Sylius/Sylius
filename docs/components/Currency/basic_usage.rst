Basic Usage
===========

Getting a Currency name
-----------------------

.. _Intl: http://symfony.com/doc/current/components/intl.html

.. code-block:: php

   <?php

   use Sylius\Component\Currency\Model\Currency;

   $currency = new Currency();
   $currency->setCode('USD');

   $currency->getName(); // Returns 'US Dollar'.

The ``getName`` method uses Symfony's `Intl`_ class to
convert currency's code into a human friendly form.

.. note::
   The output of ``getName`` may vary as the name is generated accordingly to the set locale.

.. _component_currency_converter_currency-converter:

CurrencyConverter
-----------------

The **CurrencyConverter** allows you to convert a value accordingly to the exchange rate of specified currency.

This behaviour is used just for displaying the *approximate* value in another currency than the base currency of the channel.

.. note::

   This service implements the :ref:`component_currency_converter_currency-converter-interface`.

   For more detailed information go to `Sylius API CurrencyConverter`_.

.. _Sylius API CurrencyConverter: http://api.sylius.org/Sylius/Component/Currency/Converter/CurrencyConverter.html

.. caution::

   Throws :ref:`component_currency_converter_unavailable-currency-exception`.

.. _component_currency_provider_currency-provider:

CurrencyProvider
----------------

The **CurrencyProvider** allows you to get all available currencies.

.. code-block:: php

   <?php

   use Sylius\Component\Currency\Provider\CurrencyProvider;
   use Sylius\Component\Resource\Repository\InMemoryRepository;

   $currencyRepository = new InMemoryRepository();
   $currencyProvider = new CurrencyProvider($currencyRepository);

   $currencyProvider->getAvailableCurrencies(); // Returns an array of Currency objects.

The ``getAvailableCurrencies`` method retrieves all currencies which ``enabled``
property is set to true and have been inserted in the given repository.

.. note::
   This service implements the :ref:`component_currency_provider_currency-provider-interface`.

   For more detailed information go to `Sylius API CurrencyProvider`_.

.. _Sylius API CurrencyProvider: http://api.sylius.org/Sylius/Component/Currency/Provider/CurrencyProvider.html
