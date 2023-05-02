.. rst-class:: outdated

Interfaces
==========

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

Model Interfaces
----------------

.. _component_channel_model_channel-interface:

ChannelInterface
~~~~~~~~~~~~~~~~

This interface should be implemented by every custom sale channel model.

.. note::
   This interface extends `TimestampableInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/TimestampableInterface.php>`_ and `CodeAwareInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/CodeAwareInterface.php>`_.

.. _component_channel_model_channel-aware-interface:

ChannelAwareInterface
~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by models associated
with a specific sale channel.

.. _component_channel_model_channels-aware-interface:

ChannelsAwareInterface
~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by models associated with multiple channels.

Service Interfaces
------------------

.. _component_channel_context_channel-context-interface:
