Using Custom Adapter
====================

There are certain use cases, where you do not want to send the e-mail from your app, but delegate the task to an external API.

It is really simple with Adapters system!

Implement Your Adapter
----------------------

Create your adapter class and add your custom logic for sending:

.. code-block:: php

    <?php

    namespace App\Mailer\Adapter;

    use Sylius\Component\Mailer\Sender\Adapter\AbstractAdapter;
    use Sylius\Component\Mailer\Model\EmailInterface;
    use Sylius\Component\Mailer\Renderer\RenderedEmail;

    class CustomAdapter extends AbstractAdapter
    {
        public function send(array $recipients, $senderAddress, $senderName, RenderedEmail $renderedEmail, EmailInterface $email, array $data) 
        {
            // Your custom logic.
        }
    }

Register New Adapter In Container
---------------------------------

In your ``services.yml`` file, simply add your adapter definition.

.. code-block:: yaml

    services:
        app.email_sender.adapter.custom:
            parent: sylius.email_sender.adapter.abstract
            class: App\Mailer\Adapter\CustomAdapter

Configure The New Adapter
-------------------------

Now you just need to put service name under ``sylius_mailer`` configuration in ``app/config/config.yml``.

.. code-block:: yaml

    sylius_mailer:
        sender_adapter: app.email_sender.adapter.custom

That's it! Your new adapter will be used to send out e-mails. You can do whatever you want there!
