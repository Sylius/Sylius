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
                  "enabled": true,
                  "id": "@integer@",
                  "locked": false,
                  "roles": "@array@",
                  "username": "@string@",
                  "username_canonical": "@string@",
                  "customer": "@array@",
                  "last_login": "@string@.isDateTime()"
                }
            ]
        }
    }

Getting a single user
---------------------

You can view a single user by executing the following request:

.. code-block:: text

    GET /api/users/481

Response
~~~~~~~~

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
      "enabled": true,
      "id": "@integer@",
      "locked": false,
      "roles": "@array@",
      "username": "@string@",
      "username_canonical": "@string@",
      "customer": "@array@",
      "last_login": "@string@.isDateTime()"
    }


Deleting a user
---------------

You can delete (soft) a user from the system by making the following DELETE call:

.. code-block:: text

    DELETE /api/users/24

Response
~~~~~~~~

.. code-block:: text

    STATUS: 204 NO CONTENT
