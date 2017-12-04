Models
======

.. _component_mailer_model_email:

Email
-----

**Email** object represents an email.
Email has the following properties:

+---------------+-------------------------------------------------------+
| Property      | Description                                           |
+===============+=======================================================+
| id            | Unique id of the email                                |
+---------------+-------------------------------------------------------+
| code          | Code of the email                                     |
+---------------+-------------------------------------------------------+
| enabled       | Indicates whether email is available                  |
+---------------+-------------------------------------------------------+
| subject       | Subject of the email message                          |
+---------------+-------------------------------------------------------+
| content       | Content of the email message                          |
+---------------+-------------------------------------------------------+
| template      | Template of the email                                 |
+---------------+-------------------------------------------------------+
| senderName    | Name of a sender                                      |
+---------------+-------------------------------------------------------+
| senderAddress | Address of a sender                                   |
+---------------+-------------------------------------------------------+
| createdAt     | Date when the email was created                       |
+---------------+-------------------------------------------------------+
| updatedAt     | Date of last change                                   |
+---------------+-------------------------------------------------------+

.. note::
    This model implements the :ref:`component_mailer_model_email-interface`
    For more detailed information go to `Sylius API Email`_.

.. _Sylius API Email: http://api.sylius.org/Sylius/Component/Mailer/Model/Email.html
