.. index::
   single: Currencies

Currencies
==========

Sylius supports multiple currencies per store and makes it very easy to manage them.

There are several approaches to processing several currencies, but we decided to use the simplest solution
we are storing all money values in the **base currency per channel** and convert them to other currencies with exchange rates.

.. note::

    The **base currency** to the first channel is set during the installation of Sylius and it has the **exchange rate** equal to "1.000".

.. tip::

    In the dev environment you can easily check the base currency in the Symfony debug toolbar:

    .. image:: ../../_images/toolbar.png
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

Getting the list of available currencies for a channel
------------------------------------------------------

If you want to get a list of currently available currencies for a given channel,
you can get them from the ``Channel``.
You can also get the current ``Channel`` from the container.

.. code-block:: php

    <?php

    public function fooAction()
    {
        // If you don't have it, you can get the current channel from container
        $channel = $this->container->get('sylius.context.channel')->getChannel();

        $currencies = $channel->getCurrencies();
    }

.. note::

    If you want to learn more about ``Channels``, what they represent, and how they work; read the previous chapter :doc:`Channels </book/configuration/channels>`


Currency Converter
------------------

The ``Sylius\Component\Currency\Converter\CurrencyConverter`` is a service available under the ``sylius.currency_converter`` id.

It allows you to convert money values from one currency to another.

This solution is used for displaying an *approximate* value of price when the desired currency is different from the base currency of the current channel.

Switching Currency of a Channel
-------------------------------

We may of course change the currency used by a channel. For that we have the ``sylius.storage.currency`` service, which implements
the ``Sylius\Component\Core\Currency\CurrencyStorageInterface`` with methods
``->set(ChannelInterface $channel, $currencyCode)`` and ``->get(ChannelInterface $channel)``.

.. code-block:: php

    $container->get('sylius.storage.currency')->set($channel, 'PLN');

Displaying Currencies in the templates
--------------------------------------

There are some useful helpers for rendering money values in the front end.
Simply import the money macros of the ``ShopBundle`` in your twig template and use the functions to display the value:

.. code-block:: twig

    ..
    {% import "@SyliusShop/Common/Macro/money.html.twig" as money %}
    ..

    <span class="price">{{ money.format(price, 'EUR') }}</span>

Sylius provides you with some handy :doc:`Global Twig variables </customization/template>` to facilitate displaying money values even more.

Learn more
----------

* :doc:`Currency - Component Documentation </components_and_bundles/components/Currency/index>`
* :doc:`Pricing Concept Documentation </book/products/pricing>`
