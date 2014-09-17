.. index::
   single: Currencies

Currencies
==========

Sylius supports multiple currencies per store and makes it very easy to manage them.

There are several approaches to processing several currencies, but we decided to use the simplest solution
we store all money values in the default currency and convert them to different currency with current rates or specific rates.

Every currency is represented by *Currency* entity and holds basic information:

* id
* code
* exchangeRate
* enabled
* createdAt
* updatedAt

The default currency has exchange rate of "1.000".

Currency Context
----------------

By default, user can switch his current currency in the frontend of the store.

To manage the currently used currency, we use **CurrencyContext**. You can always access it through ``sylius.context.currency`` id.

.. code-block:: php

    <?php

    public function fooAction()
    {
        $currency = $this->get('sylius.context.currency')->getCurrency();
    }

To change the currently used currency, you can simply use the ``setCurrency()`` method of context service.

.. code-block:: php

    <?php

    public function fooAction()
    {
        $this->get('sylius.context.currency')->setCurrency('PLN');
    }

The currency context can be injected into your custom service and give you access to currently used currency.

Product Prices
--------------

...

Available Currencies Provider
-----------------------------

The default menu for selecting currency is using a special service called ``sylius.currency_provider``, which returns all enabled currencies.
This is your entry point if you would like override this logic and return different currencies for various scenarios.

.. code-block:: php

    <?php

    public function fooAction()
    {
        $currencies = $this->get('sylius.currency_provider')->getAvailableCurrencies();

        foreach ($currencies as $currency) {
            echo $currency->getCode();
        }
    }

Final Thoughts
--------------

...

Learn more
----------

* ...
