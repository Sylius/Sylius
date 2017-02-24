SyliusMailerBundle
==================

Sending customizable e-mails has never been easier in Symfony.

You can configure different e-mail types in the YAML or in database. (and use YAML as fallback)
This allows you to send out e-mails with one simple method call, providing an unique code and data.

The bundle supports adapters, by default e-mails are rendered using Twig and sent via Swiftmailer, but you can easily implement your own adapter and delegate the whole operation to external API.

This bundle provides easy integration of the :doc:`Sylius Mailer component </components/Mailer/index>`
with any Symfony full-stack application.

.. toctree::
    :maxdepth: 1
    :numbered:

    installation
    your_first_email
    using_custom_adapter
    configuration

Learn more
----------

* :doc:`Emails in the Sylius platform </book/architecture/emails>` - concept documentation
