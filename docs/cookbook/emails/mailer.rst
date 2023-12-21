How to configure mailer?
========================

There are many services used for sending transactional emails in web applications. You can find for instance
`Mailjet <https://www.mailjet.com>`_, `Mandrill <https://www.mandrill.com>`_ or `SendGrid <https://sendgrid.com>`_ among them.

In Sylius emails are configured the Symfony way, so you can get inspired by the Symfony guides to those mailing services.

We are recommending to use `Symfony Mailer <https://symfony.com/doc/current/mailer.html>`_ where out of the box, you can deliver emails by configuring the ``MAILER_DSN`` variable in your .env file.

If you prefer using the SwiftMailer service you will need to:

1. **Create an account on a mailing service.**
2. **In the your** ``.env`` **file modify/add the** ``MAILER_URL`` **variable.**

.. code-block:: text

    MAILER_URL=gmail://username:password@localhost

Emails delivery is disable for `test`, `dev` and `stage` environments by default. The `prod` environment has delivery turned
on by default, so there is nothing to worry about if you did not change anything about it.

**That's pretty much all! All the other issues are dependent on the service you are using.**

.. warning::

    Remember that the parameters like username or password must not be committed publicly to your repository.
    Save them as environment variables on your server.

Learn More
----------

* :doc:`Emails Concept </book/architecture/emails>`
* `Sending configurable e-mails in Symfony Blogpost <https://sylius.com/blog/sending-configurable-e-mails-in-symfony>`_
