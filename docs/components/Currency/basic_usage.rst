Basic Usage
===========

Getting a Currency name
-----------------------

.. _Intl: http://symfony.com/doc/current/components/intl.html

.. code-block:: php

   <?php

   use Sylius\Currency\Model\Currency;

   $currency = new Currency();
   $currency->setCode('USD');

   $currency->getName(); // Returns 'US Dollar'.

The ``getName`` method uses Symfony's `Intl`_ class to
convert currency's code into a human friendly form.

.. note::
   The output of ``getName`` may vary as the name is generated accordingly to the set locale.

.. _component_currency_context_currency-context:

CurrencyContext
---------------

The **CurrencyContext** is responsible for keeping the currently
set and default currency names in a given :doc:`/components/Storage/index`.

.. tip::
   You can use a custom storage, as long as it implements the :ref:`component_storage_storage-interface`.

In this example let's use the default :ref:`component_storage_session-storage`.

.. code-block:: php

   <?php

   use Sylius\Currency\Context\CurrencyContext;
   use Sylius\Storage\SessionStorage;
   use Symfony\Component\HttpFoundation\Session\Session;

   $session = new Session();
   $session->start();
   $sessionStorage = new SessionStorage($session);

   $currencyCode = 'USD'; // The currency code which will be used by default in this context.

   $currencyContext = new CurrencyContext($sessionStorage, $currencyCode);

   $currencyContext->getDefaultCurrencyCode(); // Returns 'USD'.
   $currencyContext->getCurrencyCode('GBP'); // Returns 'USD' as the given code is not in storage.
   $currencyContext->setCurrencyCode('GBP');
   $currencyContext->getCurrencyCode('GBP'); // Returns 'GBP' for now it's available in the storage.

Be aware that setting the default currency is done only once while creating the context,
afterwards you cannot change it.

.. note::
   This service implements the :ref:`component_currency_context_currency-context-interface`.

   For more detailed information go to `Sylius API CurrencyContext`_.

.. _Sylius API CurrencyContext: http://api.sylius.org/Sylius/Component/Currency/Context/CurrencyContext.html

.. _component_currency_converter_currency-converter:

CurrencyConverter
-----------------

The **CurrencyConverter** allows you to convert a value accordingly to the exchange rate of specified currency.

.. code-block:: php

   <?php

   use Sylius\Currency\Converter\CurrencyConverter;
   use Sylius\Currency\Model\Currency;
   use Sylius\Resource\Repository\InMemoryRepository;

   $currency = new Currency();
   $currency->setCode('USD');
   $currency->setExchangeRate(1.5);

   $currencyRepository = new InMemoryRepository(); // Let's assume our $currency is already in the repository.

   $currencyConverter = new CurrencyConverter($currencyRepository);

   $currencyConverter->convert(1000, 'USD'); // Returns 1500.

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

   use Sylius\Currency\Provider\CurrencyProvider;
   use Sylius\Resource\Repository\InMemoryRepository;

   $currencyRepository = new InMemoryRepository();
   $currencyProvider = new CurrencyProvider($currencyRepository);

   $currencyProvider->getAvailableCurrencies(); // Returns an array of Currency objects.

The ``getAvailableCurrencies`` method retrieves all currencies which ``enabled``
property is set to true and have been inserted in the given repository.

.. note::
   This service implements the :ref:`component_currency_provider_currency-provider-interface`.

   For more detailed information go to `Sylius API CurrencyProvider`_.

.. _Sylius API CurrencyProvider: http://api.sylius.org/Sylius/Component/Currency/Provider/CurrencyProvider.html
