.. index::
   single: Currencies

Currencies
==========

Sylius supports multiple currencies per store and makes it very easy to manage them.

There are several approaches to processing several currencies, but we decided to use the simplest solution
we are storing all money values in the **base currency** and convert them to other currencies with current rates or specific rates.

.. note::

    The **base currency** is set during the installation of Sylius and it has the **exchange rate** equal to "1.000".

.. tip::

    In the dev environment you can easily check the base currency in the Symfony debug toolbar:

    .. image:: ../_images/toolbar.png
        :align: center

Currency Context
----------------

By default, user can switch the current currency in the frontend of the store.

To manage the currently used currency, we use the **CurrencyContext**. You can always access it through the ``sylius.context.currency`` id.

.. code-block:: php

    <?php

    public function fooAction()
    {
        $currency = $this->get('sylius.context.currency')->getCurrency();
    }

Currency Converter
------------------

The **Sylius\Component\Currency\Converter\CurrencyConverter** is a service available under the ``sylius.currency_converter`` id.

It lets you to convert money values from the base currency to all the other currencies and backwards.

.. code-block:: php

    <?php

    public function fooAction()
    {
        // convert 100 of the base currency (for instance 100$ if USD is your base) to PLN.
        $this->get('sylius.currency_converter')->convertFromBase(100, 'PLN');

        // or the other way - convert 100 PLN to amount in the base currency
        $this->get('sylius.currency_converter')->convertToBase(100, 'PLN');
    }

Available Currencies Provider
-----------------------------

The default menu for selecting currency is using a service - **CurrencyProvider** - with the ``sylius.currency_provider`` id, which returns all enabled currencies.
This is your entry point if you would like override this logic and return different currencies for various scenarios.

.. code-block:: php

    <?php

    public function fooAction()
    {
        $currencies = $this->get('sylius.currency_provider')->getAvailableCurrencies();
    }

Switching Currency of a Channel
-------------------------------

We may of course change the currency used by a channel. For that we have the ``sylius.storage.currency`` service, which implements
the ``Sylius\Component\Core\Currency\CurrencyStorageInterface`` with methods
``->set(ChannelInterface $channel, $currencyCode)`` and ``->get(ChannelInterface $channel)``.

.. code-block:: php

    $container->get('sylius.storage.currency')->set($channel, 'PLN');

Learn more
----------

* :doc:`Currency - Component Documentation </components/Currency/index>`
