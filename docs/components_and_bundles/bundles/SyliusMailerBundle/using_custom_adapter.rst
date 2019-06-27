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

Register And Configure New Adapter In Container
-----------------------------------------------

In your ``config/packages/sylius_mailer.yaml`` file, add your adapter definition and configure the mailer to use it.

.. code-block:: yaml

    services:
        app.email_sender.adapter.custom:
            parent: sylius.email_sender.adapter.abstract
            class: App\Mailer\Adapter\CustomAdapter

    sylius_mailer:
        sender_adapter: app.email_sender.adapter.custom

That's it! Your new adapter will be used to send out e-mails. You can do whatever you want there!
