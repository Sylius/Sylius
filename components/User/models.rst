Models
======

Customer
--------

The customer is represented as  **Customer** instance. It should have eveything
concerning personal data and as default has the following properties:

+----------------+---------------------------------------------------+---------------+
| Property       | Description                                       | Type          |
+================+===================================================+===============+
| id             | Unique id of the customer                         | integer       |
+----------------+---------------------------------------------------+---------------+
| email          | Customer's email                                  | string        |
+----------------+---------------------------------------------------+---------------+
| emailCanonical | Normalized representation of an email (lowercase) | string        |
+----------------+---------------------------------------------------+---------------+
| firstName      | Customer's first name                             | string        |
+----------------+---------------------------------------------------+---------------+
| lastName       | Customer's last name                              | string        |
+----------------+---------------------------------------------------+---------------+
| birthday       | Customer's birthday                               | \DateTime     |
+----------------+---------------------------------------------------+---------------+
| gender         | Customer's gender                                 | string        |
+----------------+---------------------------------------------------+---------------+
| user           | Corresponding user object                         | UserInterface |
+----------------+---------------------------------------------------+---------------+
| groups         | Customer's groups                                 | Collection    |
+----------------+---------------------------------------------------+---------------+
| createdAt      | Date of creation                                  | \DateTime     |
+----------------+---------------------------------------------------+---------------+
| updatedAt      | Date of update                                    | \DateTime     |
+----------------+---------------------------------------------------+---------------+
| deletedAt      | Delete date                                       | \DateTime     |
+----------------+---------------------------------------------------+---------------+

.. note::

    This model implements ``CustomerInterface`` and ``GroupableInterface``
