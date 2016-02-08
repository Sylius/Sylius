Customers API
=============

These endpoints will allow you to easily manage customers. Base URI is `/api/customers`.
Customer class is strongly coupled with a user class. Because of that we recommend these endpoints to manage all related to user actions

When you get a collection of resources, "Default" serialization group will be used and following fields will be exposed:

+----------------+------------------------------------------+
| Field          | Description                              |
+================+==========================================+
| id             | Id of customer                           |
+----------------+------------------------------------------+
| user[id]       | *(optional)* Id of related user          |
+----------------+------------------------------------------+
| user[username] | *(optional)* Users username              |
+----------------+------------------------------------------+
| user[enabled]  | *(optional)* Flag set if user is enabled |
+----------------+------------------------------------------+
| email          | Customers email                          |
+----------------+------------------------------------------+
| first_name     | Customers first name                     |
+----------------+------------------------------------------+
| last_name      | Customers last name                      |
+----------------+------------------------------------------+

If you request for a more detailed data, you will receive an object with following fields:

+--------------------------+-------------------------------------------+
| Field                    | Description                               |
+==========================+===========================================+
| id                       | Id of customer                            |
+--------------------------+-------------------------------------------+
| user[id]                 | *(optional)* Id of related user           |
+--------------------------+-------------------------------------------+
| user[username]           | *(optional)* Users username               |
+--------------------------+-------------------------------------------+
| user[username_canonical] | *(optional)* Canonicalized users username |
+--------------------------+-------------------------------------------+
| user[roles]              | *(optional)* Array of users roles         |
+--------------------------+-------------------------------------------+
| user[enabled]            | *(optional)* Flag set if user is enabled  |
+--------------------------+-------------------------------------------+
| email                    | Customers email                           |
+--------------------------+-------------------------------------------+
| email_canonical          | Canonicalized customers email             |
+--------------------------+-------------------------------------------+
| first_name               | Customers first name                      |
+--------------------------+-------------------------------------------+
| last_name                | Customers last name                       |
+--------------------------+-------------------------------------------+
| gender                   | Customers gender                          |
+--------------------------+-------------------------------------------+
| birthday                 | Customers birthday                        |
+--------------------------+-------------------------------------------+
| groups                   | Array of groups customer belongs to       |
+--------------------------+-------------------------------------------+

.. note::

    Read more about `Customer`__ and `User`__

__ http://docs.sylius.org/en/latest/components/User/models.html#customer
__ http://docs.sylius.org/en/latest/components/User/models.html#user

Collection of Customers
-----------------------

You can retrieve the full customers list by making the following request:

Definition
..........

.. code-block:: text

    GET /api/customers/

+---------------+----------------+-------------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                       |
+===============+================+===================================================================+
| Authorization | header         | Token received during authentication                              |
+---------------+----------------+-------------------------------------------------------------------+
| page          | query          | *(optional)* Number of the page, by default = 1                   |
+---------------+----------------+-------------------------------------------------------------------+
| limit         | query          | *(optional)* Number of items to display per page, by default = 10 |
+---------------+----------------+-------------------------------------------------------------------+


Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/customers/
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng"
        -H “Accept: application/json”

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "page":1,
        "limit":10,
        "pages":21,
        "total":205,
        "_links":{
            "self":{
                 "href":"\/api\/customers\/?page=1&limit=10"
            },
            "first":{
                 "href":"\/api\/customers\/?page=1&limit=10"
            },
            "last":{
                 "href":"\/api\/customers\/?page=21&limit=10"
            },
            "next":{
                 "href":"\/api\/customers\/?page=2&limit=10"
            }
        },
        "_embedded":{
            "items":[
                 {
                        "id":407,
                        "email":"random@gmail.com",
                        "first_name":"Random",
                        "last_name":"Doe"
                 },
                 {
                        "id":406,
                        "email":"customer@email.com",
                        "first_name":"Alexanne",
                        "last_name":"Blick"
                 },
                 {
                        "id":405,
                        "user":{
                             "id":404,
                             "username":"gaylord.bins@example.com",
                             "enabled":true
                        },
                        "email":"gaylord.bins@example.com",
                        "first_name":"Dereck",
                        "last_name":"McDermott"
                 },
                 {
                        "id":404,
                        "user":{
                             "id":403,
                             "username":"lehner.gerhard@example.com",
                             "enabled":false
                        },
                        "email":"lehner.gerhard@example.com",
                        "first_name":"Benton",
                        "last_name":"Satterfield"
                 },
                 {
                        "id":403,
                        "user":{
                             "id":402,
                             "username":"raheem.ratke@example.com",
                             "enabled":false
                        },
                        "email":"raheem.ratke@example.com",
                        "first_name":"Rusty",
                        "last_name":"Jerde"
                 },
                 {
                        "id":402,
                        "user":{
                             "id":401,
                             "username":"litzy.morissette@example.com",
                             "enabled":false
                        },
                        "email":"litzy.morissette@example.com",
                        "first_name":"Omer",
                        "last_name":"Schaden"
                 },
                 {
                        "id":401,
                        "user":{
                             "id":400,
                             "username":"bbeer@example.com",
                             "enabled":true
                        },
                        "email":"bbeer@example.com",
                        "first_name":"Willard",
                        "last_name":"Hand"
                 },
                 {
                        "id":400,
                        "user":{
                             "id":399,
                             "username":"qtrantow@example.com",
                             "enabled":false
                        },
                        "email":"qtrantow@example.com",
                        "first_name":"Caterina",
                        "last_name":"Koelpin"
                 },
                 {
                        "id":399,
                        "user":{
                             "id":398,
                             "username":"cgulgowski@example.com",
                             "enabled":false
                        },
                        "email":"cgulgowski@example.com",
                        "first_name":"Levi",
                        "last_name":"Friesen"
                 }
            ]
        }
    }

Getting a Single Customer
-------------------------

You can request detailed customer information by executing the following request:

Definition
..........

.. code-block:: text

    GET /api/customers/{id}

+---------------+----------------+-------------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                       |
+===============+================+===================================================================+
| Authorization | header         | Token received during authentication                              |
+---------------+----------------+-------------------------------------------------------------------+
| id            | url attribute  | Id of requested resource                                          |
+---------------+----------------+-------------------------------------------------------------------+
| page          | query          | *(optional)* Number of the page, by default = 1                   |
+---------------+----------------+-------------------------------------------------------------------+
| limit         | query          | *(optional)* Number of items to display per page, by default = 10 |
+---------------+----------------+-------------------------------------------------------------------+

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/customers/399
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng"
        -H “Accept: application/json”

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "id":399,
        "user":{
            "id":398,
            "username":"cgulgowski@example.com",
            "username_canonical":"cgulgowski@example.com",
            "roles":[
                "ROLE_USER"
            ],
            "enabled":false
        },
        "email":"cgulgowski@example.com",
        "email_canonical":"cgulgowski@example.com",
        "first_name":"Levi",
        "last_name":"Friesen",
        "gender":"u",
        "groups":[

        ]
    }

Creating Customer
-----------------

Definition
..........

.. code-block:: text

    POST /api/customers/

+--------------------------+----------------+------------------------------------------------------------------------------------------------------+
| Parameter                | Parameter type | Description                                                                                          |
+==========================+================+======================================================================================================+
| Authorization            | header         | Token received during authentication                                                                 |
+--------------------------+----------------+------------------------------------------------------------------------------------------------------+
| email                    | request        | **(unique)** Customers email                                                                         |
+--------------------------+----------------+------------------------------------------------------------------------------------------------------+
| first_name               | request        | Customers first name                                                                                 |
+--------------------------+----------------+------------------------------------------------------------------------------------------------------+
| last_name                | request        | Customers last name                                                                                  |
+--------------------------+----------------+------------------------------------------------------------------------------------------------------+
| groups                   | request        | *(optional)* Array of groups customer belongs to                                                     |
+--------------------------+----------------+------------------------------------------------------------------------------------------------------+
| gender                   | request        | Customers gender                                                                                     |
+--------------------------+----------------+------------------------------------------------------------------------------------------------------+
| birthday                 | request        | *(optional)* Customers birthday                                                                      |
+--------------------------+----------------+------------------------------------------------------------------------------------------------------+
| user[plainPassword]      | request        | *(optional)* Users plain password. Required if user account should be created together with customer |
+--------------------------+----------------+------------------------------------------------------------------------------------------------------+
| user[authorizationRoles] | request        | *(optional)* Array of users roles                                                                    |
+--------------------------+----------------+------------------------------------------------------------------------------------------------------+
| user[enabled]            | request        | *(optional)* Flag set if user is enabled                                                             |
+--------------------------+----------------+------------------------------------------------------------------------------------------------------+

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/customers/
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng"
        -H "Content-Type: application/json"
        -X POST
        --data '
            {
                "firstName": "John",
                "lastName": "Diggle",
                "email": "john.diggle@yahoo.com",
                "gender": "m",
                "user": {
                    "plainPassword" : "testPassword"
                }
            }
        '

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 201 Created

.. code-block:: json

    {
        "id":409,
        "user":{
            "id":405,
            "username":"john.diggle@yahoo.com",
            "roles":[
                "ROLE_USER"
            ],
            "enabled":false
        },
        "email":"john.diggle@yahoo.com",
        "email_canonical":"john.diggle@yahoo.com",
        "first_name":"John",
        "last_name":"Diggle",
        "gender":"m",
        "groups":[

        ]
    }

If you try to create a customer without email, first name, last name or gender, you will receive a 400 error.

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/customers/
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng"
        -H "Accept: application/json"
        -X POST

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 400 Bad Request

.. code-block:: json

    {
        "code":400,
        "message":"Validation Failed",
        "errors":{
            "children":{
                "firstName":{
                    "errors":[
                        "Please enter your first name."
                    ]
                },
                "lastName":{
                    "errors":[
                        "Please enter your last name."
                    ]
                },
                "email":{
                    "errors":[
                        "Please enter your email."
                    ]
                },
                "birthday":{

                },
                "gender":{
                    "errors":[
                        "Please choose your gender."
                    ]
                },
                "groups":{

                }
            }
        }
    }

Updating Customer
-----------------

You can request full or partial update of resource. For full customer update, you should use PUT method.

Definition
..........

.. code-block:: text

    PUT /api/customers/{id}

+--------------------------+----------------+------------------------------------------------------------------------------+
| Parameter                | Parameter type | Description                                                                  |
+==========================+================+==============================================================================+
| Authorization            | header         | Token received during authentication                                         |
+--------------------------+----------------+------------------------------------------------------------------------------+
| id                       | url attribute  | Id of requested resource                                                     |
+--------------------------+----------------+------------------------------------------------------------------------------+
| email                    | request        | **(unique)** Customers email                                                 |
+--------------------------+----------------+------------------------------------------------------------------------------+
| first_name               | request        | Customers first name                                                         |
+--------------------------+----------------+------------------------------------------------------------------------------+
| last_name                | request        | Customers last name                                                          |
+--------------------------+----------------+------------------------------------------------------------------------------+
| groups                   | request        | *(optional)* Array of groups customer belongs to                             |
+--------------------------+----------------+------------------------------------------------------------------------------+
| gender                   | request        | Customers gender                                                             |
+--------------------------+----------------+------------------------------------------------------------------------------+
| birthday                 | request        | *(optional)* Customers birthday                                              |
+--------------------------+----------------+------------------------------------------------------------------------------+
| user[plainPassword]      | request        | *(optional)* Users plain password. Required if any of user fields is defined |
+--------------------------+----------------+------------------------------------------------------------------------------+
| user[authorizationRoles] | request        | *(optional)* Array of users roles.                                           |
+--------------------------+----------------+------------------------------------------------------------------------------+
| user[enabled]            | request        | *(optional)* Flag set if user is enabled.                                    |
+--------------------------+----------------+------------------------------------------------------------------------------+

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/customers/399
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng"
        -H "Content-Type: application/json"
        -X PUT
        --data '
            {
                "firstName": "John",
                "lastName": "Diggle",
                "email": "john.diggle@example.com",
                "gender": "m"
            }
        '

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 204 No Content

If you try to perform full customer update without all required fields specified, you will receive a 400 error.

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/customers/399
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng"
        -H “Accept: application/json”
        -X PUT

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 400 Bad Request

.. code-block:: json

    {
        "code":400,
        "message":"Validation Failed",
        "errors":{
            "children":{
                "firstName":{
                    "errors":[
                        "Please enter your first name."
                    ]
                },
                "lastName":{
                    "errors":[
                        "Please enter your last name."
                    ]
                },
                "email":{
                    "errors":[
                        "Please enter your email."
                    ]
                },
                "birthday":{

                },
                "gender":{
                    "errors":[
                        "Please choose your gender."
                    ]
                },
                "groups":{

                }
            }
        }
    }

In order to perform a partial update, you should use a PATCH method.

Definition
..........

.. code-block:: text

    PATCH /api/customers/{id}

+--------------------------+----------------+--------------------------------------------------+
| Parameter                | Parameter type | Description                                      |
+==========================+================+==================================================+
| Authorization            | header         | Token received during authentication             |
+--------------------------+----------------+--------------------------------------------------+
| id                       | url attribute  | Id of requested resource                         |
+--------------------------+----------------+--------------------------------------------------+
| email                    | request        | *(optional)* **(unique)** Customers email        |
+--------------------------+----------------+--------------------------------------------------+
| first_name               | request        | *(optional)* Customers first name                |
+--------------------------+----------------+--------------------------------------------------+
| last_name                | request        | *(optional)* Customers last name                 |
+--------------------------+----------------+--------------------------------------------------+
| groups                   | request        | *(optional)* Array of groups customer belongs to |
+--------------------------+----------------+--------------------------------------------------+
| gender                   | request        | *(optional)* Customers gender                    |
+--------------------------+----------------+--------------------------------------------------+
| birthday                 | request        | *(optional)* Customers birthday                  |
+--------------------------+----------------+--------------------------------------------------+
| user[plainPassword]      | request        | *(optional)* Users plain password.               |
+--------------------------+----------------+--------------------------------------------------+
| user[authorizationRoles] | request        | *(optional)* Array of users roles.               |
+--------------------------+----------------+--------------------------------------------------+
| user[enabled]            | request        | *(optional)* Flag set if user is enabled.        |
+--------------------------+----------------+--------------------------------------------------+

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/customers/399
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng"
        -H "Content-Type: application/json"
        -X PATCH
        --data '{"first_name": "Joe"}'

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 204 No Content

Deleting Customer
-----------------

Definition
..........

.. code-block:: text

    DELETE /api/customers/{id}

+---------------+----------------+-------------------------------------------+
| Parameter     | Parameter type | Description                               |
+===============+================+===========================================+
| Authorization | header         | Token received during authentication      |
+---------------+----------------+-------------------------------------------+
| id            | url attribute  | Id of requested resource                  |
+---------------+----------------+-------------------------------------------+

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/customers/399
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng"
        -H “Accept: application/json”
        -X DELETE

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 204 No Content
