Interfaces
==========

Model Interfaces
----------------

.. _component_channel_model_channel-interface:

ChannelInterface
~~~~~~~~~~~~~~~~

This interface should be implemented by every custom sale channel model.

.. note::
   This interface extends :ref:`component_resource_model_timestampable-interface` and :ref:`component_resource_model_code-aware-interface`.

   For more detailed information go to `Sylius API ChannelInterface`_.

.. _Sylius API ChannelInterface: http://api.sylius.com/Sylius/Component/Channel/Model/ChannelInterface.html

.. _component_channel_model_channel-aware-interface:

ChannelAwareInterface
~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by models associated
with a specific sale channel.

.. note::
   For more detailed information go to `Sylius API ChannelAwareInterface`_.

.. _Sylius API ChannelAwareInterface: http://api.sylius.com/Sylius/Component/Channel/Model/ChannelAwareInterface.html

.. _component_channel_model_channels-aware-interface:

ChannelsAwareInterface
~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by models associated with multiple channels.

.. note::
   For more detailed information go to `Sylius API ChannelsAwareInterface`_.

.. _Sylius API ChannelsAwareInterface: http://api.sylius.com/Sylius/Component/Channel/Model/ChannelsAwareInterface.html

Service Interfaces
------------------

.. _component_channel_context_channel-context-interface:
