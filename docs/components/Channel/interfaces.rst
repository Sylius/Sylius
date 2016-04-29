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

.. _Sylius API ChannelInterface: http://api.sylius.org/Sylius/Component/Channel/Model/ChannelInterface.html

.. _component_channel_model_channel-aware-interface:

ChannelAwareInterface
~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by models associated
with a specific sale channel.

.. note::
   For more detailed information go to `Sylius API ChannelAwareInterface`_.

.. _Sylius API ChannelAwareInterface: http://api.sylius.org/Sylius/Component/Channel/Model/ChannelAwareInterface.html

.. _component_channel_model_channels-aware-interface:

ChannelsAwareInterface
~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by models associated with multiple channels.

.. note::
   For more detailed information go to `Sylius API ChannelsAwareInterface`_.

.. _Sylius API ChannelsAwareInterface: http://api.sylius.org/Sylius/Component/Channel/Model/ChannelsAwareInterface.html

Service Interfaces
------------------

.. _component_channel_context_channel-context-interface:

ChannelContextInterface
~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by a service
responsible for managing the currently used :ref:`component_channel_model_channel`.

.. note::
   For more detailed information go to `Sylius API ChannelContextInterface`_.

.. _Sylius API ChannelContextInterface: http://api.sylius.org/Sylius/Component/Channel/Model/ChannelContextInterface.html

.. _component_channel_repository_channel-repository-interface:

ChannelRepositoryInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by repositories responsible
for storing the :ref:`component_channel_model_channel` objects.

.. note::
   For more detailed information go to `Sylius API ChannelRepositoryInterface`_.

.. _Sylius API ChannelRepositoryInterface: http://api.sylius.org/Sylius/Component/Channel/Model/ChannelRepositoryInterface.html
