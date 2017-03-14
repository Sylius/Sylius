Admin Users API
===============

These endpoints will allow you to easily manage admin users. Base URI is `/api/v1/users`.

Admin User API response structure
---------------------------------

If you request an admin user via API, you will receive an object with the following fields:

+----------+---------------------------------+
| Field    | Description                     |
+==========+=================================+
| id       | Admin user's id                 |
+----------+---------------------------------+
| username | Admin user's name               |
+----------+---------------------------------+
| email    | Admin user's email              |
+----------+---------------------------------+
| enabled  | Flag set if the user is enabled |
+----------+---------------------------------+

If you request for more detailed data, you will receive an object with the following fields:

+-------------------+-------------------------------------------------------+
| Field             | Description                                           |
+===================+=======================================================+
| id                | Admin user's id                                       |
+-------------------+-------------------------------------------------------+
| username          | Admin user's name                                     |
+-------------------+-------------------------------------------------------+
| email             | Admin user's email                                    |
+-------------------+-------------------------------------------------------+
| enabled           | Flag set if the user is enabled                       |
+-------------------+-------------------------------------------------------+
| usernameCanonical | Username of the admin user in canonical form          |
+-------------------+-------------------------------------------------------+
| emailCanonical    | Email of the admin user in canonical form             |
+-------------------+-------------------------------------------------------+
| roles             | Roles of the admin user                               |
+-------------------+-------------------------------------------------------+
| firstName         | The admin user's first name                           |
+-------------------+-------------------------------------------------------+
| lastName          | The admin user's last name                            |
+-------------------+-------------------------------------------------------+
| localeCode        | Code of the language, which is used by the admin user |
+-------------------+-------------------------------------------------------+

.. note::

    Read more about :doc:`User model in the component docs</components/User/models>`.

Creating an Admin User
----------------------

To create a new admin user you will need to call the ``/api/v1/users/`` endpoint with the ``POST`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    POST /api/v1/users/

+---------------+----------------+-------------------------------------------------------+
| Parameter     | Parameter type | Description                                           |
+===============+================+=======================================================+
| Authorization | header         | Token received during authentication                  |
+---------------+----------------+-------------------------------------------------------+
| username      | request        | Admin user name                                       |
+---------------+----------------+-------------------------------------------------------+
| email         | request        | Admin user email                                      |
+---------------+----------------+-------------------------------------------------------+
| plainPassword | request        | Admin user password                                   |
+---------------+----------------+-------------------------------------------------------+
| localeCode    | request        | Code of the language, which is used by the admin user |
+---------------+----------------+-------------------------------------------------------+


Example
^^^^^^^

To create a new admin user use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/users/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
                "username": "Balrog",
                "email": "teamEvil@middleearth.com",
                "plainPassword": "youShallNotPass",
                "localeCode": "en_US"
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "id": 7,
        "username": "Balrog",
        "usernameCanonical": "barlog",
        "roles": [
            "ROLE_ADMINISTRATION_ACCESS"
        ],
        "email": "teamEvil@middleearth.com",
        "emailCanonical": "teamevil@middleearth.com",
        "enabled": false
    }

.. warning::

    If you try to create an admin user without username, email, password or locale's code, you will receive a ``400 Bad Request`` error,
    that will contain validation errors.

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/users/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
       -X POST

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 400 Bad Request

.. code-block:: json

    {
        "code": 400,
        "message": "Validation Failed",
        "errors": {
            "children": {
                "username": {
                    "errors": [
                        "Please enter your name."
                    ]
                },
                "email": {
                    "errors": [
                        "Please enter your email."
                    ]
                },
                "plainPassword": {
                    "errors": [
                        "Please enter your password."
                    ]
                },
                "enabled": {},
                "firstName": {},
                "lastName": {},
                "localeCode": {
                    "errors": [
                        "Please choose a locale."
                    ]
                }
            }
        }
    }

You can also create an admin user with additional (not required) fields:

+-----------+----------------+---------------------------------+
| Parameter | Parameter type | Description                     |
+===========+================+=================================+
| enabled   | request        | Flag set if the user is enabled |
+-----------+----------------+---------------------------------+
| firstName | request        | The admin user's first name     |
+-----------+----------------+---------------------------------+
| lastName  | request        | The admin user's last name      |
+-----------+----------------+---------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/users/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
                "firstName": "Balrog",
                "lastName": "of Morgoth",
                "username": "Balrog",
                "email": "teamEvil@middleearth.com",
                "plainPassword": "youShallNotPass",
                "localeCode": "en_US",
                "enabled": "true"
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "id": 9,
        "username": "Balrog",
        "usernameCanonical": "barlog",
        "roles": [
            "ROLE_ADMINISTRATION_ACCESS"
        ],
        "email": "teamEvil@middleearth.com",
        "emailCanonical": "teamevil@middleearth.com",
        "enabled": true,
        "firstName": "Balrog",
        "lastName": "of Morgoth"
    }

Getting a Single Admin User
---------------------------

To retrieve the details of an admin user you will need to call the ``/api/v1/users/{id}`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/users/{id}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| id            | url attribute  | Id of the admin user                 |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To see the details for the admin user with ``id = 9`` use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/users/9 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

.. note::

    The *9* id is an exemplary value. Your value can be different.
    Check in the list of all admin users if you are not sure which id should be used.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "id": 9,
        "username": "Balrog",
        "usernameCanonical": "barlog",
        "roles": [
            "ROLE_ADMINISTRATION_ACCESS"
        ],
        "email": "teamEvil@middleearth.com",
        "emailCanonical": "teamevil@middleearth.com",
        "enabled": true,
        "firstName": "Balrog",
        "lastName": "of Morgoth"
    }

Collection of Admin Users
-------------------------

To retrieve a paginated list of admin users you will need to call the ``/api/v1/users/`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/users/

+-------------------------------------+----------------+---------------------------------------------------+
| Parameter                           | Parameter type | Description                                       |
+=====================================+================+===================================================+
| Authorization                       | header         | Token received during authentication              |
+-------------------------------------+----------------+---------------------------------------------------+
| limit                               | query          | *(optional)* Number of items to display per page, |
|                                     |                | by default = 10                                   |
+-------------------------------------+----------------+---------------------------------------------------+

To see the first page of all admin users use the below method:

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/users/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "page": 1,
        "limit": 4,
        "pages": 1,
        "total": 3,
        "_links": {
            "self": {
                "href": "\/api\/v1\/users\/?sorting%5Bcode%5D=desc&page=1&limit=4"
            },
            "first": {
                "href": "\/api\/v1\/users\/?sorting%5Bcode%5D=desc&page=1&limit=4"
            },
            "last": {
                "href": "\/api\/v1\/users\/?sorting%5Bcode%5D=desc&page=1&limit=4"
            }
        },
        "_embedded": {
            "items": [
                {
                    "id": 5,
                    "username": "sylius",
                    "email": "sylius@example.com",
                    "enabled": true
                },
                {
                    "id": 6,
                    "username": "api",
                    "email": "api@example.com",
                    "enabled": true
                },
                {
                    "id": 9,
                    "username": "Balrog",
                    "email": "teamEvil@middleearth.com",
                    "enabled": true
                }
            ]
        }
    }

Updating an Admin User
----------------------

To fully update an admin user you will need to call the ``/api/v1/users/{id}`` endpoint with the ``PUT`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    PUT /api/v1/users/{id}

+---------------+----------------+-------------------------------------------------------+
| Parameter     | Parameter type | Description                                           |
+===============+================+=======================================================+
| Authorization | header         | Token received during authentication                  |
+---------------+----------------+-------------------------------------------------------+
| id            | url attribute  | Id of the admin user                                  |
+---------------+----------------+-------------------------------------------------------+
| username      | request        | Admin user name                                       |
+---------------+----------------+-------------------------------------------------------+
| email         | request        | Admin user email                                      |
+---------------+----------------+-------------------------------------------------------+
| plainPassword | request        | Admin user password                                   |
+---------------+----------------+-------------------------------------------------------+
| localeCode    | request        | Code of the language, which is used by the admin user |
+---------------+----------------+-------------------------------------------------------+

Example
^^^^^^^

 To fully update the admin user with ``id = 9`` use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/users/9 \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PUT \
        --data '
            {
                "firstName": "Gollum",
                "lastName": "Gollum!",
                "username": "Smeagol",
                "email": "smeagol@middleearth.com",
                "plainPassword": "myPrecious",
                "localeCode": "en_US"
            }
    '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

If you try to perform a full admin user update without all the required fields specified, you will receive a ``400 Bad Request`` error.

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/users/9 \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PUT

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 400 Bad Request

.. code-block:: json

    {
        "code": 400,
        "message": "Validation Failed",
        "errors": {
            "children": {
                "username": {
                    "errors": [
                        "Please enter your name."
                    ]
                },
                "email": {
                    "errors": [
                        "Please enter your email."
                    ]
                },
                "plainPassword": {},
                "enabled": {},
                "firstName": {},
                "lastName": {},
                "localeCode": {
                    "errors": [
                        "Please choose a locale."
                    ]
                }
            }
        }
    }

To update an admin user partially you will need to call the ``/api/v1/users/{id}`` endpoint with the ``PATCH`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    PATCH /api/v1/users/{id}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| id            | url attribute  | Id of the admin user                 |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To partially update the admin user with ``id = 9`` use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/users/9 \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PATCH \
        --data '
            {
                "email": "smeagol@ring.com"
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

Deleting an Admin User
----------------------

To delete an admin user you will need to call the ``/api/v1/users/{id}`` endpoint with the ``DELETE`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    DELETE /api/v1/users/{id}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| id            | url attribute  | Id of the admin user                 |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To delete the admin user with ``id = 9`` use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/users/9 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X DELETE

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

.. warning::

    If you try to delete the admin user which is currently logged in, you will receive a ``422 Unprocessable Entity`` error.

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/users/6 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X DELETE

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 422 Unprocessable Entity

.. code-block:: json

    {
        "code": 422,
        "message": "Cannot remove currently logged in user."
    }
