Importers
=========

Importers allow you to get up-to-date exchange rates from given provider.
The rates are set from your chosen base currency to all other managed currencies.

.. _component_currency_importer_abstract-importer:

AbstractImporter
----------------

The abstract class **AbstractImporter** provides you with a handy method called ``updateOrCreate``
which manages the update of existing currencies or creating new if the ones given don't exist.
After that it saves all your currencies to a database.

.. note::
   This class implements the :ref:`component_currency_importer_importer-interface`.

By default there are two importers already implemented:

* :ref:`component_currency_importer_european-central-bank-importer`
* :ref:`component_currency_importer_open-exchange-rates-importer`

.. tip::
   Of course you are not limited to the default importers as you can simply make your own!

   The only thing you need to do is extend the **AbstractImporter**.

.. _component_currency_importer_european-central-bank-importer:

EuropeanCentralBankImporter
---------------------------

The source of **EuropeanCentralBankImporter**'s data is the `European Central Bank`_.

.. _European Central Bank: http://www.ecb.int

.. code-block:: php

   <?php

   use Sylius\Component\Currency\Importer\EuropeanCentralBankImporter;
   use Sylius\Component\Resource\Repository\InMemoryRepository;
   // Also use a class implementing the Doctrine's ObjectManager interface.

   $manager = // Your ObjectManager instance.
   $repository = new InMemoryRepository();

   $baseCurrency = 'EUR';
   $managedCurrencies = array('USD', 'JPY', 'GBP');

   $euroImporter = new EuropeanCentralBankImporter($manager, $repository);

   $euroImporter->configure(array('base_currency' => $baseCurrency)); // Sets base currency for this importer.

   $euroImporter->import($managedCurrencies); // Updates exchange rates for managed currencies.

.. note::
   This service extends the :ref:`component_currency_importer_abstract-importer`.

   For more detailed information go to `Sylius API EuropeanCentralBankImporter`_.

.. _Sylius API EuropeanCentralBankImporter: http://api.sylius.org/Sylius/Component/Currency/Importer/EuropeanCentralBankImporter.html

.. _component_currency_importer_open-exchange-rates-importer:

OpenExchangeRatesImporter
-------------------------

The **OpenExchangeRatesImporter** gets it's data from `Open Exchange Rates`_.

.. _Open Exchange Rates: http://openexchangerates.org

.. code-block:: php

   use Sylius\Component\Currency\Importer\OpenExchangeRatesImporter;
   use Sylius\Component\Resource\Repository\InMemoryRepository;
   // Also use a class implementing the Doctrine's ObjectManager interface.

   $manager = // Your ObjectManager instance.
   $repository = new InMemoryRepository();

   $app_id = 'YOUR_OER_APP_ID';
   $managedCurrencies = array('USD', 'JPY', 'GBP');

   $openImporter = new OpenExchangeRatesImporter($manager, $repository);

   $openImporter->configure(array('app_id' => $app_id)); // Sets app id for this importer.

   $openImporter->import($managedCurrencies); // Updates exchange rates for managed currencies.

.. note::
   This service extends the :ref:`component_currency_importer_abstract-importer`.

   For more detailed information go to `Sylius API OpenExchangeRatesImporter`_.

.. _Sylius API OpenExchangeRatesImporter: http://api.sylius.org/Sylius/Component/Currency/Importer/OpenExchangeRatesImporter.html
