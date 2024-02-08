.. rst-class:: outdated

Interfaces
==========

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

Model Interfaces
----------------

.. _component_currency_model_currency-interface:

CurrencyInterface
~~~~~~~~~~~~~~~~~

This interface provides you with basic management of a currency's code,
name, exchange rate and whether the currency should be enabled or not.

.. note::
   This interface extends `CodeAwareInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/CodeAwareInterface.php>`_ and `TimestampableInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/TimestampableInterface.php>`_.

Service Interfaces
------------------

.. _component_currency_model_currencies-aware-interface:

CurrenciesAwareInterface
~~~~~~~~~~~~~~~~~~~~~~~~

Any container used to store, and manage currencies should implement this interface.

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

.. _component_currency_converter_currency-converter-interface:

CurrencyConverterInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by any service used to convert
the amount of money from one currency to another, according to their exchange rates.

.. _component_currency_provider_currency-provider-interface:

CurrencyProviderInterface
~~~~~~~~~~~~~~~~~~~~~~~~~

This interface allows you to implement one fast service which gets
all available currencies from any container you would like.
