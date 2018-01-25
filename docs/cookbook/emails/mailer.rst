How to configure mailer?
========================

There are many services used for sending transactional emails in web applications. You can find for instance
`Mailjet <https://www.mailjet.com>`_, `Mandrill <http://www.mandrill.com>`_ or `SendGrid <https://sendgrid.com>`_ among them.

In Sylius emails are configured the Symfony way, so you can get inspired by the Symfony guides to those mailing services.

Basically to start sending emails via a mailing service you will need to:

**1. Create an account on a mailing service.**
**2. In the** `app/config/parameters.yml` **modify such parameters:**

.. code-block:: yaml

    # By default it is set to "swiftmailer", we need to have "smtp" here
    mailer_transport: smtp

    # The mailer host may be called "SMTP Server" for some services. Copy its name from your mailing provider and paste here.
    mailer_host: ...

    # These are Username and Password provided by the service for your account
    mailer_user: ...
    mailer_password: ~

    # Here you provide a Mailing port suggested by your service. It can be 25, 465 or 587
    # This parameter is not there by default, you need to add it.
    mailer_port: 25

**3. **Remember not to have the** `disable_delivery: true` **parameter in the** `app/config/config_prod.yml` for your production environment.

Emails delivery is disable for `test`, `dev` and `stage` environments by default. The `prod` environment has delivery turned
on by default, so there is nothing to worry about if you did not change anything about it.

**That's pretty much all! All the other issues are dependent on the service you are using.**

.. warning::

    Remember that the parameters like username or password must not be commited publicly to your repository.
    Save them as environment variables on your server.

Learn More
----------

* :doc:`Emails Concept </book/architecture/emails>`
* `Sending configurable e-mails in Symfony Blogpost <http://sylius.org/blog/sending-configurable-e-mails-in-symfony>`_
