Interfaces
==========

Model Interfaces
----------------

.. _component_currency_model_currency-interface:

CurrencyInterface
~~~~~~~~~~~~~~~~~

This interface provides you with basic management of a currency's code,
name, exchange rate and whether the currency should be enabled or not.

.. note::
   This interface extends :ref:`component_resource_model_code-aware-interface` and :ref:`component_resource_model_timestampable-interface`.

   For more detailed information go to `Sylius API CurrencyInterface`_.

.. _Sylius API CurrencyInterface: http://api.sylius.org/Sylius/Component/Currency/Model/CurrencyInterface.html

Service Interfaces
------------------

.. _component_currency_model_currencies-aware-interface:

CurrenciesAwareInterface
~~~~~~~~~~~~~~~~~~~~~~~~

Any container used to store, and manage currencies should implement this interface.

.. note::
   For more detailed information go to `Sylius API CurrenciesAwareInterface`_.

.. _Sylius API CurrenciesAwareInterface: http://api.sylius.org/Sylius/Component/Currency/Model/CurrenciesAwareInterface.html

.. _component_currency_context_currency-context-interface:

CurrencyContextInterface
~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by a service used for managing the currency name.
It also contains the default storage key:

+------------------+------------------+
| Related constant | Storage key      |
+==================+==================+
| STORAGE_KEY      | _sylius_currency |
+------------------+------------------+

.. note::
   For more detailed information go to `Sylius API CurrencyContextInterface`_.

.. _Sylius API CurrencyContextInterface: http://api.sylius.org/Sylius/Component/Currency/Context/CurrencyContextInterface.html

.. _component_currency_converter_currency-converter-interface:

CurrencyConverterInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by any service used to convert
the amount of money from one currency to another, according to their exchange rates.

.. note::
   For more detailed information go to `Sylius API CurrencyConverterInterface`_.

.. _Sylius API CurrencyConverterInterface: http://api.sylius.org/Sylius/Component/Currency/Converter/CurrencyConverterInterface.html

.. _component_currency_provider_currency-provider-interface:

CurrencyProviderInterface
~~~~~~~~~~~~~~~~~~~~~~~~~

This interface allows you to implement one fast service which gets
all available currencies from any container you would like.

.. note::
   For more detailed information go to `Sylius API CurrencyProviderInterface`_.

.. _Sylius API CurrencyProviderInterface: http://api.sylius.org/Sylius/Component/Currency/Provider/CurrencyProviderInterface.html
