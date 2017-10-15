Basic usage
===========

Sender
------

.. _component_mailer_sender_adapter_abstract-adapter:

SenderAdapter
~~~~~~~~~~~~~

This is an abstraction layer that allows you to create your own logic of sending an email.

.. code-block:: php

    <?php

    use Sylius\Component\Mailer\Sender\Adapter\AbstractAdapter as BaseSenderAdapter;
    use Sylius\Component\Mailer\Model\EmailInterface;
    use Sylius\Component\Mailer\Model\Email;

    class SenderAdapter extends BaseSenderAdapter
    {
        /**
         * Send an e-mail.
         *
         * @param array  $recipients
         * @param string $senderAddress
         * @param string $senderName
         * @param RenderedEmail $renderedEmail
         * @param EmailInterface $email
         */
        public function send(array $recipients, $senderAddress, $senderName, RenderedEmail $renderedEmail, EmailInterface $email, array $data)
        {
            // TODO: Implement send() method.
        }
    }

    $email = new Email();

    $email->setCode('christmas_party_invitation');
    $email->setContent('Hi, we would like to invite you to christmas party');
    $email->setSubject('Christmas party');
    $email->setSenderAddress('mike.ehrmantraut@gmail.com');
    $email->setSenderName('Mike Ehrmantraut');

    $senderAdapter = new SenderAdapter();
    $rendererAdapter = new RendererAdapter();

    $renderedEmail = $rendererAdapter->render($email, $data);

    $senderAdapter->send(array('john.doe@gmail.com'), $email->getSenderAddress(), $email->getSenderName(), $renderedEmail, $email, array())

.. _component_mailer_sender_sender:

Sender
~~~~~~

This service collects those two adapters **SenderAdapter**, **RendererAdapter** and deals with process of sending an email.

.. code-block:: php

    <?php

    use Sylius\Component\Mailer\Provider\DefaultSettingsProvider;
    use Sylius\Component\Mailer\Provider\EmailProvider;
    use Sylius\Component\Mailer\Sender\Sender;

    $sender = new Sender($rendererAdapter, $senderAdapter, $emailProvider, $defaultSettingsProvider);

    $sender->send('christmas_party_invitation', array('mike.ehrmantraut@gmail.com'));


Renderer
--------

.. _component_mailer_renderer_abstract-adapter:

RendererAdapter
~~~~~~~~~~~~~~~

This is an abstraction layer that allows you to create your own logic of rendering an email object.

.. code-block:: php

    <?php

    use Sylius\Component\Mailer\Renderer\Adapter\AbstractAdapter as BaseRendererAdapter;
    use Sylius\Component\Mailer\Model\EmailInterface;
    use Sylius\Component\Mailer\Model\Email;

    class RendererAdapter extends BaseRendererAdapter
    {
        /**
         * Render an e-mail.
         *
         * @param EmailInterface $email
         * @param array $data
         *
         * @return RenderedEmail
         */
        public function render(EmailInterface $email, array $data = array())
        {
            // TODO: Implement render() method.

            return new RenderedEmail($subject, $body);
        }
    }

    $email = new Email();

    $email->setCode('christmas_party_invitation');
    $email->setContent('Hi, we would like to invite you to christmas party');
    $email->setSubject('Christmas party');
    $email->setSenderAddress('mike.ehrmantraut@gmail.com');
    $email->setSenderName('Mike Ehrmantraut');

    $rendererAdapter = new RendererAdapter();
    $renderedEmail = $rendererAdapter->render($email, $data); // It will render an email object based on your implementation.

    $renderedEmail->getBody(); // Output will be Hi, we would .....
    $renderedEmail->getSubject(); // Output will be Christmas party.

.. hint::

    Renderer should return `RenderedEmail`_

.. _RenderedEmail: http://api.sylius.org/Sylius/Component/Mailer/Renderer/RenderedEmail.html

.. _component_mailer_provider_default-settings-provider:

DefaultSettingsProvider
-----------------------

The **DefaultSettingsProvider** is service which provides you with default sender address and sender name.

.. code-block:: php

    <?php

    use Sylius\Component\Mailer\Provider\DefaultSettingsProvider;

    $defaultSettingsProvider = new DefaultSettingsProvider('Mike Ehrmantraut', 'mike.ehrmantraut@gmail.com');

    $defaultSettingsProvider->getSenderAddress(); // mike.ehrmantraut@gmail.com
    $defaultSettingsProvider->getSenderName(); // Output will be Mike Ehrmantraut

.. _component_mailer_provider_email-provider:

EmailProvider
-------------

The **EmailProvider** allows you to get specific email from storage.

.. code-block:: php

    <?php

    use Sylius\Component\Mailer\Provider\EmailProvider;
    use Sylius\Component\Resource\Repository\InMemoryRepository;

    $inMemoryRepository = new InMemoryRepository();

    $configuration = array(
        'christmas_party_invitation' => array(
            'subject' => 'Christmas party',
            'template' => 'email.html.twig',
            'enabled' => true,
            'sender' => array(
                'name' => 'John',
                'address' => 'john.doe@gmail.com',
            ),
        ),
    );

    $emailProvider = new EmailProvider($inMemoryRepository, $configuration);

    $email = $emailProvider->getEmail('christmas_party_invitation'); // This method will search for an email in your storage or in given configuration.

    $email->getCode(); // Output will be christmas_party_invitation.
    $email->getSenderAddress(); // Output will be john.doe@gmail.com.
    $email->getSenderName(); // Output will be John.
