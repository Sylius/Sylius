Basic Usage
===========

.. _component_channel_context_channel-context:

ChannelContext
--------------

The **ChannelContext** allows you to manage the currently used sale channel.

.. code-block:: php

   <?php

   use Sylius\Channel\Context\ChannelContext;
   use Sylius\Channel\Model\Channel;

   $channel = new Channel();
   $channelContext = new ChannelContext();

   $channelContext->setChannel($channel);

   $channelContext->getChannel(); // will return the $channel object

.. note::
   This service implements :ref:`component_channel_context_channel-context-interface`.
