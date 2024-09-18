.. index::
   single: E-Mails

E-Mails
=======

Sylius is sending various e-mails and this chapter is a reference about all of them. Continue reading to learn what e-mails are sent, when and how to customize the templates.
To understand how e-mail sending works internally, please refer to `SyliusMailerBundle documentation <https://github.com/Sylius/SyliusMailerBundle/blob/master/docs/index.md>`_.
And to learn more about mailer services configuration, read :doc:`the dedicated cookbook </cookbook/emails/mailer>`.

User Confirmation
-----------------

Every time a customer registers via the registration form, a user registration e-mail is sent to them.

**Code**: ``user_registration``

**The default template**: ``@SyliusShop/Email/user_registration.html.twig``

You also have the following parameters available:

* ``user``: Instance of the user model
* ``channel``: Currently used channel
* ``localeCode``: Currently used locale code

Email Verification
------------------

When a customer registers via the registration form, besides the User Confirmation an Email Verification is sent.

**Code**: ``verification_token``

**The default template**: ``@SyliusShop/Email/verification.html.twig``

You also have the following parameters available:

* ``user``: Instance of the user model
* ``channel``: Currently used channel
* ``localeCode``: Currently used locale code

Password Reset
--------------

This e-mail is used when the user requests to reset their password in the login form.

**Code**: ``reset_password_token``

**The default template**: ``@SyliusShop/Email/password_reset.html.twig``

You also have the following parameters available:

* ``user``: Instance of the user model
* ``channel``: Currently used channel
* ``localeCode``: Currently used locale code

Order Confirmation
------------------

This e-mail is sent when order is placed.

**Code**: ``order_confirmation``

**The default template**: ``@SyliusShop/Email/order_confirmation.html.twig``

You also have the following parameters available:

* ``order``: Instance of the order, with all its data
* ``channel``: Channel in which an order was placed
* ``localeCode``: Locale code in which an order was placed

Shipment Confirmation
---------------------

This e-mail is sent when the order's shipping process has started.

**Code**: ``shipment_confirmation``

**The default template**: ``@SyliusAdmin/Email/shipmentConfirmation.html.twig``

You have the following parameters available:

* ``shipment``: Shipment instance
* ``order``: Instance of the order, with all its data
* ``channel``: Channel in which an order was placed
* ``localeCode``: Locale code in which an order was placed

Contact Request
---------------

This e-mail is sent when a customer validates contact form.

**Code**: ``contact_request``

**The default template**: ``@SyliusShop/Email/contact_request.html.twig``

You have the following parameters available:

* ``data``: An array of submitted data from form
* ``channel``: Channel in which an order was placed
* ``localeCode``: Locale code in which an order was placed

.. rst-class:: plus-doc

Return Requests Emails
----------------------

.. hint::

   What are Return Requests? :doc:`Check here </book/orders/returns>`!

Return Request Confirmation
'''''''''''''''''''''''''''

This email is sent after return request has been created by a customer.

**Code**: ``sylius_plus_return_request_confirmation``

**The default template**:
``@SyliusPlusPlugin/Returns/Infrastructure``
``/Resources/views/Emails/returnRequestConfirmation.html.twig``

Parameters:

* ``order`` - for which the return request has been created

Return Request Acceptation
''''''''''''''''''''''''''

This email is sent when the administrator accepts a return request.

**Code**: ``sylius_plus_return_request_accepted``

**The default template**:
``@SyliusPlusPlugin/Returns/Infrastructure``
``/Resources/views/Emails/returnRequestAcceptedNotification.html.twig``

Parameters:

* ``returnRequest`` which has been accepted
* ``order`` of the accepted return request

Return Request Rejection
''''''''''''''''''''''''

This email is sent when the administrator rejects a return request.

**Code**: ``sylius_plus_return_request_rejected``

**The default template**:
``@SyliusPlusPlugin/Returns/Infrastructure``
``/Resources/views/Emails/returnRequestRejectedNotification.html.twig``

Parameters:

* ``returnRequest`` which has been rejected
* ``order`` of the rejected return request


Return Request Resolution Change
''''''''''''''''''''''''''''''''

This email is sent when the administrator changes return request's resolution proposed by a customer.

**Code**: ``sylius_plus_return_request_resolution_changed``

**The default template**:
``@SyliusPlusPlugin/Returns/Infrastructure``
``/Resources/views/Emails/returnRequestResolutionChangedNotification.html.twig``

Parameters:

* ``returnRequest`` whose resolution has been changed
* ``order`` of the modified return request

Return Request: Repaired Items Sent
'''''''''''''''''''''''''''''''''''

This email is sent when the administrator marks that a return request's repaired items have been sent back to the Customer.

**Code**: ``sylius_plus_return_request_repaired_items_sent``

**The default template**:
``@SyliusPlusPlugin/Returns/Infrastructure``
``/Resources/views/Emails/returnRequestRepairedItemsSentNotification.html.twig``

Parameters:

* ``returnRequest`` of which the items were sent
* ``order`` of the return request

.. image:: ../../_images/sylius_plus/banner.png
   :align: center
   :target: https://sylius.com/plus/?utm_source=docs

How to send an Email programmatically?
--------------------------------------

For sending emails **Sylius** is using a dedicated service - **Sender**. Additionally we have **EmailManagers**
for Order Confirmation(`OrderEmailManager <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/ShopBundle/EmailManager/OrderEmailManager.php>`_)
and for Shipment Confirmation(`ShipmentEmailManager <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/AdminBundle/EmailManager/ShipmentEmailManager.php>`_).

.. tip::

    While using **Sender** you have the available emails of Sylius available under constants in:

    * `Core - Emails <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/CoreBundle/Mailer/Emails.php>`_
    * `User - Emails <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/UserBundle/Mailer/Emails.php>`_

Example using **Sender**:

.. code-block:: php

    /** @var SenderInterface $sender */
    $sender = $this->container->get('sylius.email_sender');

    $sender->send(\Sylius\Bundle\UserBundle\Mailer\Emails::EMAIL_VERIFICATION_TOKEN, ['sylius@example.com'], ['user' => $user, 'channel' => $channel, 'localeCode' => $localeCode]);

Example using **EmailManager**:

.. code-block:: php

    /** @var OrderEmailManagerInterface $sender */
    $orderEmailManager = $this->container->get('sylius.email_manager.order');

    $orderEmailManager->sendConfirmationEmail($order);

Learn more
----------

* `Mailer - Documentation <https://github.com/Sylius/SyliusMailerBundle/blob/master/docs/index.md>`_
