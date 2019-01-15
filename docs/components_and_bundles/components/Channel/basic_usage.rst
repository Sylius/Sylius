.. rst-class:: outdated

Basic Usage
===========

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

.. _component_channel_context_channel-context:

ChannelContext
--------------

The **ChannelContext** allows you to manage the currently used sale channel.

.. code-block:: php

   <?php

   use Sylius\Component\Channel\Context\ChannelContext;
   use Sylius\Component\Channel\Model\Channel;

   $channel = new Channel();
   $channelContext = new ChannelContext();

   $channelContext->setChannel($channel);

   $channelContext->getChannel(); // will return the $channel object
