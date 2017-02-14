.. index::
   single: E-Mails

E-Mails
=======

Sylius is sending various e-mails and this chapter is a reference about all of them. Continue reading to learn what e-mails are sent, when and how to customize the templates.
To understand how e-mail sending works internally, please refer to :doc:`SyliusMailerBundle documentation </bundles/SyliusMailerBundle/index>`.

User Confirmation
-----------------

Every time a customer registers via the registration form, a user registration e-mail is sent to them.

**Code**: ``user_registration``

**The default template**: ``SyliusCoreBundle:Email:userRegistration.html.twig``

You also have the following parameters available:

* ``user``: Instance of the user model

Email Verification
------------------

When a customer registers via the registration form, besides the User Confirmation an Email Verification is sent.

**Code**: ``verification_token``

**The default template**: ``SyliusCoreBundle:Email:verification.html.twig``

You also have the following parameters available:

* ``user``: Instance of the user model

Password Reset
--------------

This e-mail is used when the user requests to reset their password in the login form.

**Code**: ``reset_password_token``

**The default template**: ``SyliusCoreBundle:Email:passwordReset.html.twig``

You also have the following parameters available:

* ``user``: Instance of the user model

Order Confirmation
------------------

This e-mail is sent when order is placed.

**Code**: ``order_confirmation``

**The default template**: ``SyliusCoreBundle:Email:orderConfirmation.html.twig``

You also have the following parameters available:

* ``order``: Instance of the order, with all its data

Shipment Confirmation
---------------------

This e-mail is sent when the order's shipping process has started.

**Code**: ``shipment_confirmation``

**The default template**: ``SyliusCoreBundle:Email:shipmentConfirmation.html.twig``

You have the following parameters available:

* ``shipment``: Shipment instance
* ``order``: Instance of the order, with all its data

How to send an Email programmatically?
--------------------------------------

For sending emails **Sylius** is using a dedicated service - **Sender**. Additionally we have **EmailManagers**
for Order Confirmation(`OrderEmailManager <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/CoreBundle/EmailManager/OrderEmailManager.php>`_)
and for Shipment Confirmation(`ShipmentEmailManager <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/CoreBundle/EmailManager/ShipmentEmailManager.php>`_).

.. tip::

    While using **Sender** you have the available emails of Sylius available under constants in:

    * `Core - Emails <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/CoreBundle/Mailer/Emails.php>`_
    * `User - Emails <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/UserBundle/Mailer/Emails.php>`_

Example using **Sender**:

.. code-block:: php

    /** @var SenderInterface $sender */
    $sender = $this->container->get('sylius.email_sender');

    $sender->send(\Sylius\Bundle\UserBundle\Mailer\Emails::EMAIL_VERIFICATION_TOKEN, ['bannanowa@gmail.com'], ['user' => $user]);

Example using **EmailManager**:

.. code-block:: php

    /** @var OrderEmailManagerInterface $sender */
    $orderEmailManager = $this->container->get('sylius.email_manager.order');

    $orderEmailManager->sendConfirmationEmail($order);

Learn more
----------

* :doc:`Mailer - Component Documentation </components/Mailer/index>`
* :doc:`Mailer - Bundle Documentation </bundles/SyliusMailerBundle/index>`
