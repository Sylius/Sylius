.. index::
   single: Channels

Channels
========

In the modern world of e-commerce your website is no longer the only point of sale for your goods.

**Channel** model represents a single sales channel, which can be one of the following things:

* Webstore
* Mobile application
* Cashier in your physical store

Or pretty much any other channel type you can imagine.

**What may differ between channels?** Particularly anything from your shop configuration:

* products,
* currencies,
* locales (language),
* themes,
* hostnames,
* taxes,
* payment and shipping methods.

A **Channel** has a ``code``, a ``name`` and a ``color``.

In order to make the system more convenient for the administrator - there is just one, shared admin panel. Also users are shared among the channels.

.. tip::

   In the dev environment you can easily check what channel you are currently on in the Symfony debug toolbar.

   .. image:: ../../_images/channel_toolbar.png
         :align: center

**How to get the current channel?**

You can get the current channel from the channel context.

.. code-block:: php

   $channel = $this->container->get('sylius.context.channel')->getChannel();

.. note::

   The channel is by default determined basing on the hostname, but you can customize that behaviour.
   To do that you have to implement the ``Sylius\Component\Channel\Context\ChannelContextInterface``
   and register it as a service under the ``sylius.context.channel`` tag. Optionally you can add a ``priority="-64"``
   since the default ChannelContext has a ``priority="-128"``, and by default a ``priority="0"`` is assigned.

.. note::

   Moreover if the channel depends mainly on the request you can implement the ``Sylius\Component\Channel\Context\RequestBased\RequestResolverInterface``
   with its ``findChannel(Request $request)`` method and register it under the ``sylius.context.channel.request_based.resolver`` tag.

Learn more
----------

* :doc:`Channel - Component Documentation </components/Channel/index>`.
