Models
======

Customer
--------

The customer is represented as a **Customer** instance. It should have everything
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
| group          | Customer's groups                                 | Collection    |
+----------------+---------------------------------------------------+---------------+
| createdAt      | Date of creation                                  | \DateTime     |
+----------------+---------------------------------------------------+---------------+
| updatedAt      | Date of update                                    | \DateTime     |
+----------------+---------------------------------------------------+---------------+

.. note::

    This model implements ``CustomerInterface``

User
----

The registered user is represented as an **User** instance. It should have everything
concerning application user preferences and a corresponding **Customer** instance.
As default has the following properties:

+---------------------+-------------------------------------------------------+-------------------+
| Property            | Description                                           | Type              |
+=====================+=======================================================+===================+
| id                  | Unique id of the user                                 | integer           |
+---------------------+-------------------------------------------------------+-------------------+
| customer            | Customer which is associated to this user (required)  | CustomerInterface |
+---------------------+-------------------------------------------------------+-------------------+
| username            | User's username                                       | string            |
+---------------------+-------------------------------------------------------+-------------------+
| usernameCanonical   | Normalized representation of a username (lowercase)   | string            |
+---------------------+-------------------------------------------------------+-------------------+
| enabled             | Indicates whether user is enabled                     | bool              |
+---------------------+-------------------------------------------------------+-------------------+
| salt                | Additional input to a function that hashes a password | string            |
+---------------------+-------------------------------------------------------+-------------------+
| password            | Encrypted password, must be persisted                 | string            |
+---------------------+-------------------------------------------------------+-------------------+
| plainPassword       | Password before encryption, must not be persisted     | string            |
+---------------------+-------------------------------------------------------+-------------------+
| lastLogin           | Last login date                                       | \DateTime         |
+---------------------+-------------------------------------------------------+-------------------+
| confirmationToken   | Random string used to verify user                     | string            |
+---------------------+-------------------------------------------------------+-------------------+
| passwordRequestedAt | Date of password request                              | \DateTime         |
+---------------------+-------------------------------------------------------+-------------------+
| locked              | Indicates whether user is locked                      | bool              |
+---------------------+-------------------------------------------------------+-------------------+
| expiresAt           | Date when user account will expire                    | \DateTime         |
+---------------------+-------------------------------------------------------+-------------------+
| credentialExpiresAt | Date when user account credentials will expire        | \DateTime         |
+---------------------+-------------------------------------------------------+-------------------+
| roles               | Security roles of a user                              | array             |
+---------------------+-------------------------------------------------------+-------------------+
| oauthAccounts       | Associated OAuth accounts                             | Collection        |
+---------------------+-------------------------------------------------------+-------------------+
| createdAt           | Date of creation                                      | \DateTime         |
+---------------------+-------------------------------------------------------+-------------------+
| updatedAt           | Date of update                                        | \DateTime         |
+---------------------+-------------------------------------------------------+-------------------+

.. note::

    This model implements ``UserInterface``

CustomerGroup
-------------

The customer group is represented as a **CustomerGroup** instance. It can be used to classify customers.
As default has the following properties:

+----------+------------------------+---------+
| Property | Description            | Type    |
+==========+========================+=========+
| id       | Unique id of the group | integer |
+----------+------------------------+---------+
| name     | Group name             | string  |
+----------+------------------------+---------+

.. note::

    This model implements ``CustomerGroupInterface``

UserOAuth
---------

The user OAuth account is represented as an **UserOAuth** instance. It has all data
concerning OAuth account and as default has the following properties:

+-------------+----------------------------+---------------+
| Property    | Description                | Type          |
+=============+============================+===============+
| id          | Unique id of the customer  | integer       |
+-------------+----------------------------+---------------+
| provider    | OAuth provider name        | string        |
+-------------+----------------------------+---------------+
| identifier  | OAuth identifier           | string        |
+-------------+----------------------------+---------------+
| accessToken | OAuth access token         | string        |
+-------------+----------------------------+---------------+
| user        | Corresponding user account | UserInterface |
+-------------+----------------------------+---------------+

.. note::

    This model implements ``UserOAuthInterface``
