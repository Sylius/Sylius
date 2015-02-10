Email
=====

Every type of e-mail in your application is represented by **Email** model.

+-----------------+-------------------------------------+------------+
| Method          | Description                         | Type       |
+=================+=====================================+============+
| code            | Code of the email                   | string     |
+-----------------+-------------------------------------+------------+
| subject         | Subject                             | string     |
+-----------------+-------------------------------------+------------+
| content         | Content (optional)                  | string     |
+-----------------+-------------------------------------+------------+
| template        | Template (optional)                 | string     |
+-----------------+-------------------------------------+------------+
| senderName      | (optional)                          | string     |
+-----------------+-------------------------------------+------------+
| senderAddress   | (optional)                          | string     |
+-----------------+-------------------------------------+------------+
| enabled         | Is email active?                    | boolean    |
+-----------------+-------------------------------------+------------+
| createdAt       | Date of creation                    | \DateTime  |
+-----------------+-------------------------------------+------------+
| updatedAt       | Date of last update                 | \DateTime  |
+-----------------+-------------------------------------+------------+

.. note::

    This model implements ``EmailInterface``.