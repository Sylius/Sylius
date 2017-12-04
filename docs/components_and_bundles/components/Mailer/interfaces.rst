Interfaces
==========

Model Interfaces
----------------

.. _component_mailer_model_email-interface:

EmailInterface
~~~~~~~~~~~~~~

This interface should be implemented by model representing a single type of Email.

.. note::
    This interface extends :ref:`component_resource_model_code-aware-interface` and :ref:`component_resource_model_timestampable-interface`.

    For more detailed information go to `Sylius API EmailInterface`_.

.. _Sylius API EmailInterface: http://api.sylius.org/Sylius/Component/Mailer/Model/EmailInterface.html

Service Interfaces
------------------

.. _component_mailer_provider_default-settings-provider-interface:

DefaultSettingsProviderInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface provides methods for retrieving default sender name nad address.

.. note::
    For more detailed information go to `Sylius API DefaultSettingsProviderInterface`_.

.. _Sylius API DefaultSettingsProviderInterface: http://api.sylius.org/Sylius/Component/Mailer/Provider/DefaultSettingsProviderInterface.html

.. _component_mailer_provider_email-provider-interface:

EmailProviderInterface
~~~~~~~~~~~~~~~~~~~~~~

This interface provides methods for retrieving an email from storage.

.. note::
    For more detailed information go to `Sylius API EmailProviderInterface`_.

.. _Sylius API EmailProviderInterface: http://api.sylius.org/Sylius/Component/Mailer/Provider/EmailProviderInterface.html

Sender
~~~~~~

The **Sender** it is way of sending emails

.. _component_mailer_sender_adapter_adapter-interface:

AdapterInterface
^^^^^^^^^^^^^^^^

This interface provides methods for sending an email. This is an abstraction layer to provide flexibility of mailer component.
The Adapter is injected into sender thanks to this you are free to inject your own logic of sending an email, one thing you should do is just implement this interface.

.. _component_mailer_sender_sender-interface:

SenderInterface
^^^^^^^^^^^^^^^

This interface provides methods for sending an email.

Renderer
~~~~~~~~

.. _component_mailer_renderer_adapter_adapter-interface:

AdapterInterface
^^^^^^^^^^^^^^^^

This interface provides methods for rendering an email. The Adapter is inject into sender for rendering email's content.
