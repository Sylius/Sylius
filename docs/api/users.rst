Users API
=========

Sylius users API endpoint is `/api/users` and it allows for browsing, creating & editing user data.

Index of all users
------------------

To browse all users available in the store you should call the following GET request:

.. code-block:: text

    GET /api/users/

Parameters
~~~~~~~~~~

page
    Number of the page, by default = 1
limit
    Number of items to display per page
criteria[query]
    Username, email or first & last names

Response
~~~~~~~~

Response will contain a paginated list of users.

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "page":1,
        "limit":10,
        "pages":10,
        "total":100,
        "_links":{
            "self":{
                "href":"\/api\/users\/?page=1"
            },
            "first":{
                "href":"\/api\/users\/?page=1"
            },
            "last":{
                "href":"\/api\/users\/?page=12"
            },
            "next":{
                "href":"\/api\/users\/?page=2"
            }
        },
        "_embedded":{
            "items":[
                {
                    "credentials_expired": false,
                    "email": "chelsie.witting@example.com",
                    "email_canonical": "chelsie.witting@example.com",
                    "enabled": true,
                    "expired": false,
                    "groups": [],
                    "id": 481,
                    "locked": false,
                    "password": "EbOLtGHYxJKotA+bdb9BElhXPd8qZsnlo8CjDdCk+qFR22EEZJoOTntBX/M5GUXw2vnEqOKIEVPaJr66yxXqqQ==",
                    "roles": [],
                    "salt": "h9ltmmawvdsk08oocogkws4sg040k04",
                    "username": "chelsie.witting@example.com",
                    "username_canonical": "chelsie.witting@example.com"
                }
            ]
        }
    }

Getting a single user
------------------------

You can view a single user by executing the following request:

.. code-block:: text

    GET /api/users/481

Response
~~~~~~~~

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "credentials_expired": false,
        "email": "chelsie.witting@example.com",
        "email_canonical": "chelsie.witting@example.com",
        "enabled": true,
        "expired": false,
        "groups": [],
        "id": 481,
        "locked": false,
        "password": "EbOLtGHYxJKotA+bdb9BElhXPd8qZsnlo8CjDdCk+qFR22EEZJoOTntBX/M5GUXw2vnEqOKIEVPaJr66yxXqqQ==",
        "roles": [],
        "salt": "h9ltmmawvdsk08oocogkws4sg040k04",
        "username": "chelsie.witting@example.com",
        "username_canonical": "chelsie.witting@example.com"
    }

Create an user
---------------

To create a new user, you can execute the following request:

.. code-block:: text

    POST /api/users/

Parameters
~~~~~~~~~~

firstName
    Firstname of the customer
lastName
    Lastname of the customer
email
    User e-mail
plainPassword
    Password string
enabled *(optional)*
    User account status (boolean)

Response
~~~~~~~~

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "credentials_expired": false,
        "email": "chelsie.witting@example.com",
        "email_canonical": "chelsie.witting@example.com",
        "enabled": true,
        "expired": false,
        "groups": [],
        "id": 481,
        "locked": false,
        "password": "EbOLtGHYxJKotA+bdb9BElhXPd8qZsnlo8CjDdCk+qFR22EEZJoOTntBX/M5GUXw2vnEqOKIEVPaJr66yxXqqQ==",
        "roles": [],
        "salt": "h9ltmmawvdsk08oocogkws4sg040k04",
        "username": "chelsie.witting@example.com",
        "username_canonical": "chelsie.witting@example.com"
    }

Updating a user
------------------

You can update an existing user using PUT or PATCH method:

.. code-block:: text

    PUT /api/users/481

.. code-block:: text

    PATCH /api/users/481

Parameters
~~~~~~~~~~

firstName
    Firstname of the customer
lastName
    Lastname of the customer
email
    User e-mail
plainPassword
    Password string
enabled *(optional)*
    User account status (boolean)

Response
~~~~~~~~

.. code-block:: text

    STATUS: 204 NO CONTENT

Deleting a user
------------------

You can delete (soft) a user from the system by making the following DELETE call:

.. code-block:: text

    DELETE /api/users/24

Response
~~~~~~~~

.. code-block:: text

    STATUS: 204 NO CONTENT

Request password resetting
--------------------------

You can create a new password resetting request by calling the following API endpoint:

.. code-block:: text

    POST /api/password-resetting-requests/

Parameters
~~~~~~~~~~

username
    Username or e-mail

Response
~~~~~~~~

The successful response will contain the user object with a confirmation token and date of password request.

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "confirmation_token": "dzOeNrmdnn20IVHBW2Uaq-yAYsO2sY2hCXhfKdYl_xM",
        "credentials_expired": false,
        "email": "sylius@example.com",
        "email_canonical": "sylius@example.com",
        "enabled": true,
        "expired": false,
        "groups": [],
        "id": 1,
        "last_login": "2014-12-08T13:08:02+0000",
        "locked": false,
        "password_requested_at": "2014-12-08T14:19:26+0000",
        "roles": [
            "ROLE_ADMINISTRATION_ACCESS"
        ],
        "username": "sylius@example.com",
        "username_canonical": "sylius@example.com"
    }

Index of all user orders
------------------------

To browse all orders for specific user, you can do the following call:

.. code-block:: text

    GET /api/users/14/orders/

Parameters
~~~~~~~~~~

page
    Number of the page, by default = 1
limit
    Number of items to display per page
