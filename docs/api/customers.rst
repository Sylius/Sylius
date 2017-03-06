Customers API
=============

These endpoints will allow you to easily manage customers. Base URI is `/api/v1/customers`.
The Customer class is strongly coupled with the User class. Because of that we recommend these endpoints to manage all related to user actions.

When you get a collection of resources, "Default" serialization group will be used and the following fields will be exposed:

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
| firstName      | Customers first name                     |
+----------------+------------------------------------------+
| lastName       | Customers last name                      |
+----------------+------------------------------------------+

If you request for a more detailed data, you will receive an object with following fields:

+-------------------------+-------------------------------------------+
| Field                   | Description                               |
+=========================+===========================================+
| id                      | Id of customer                            |
+-------------------------+-------------------------------------------+
| user[id]                | *(optional)* Id of related user           |
+-------------------------+-------------------------------------------+
| user[username]          | *(optional)* Users username               |
+-------------------------+-------------------------------------------+
| user[usernameCanonical] | *(optional)* Canonicalized users username |
+-------------------------+-------------------------------------------+
| user[roles]             | *(optional)* Array of users roles         |
+-------------------------+-------------------------------------------+
| user[enabled]           | *(optional)* Flag set if user is enabled  |
+-------------------------+-------------------------------------------+
| email                   | Customers email                           |
+-------------------------+-------------------------------------------+
| emailCanonical          | Canonicalized customers email             |
+-------------------------+-------------------------------------------+
| firstName               | Customers first name                      |
+-------------------------+-------------------------------------------+
| lastName                | Customers last name                       |
+-------------------------+-------------------------------------------+
| gender                  | Customers gender                          |
+-------------------------+-------------------------------------------+
| birthday                | Customers birthday                        |
+-------------------------+-------------------------------------------+
| groups                  | Array of groups customer belongs to       |
+-------------------------+-------------------------------------------+

.. note::

    Read more about :doc:`Customers and Users </components/User/models>`.

Creating a Customer
-------------------

Definition
^^^^^^^^^^

.. code-block:: text

    POST /api/v1/customers/

+--------------------------+----------------+------------------------------------------------------------------------------------------------------+
| Parameter                | Parameter type | Description                                                                                          |
+==========================+================+======================================================================================================+
| Authorization            | header         | Token received during authentication                                                                 |
+--------------------------+----------------+------------------------------------------------------------------------------------------------------+
| email                    | request        | **(unique)** Customer's email                                                                        |
+--------------------------+----------------+------------------------------------------------------------------------------------------------------+
| firstName                | request        | Customer's first name                                                                                |
+--------------------------+----------------+------------------------------------------------------------------------------------------------------+
| lastName                 | request        | Customer's last name                                                                                 |
+--------------------------+----------------+------------------------------------------------------------------------------------------------------+
| groups                   | request        | *(optional)* Array of groups customer belongs to                                                     |
+--------------------------+----------------+------------------------------------------------------------------------------------------------------+
| gender                   | request        | Customer's gender                                                                                    |
+--------------------------+----------------+------------------------------------------------------------------------------------------------------+
| birthday                 | request        | *(optional)* Customer's birthday                                                                     |
+--------------------------+----------------+------------------------------------------------------------------------------------------------------+
| user[plainPassword]      | request        | *(optional)* Users plain password. Required if user account should be created together with customer |
+--------------------------+----------------+------------------------------------------------------------------------------------------------------+
| user[authorizationRoles] | request        | *(optional)* Array of users roles                                                                    |
+--------------------------+----------------+------------------------------------------------------------------------------------------------------+
| user[enabled]            | request        | *(optional)* Flag set if user is enabled                                                             |
+--------------------------+----------------+------------------------------------------------------------------------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/customers/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
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

Exemplary Response
^^^^^^^^^^^^^^^^^^

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
        "emailCanonical":"john.diggle@yahoo.com",
        "firstName":"John",
        "lastName":"Diggle",
        "gender":"m",
        "group":[

        ]
    }

If you try to create a customer without email or gender, you will receive a ``400 Bad Request`` error.

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/customers/ \
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
                "firstName": {},
                "lastName": {},
                "email": {
                    "errors": [
                        "Please enter your email."
                    ]
                },
                "birthday": {},
                "gender": {
                    "errors": [
                        "Please choose your gender."
                    ]
                },
                "phoneNumber": {},
                "subscribedToNewsletter": {},
                "group": {}
            }
        }
    }

Getting a Single Customer
-------------------------

You can request detailed customer information by executing the following request:

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/customers/{id}

+---------------+----------------+-------------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                       |
+===============+================+===================================================================+
| Authorization | header         | Token received during authentication                              |
+---------------+----------------+-------------------------------------------------------------------+
| id            | url attribute  | Id of the requested resource                                      |
+---------------+----------------+-------------------------------------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/customers/399 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "id":399,
        "user":{
            "id":398,
            "username":"cgulgowski@example.com",
            "usernameCanonical":"cgulgowski@example.com",
            "roles":[
                "ROLE_USER"
            ],
            "enabled":false
        },
        "email":"cgulgowski@example.com",
        "emailCanonical":"cgulgowski@example.com",
        "firstName":"Levi",
        "lastName":"Friesen",
        "gender":"u",
        "group":[

        ]
    }

Collection of Customers
-----------------------

You can retrieve the full customers list by making the following request:

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/customers/

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
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/customers/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

Exemplary Response
^^^^^^^^^^^^^^^^^^

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
                        "firstName":"Random",
                        "lastName":"Doe"
                 },
                 {
                        "id":406,
                        "email":"customer@email.com",
                        "firstName":"Alexanne",
                        "lastName":"Blick"
                 },
                 {
                        "id":405,
                        "user":{
                             "id":404,
                             "username":"gaylord.bins@example.com",
                             "enabled":true
                        },
                        "email":"gaylord.bins@example.com",
                        "firstName":"Dereck",
                        "lastName":"McDermott"
                 },
                 {
                        "id":404,
                        "user":{
                             "id":403,
                             "username":"lehner.gerhard@example.com",
                             "enabled":false
                        },
                        "email":"lehner.gerhard@example.com",
                        "firstName":"Benton",
                        "lastName":"Satterfield"
                 },
                 {
                        "id":403,
                        "user":{
                             "id":402,
                             "username":"raheem.ratke@example.com",
                             "enabled":false
                        },
                        "email":"raheem.ratke@example.com",
                        "firstName":"Rusty",
                        "lastName":"Jerde"
                 },
                 {
                        "id":402,
                        "user":{
                             "id":401,
                             "username":"litzy.morissette@example.com",
                             "enabled":false
                        },
                        "email":"litzy.morissette@example.com",
                        "firstName":"Omer",
                        "lastName":"Schaden"
                 },
                 {
                        "id":401,
                        "user":{
                             "id":400,
                             "username":"bbeer@example.com",
                             "enabled":true
                        },
                        "email":"bbeer@example.com",
                        "firstName":"Willard",
                        "lastName":"Hand"
                 },
                 {
                        "id":400,
                        "user":{
                             "id":399,
                             "username":"qtrantow@example.com",
                             "enabled":false
                        },
                        "email":"qtrantow@example.com",
                        "firstName":"Caterina",
                        "lastName":"Koelpin"
                 },
                 {
                        "id":399,
                        "user":{
                             "id":398,
                             "username":"cgulgowski@example.com",
                             "enabled":false
                        },
                        "email":"cgulgowski@example.com",
                        "firstName":"Levi",
                        "lastName":"Friesen"
                 }
            ]
        }
    }

Updating a Customer
-------------------

You can request full or partial update of resource. For full customer update, you should use PUT method.

Definition
^^^^^^^^^^

.. code-block:: text

    PUT /api/v1/customers/{id}

+--------------------------+----------------+------------------------------------------------------------------------------+
| Parameter                | Parameter type | Description                                                                  |
+==========================+================+==============================================================================+
| Authorization            | header         | Token received during authentication                                         |
+--------------------------+----------------+------------------------------------------------------------------------------+
| id                       | url attribute  | Id of the requested resource                                                 |
+--------------------------+----------------+------------------------------------------------------------------------------+
| email                    | request        | **(unique)** Customers email                                                 |
+--------------------------+----------------+------------------------------------------------------------------------------+
| firstName                | request        | Customers first name                                                         |
+--------------------------+----------------+------------------------------------------------------------------------------+
| lastName                 | request        | Customers last name                                                          |
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
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/customers/399 \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PUT \
        --data '
            {
                "firstName": "John",
                "lastName": "Diggle",
                "email": "john.diggle@example.com",
                "gender": "m"
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

If you try to perform full customer update without all required fields specified, you will receive a 400 error.

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/customers/399 \
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
                "firstName": {},
                "lastName": {},
                "email": {
                    "errors": [
                        "Please enter your email."
                    ]
                },
                "birthday": {},
                "gender": {
                    "errors": [
                        "Please choose your gender."
                    ]
                },
                "phoneNumber": {},
                "subscribedToNewsletter": {},
                "group": {}
            }
        }
    }

In order to perform a partial update, you should use a PATCH method.

Definition
^^^^^^^^^^

.. code-block:: text

    PATCH /api/v1/customers/{id}

+--------------------------+----------------+--------------------------------------------------+
| Parameter                | Parameter type | Description                                      |
+==========================+================+==================================================+
| Authorization            | header         | Token received during authentication             |
+--------------------------+----------------+--------------------------------------------------+
| id                       | url attribute  | Id of the requested resource                     |
+--------------------------+----------------+--------------------------------------------------+
| email                    | request        | *(optional)* **(unique)** Customers email        |
+--------------------------+----------------+--------------------------------------------------+
| firstName                | request        | *(optional)* Customers first name                |
+--------------------------+----------------+--------------------------------------------------+
| lastName                 | request        | *(optional)* Customers last name                 |
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
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/customers/399 \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PATCH \
        --data '{"firstName": "Joe"}'

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

Deleting a Customer
-------------------

Definition
^^^^^^^^^^

.. code-block:: text

    DELETE /api/v1/customers/{id}

+---------------+----------------+-------------------------------------------+
| Parameter     | Parameter type | Description                               |
+===============+================+===========================================+
| Authorization | header         | Token received during authentication      |
+---------------+----------------+-------------------------------------------+
| id            | url attribute  | Id of the requested resource              |
+---------------+----------------+-------------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/customers/399 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X DELETE

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

Collection of all customer orders
---------------------------------

To browse all orders for specific customer, you can do the following call:

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/customers/{id}/orders/

+---------------+----------------+-------------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                       |
+===============+================+===================================================================+
| Authorization | header         | Token received during authentication                              |
+---------------+----------------+-------------------------------------------------------------------+
| page          | query          | *(optional)* Number of the page, by default = 1                   |
+---------------+----------------+-------------------------------------------------------------------+
| paginate      | query          | *(optional)* Number of items to display per page, by default = 10 |
+---------------+----------------+-------------------------------------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/customers/7/orders/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 OK

    {
        "page":1,
        "limit":10,
        "pages":1,
        "total":1,
        "_links":{
            "self":{
                "href":"\/api\/v1\/customers\/2\/orders\/?page=1&limit=10"
            },
            "first":{
                "href":"\/api\/v1\/customers\/2\/orders\/?page=1&limit=10"
            },
            "last":{
                "href":"\/api\/v1\/customers\/2\/orders\/?page=1&limit=10"
            }
        },
        "_embedded":{
            "items":[
                {
                    "id":2,
                    "checkoutCompletedAt":"2017-02-23T14:53:11+0100",
                    "number":"000000002",
                    "items":[
                        {
                            "id":4,
                            "quantity":2,
                            "unitPrice":101,
                            "total":123,
                            "units":[
                                {
                                    "id":11,
                                    "adjustments":[
                                        {
                                            "id":12,
                                            "type":"order_promotion",
                                            "label":"Christmas",
                                            "amount":-40
                                        }
                                    ],
                                    "adjustmentsTotal":-40
                                },
                                {
                                    "id":12,
                                    "adjustments":[
                                        {
                                            "id":13,
                                            "type":"order_promotion",
                                            "label":"Christmas",
                                            "amount":-39
                                        }
                                    ],
                                    "adjustmentsTotal":-39
                                }
                            ],
                            "unitsTotal":123,
                            "adjustments":[

                            ],
                            "adjustmentsTotal":0,
                            "variant":{
                                "id":181,
                                "code":"MEDIUM_MUG_CUP",
                                "optionValues":[
                                    {
                                        "code":"t_shirt_color_red",
                                        "translations":{
                                            "en_US":{
                                                "locale":"en_US",
                                                "id":7,
                                                "value":"Red"
                                            }
                                        }
                                    },
                                    {
                                        "code":"t_shirt_size_s",
                                        "translations":{
                                            "en_US":{
                                                "locale":"en_US",
                                                "id":10,
                                                "value":"S"
                                            }
                                        }
                                    }
                                ],
                                "position":0,
                                "translations":{
                                    "en_US":{
                                        "locale":"en_US",
                                        "id":181,
                                        "name":"tempore"
                                    }
                                },
                                "onHold":0,
                                "onHand":6,
                                "tracked":false,
                                "channelPricings":{
                                    "US_WEB": {
                                        "channelCode": "US_WEB",
                                        "price":101
                                    }
                                },
                                "_links":{
                                    "self":{
                                        "href":"\/api\/v1\/products\/MUG\/variants\/MEDIUM_MUG_CUP"
                                    },
                                    "product":{
                                        "href":"\/api\/v1\/products\/MUG"
                                    }
                                }
                            },
                            "_links":{
                                "order":{
                                    "href":"\/api\/v1\/orders\/2"
                                },
                                "product":{
                                    "href":"\/api\/v1\/products\/MUG"
                                },
                                "variant":{
                                    "href":"\/api\/v1\/products\/MUG\/variants\/MEDIUM_MUG_CUP"
                                }
                            }
                        },
                        {
                            "id":5,
                            "quantity":4,
                            "unitPrice":840,
                            "total":2050,
                            "units":[
                                {
                                    "id":13,
                                    "adjustments":[
                                        {
                                            "id":14,
                                            "type":"order_promotion",
                                            "label":"Christmas",
                                            "amount":-328
                                        }
                                    ],
                                    "adjustmentsTotal":-328
                                },
                                {
                                    "id":14,
                                    "adjustments":[
                                        {
                                            "id":15,
                                            "type":"order_promotion",
                                            "label":"Christmas",
                                            "amount":-328
                                        }
                                    ],
                                    "adjustmentsTotal":-328
                                },
                                {
                                    "id":15,
                                    "adjustments":[
                                        {
                                            "id":16,
                                            "type":"order_promotion",
                                            "label":"Christmas",
                                            "amount":-327
                                        }
                                    ],
                                    "adjustmentsTotal":-327
                                },
                                {
                                    "id":16,
                                    "adjustments":[
                                        {
                                            "id":17,
                                            "type":"order_promotion",
                                            "label":"Christmas",
                                            "amount":-327
                                        }
                                    ],
                                    "adjustmentsTotal":-327
                                }
                            ],
                            "unitsTotal":2050,
                            "adjustments":[

                            ],
                            "adjustmentsTotal":0,
                            "variant":{
                                "id":97,
                                "code":"cd843634-6c85-3be0-9c84-7ce7786a394d-variant-0",
                                "optionValues":[

                                ],
                                "position":0,
                                "translations":{
                                    "en_US":{
                                        "locale":"en_US",
                                        "id":97,
                                        "name":"sequi"
                                    }
                                },
                                "onHold":0,
                                "onHand":5,
                                "tracked":false,
                                "channelPricings":{
                                    "US_WEB": {
                                        "channelCode": "US_WEB",
                                        "price":840
                                    }
                                },
                                "_links":{
                                    "self":{
                                        "href":"\/api\/v1\/products\/cd843634-6c85-3be0-9c84-7ce7786a394d\/variants\/cd843634-6c85-3be0-9c84-7ce7786a394d-variant-0"
                                    },
                                    "product":{
                                        "href":"\/api\/v1\/products\/cd843634-6c85-3be0-9c84-7ce7786a394d"
                                    }
                                }
                            },
                            "_links":{
                                "order":{
                                    "href":"\/api\/v1\/orders\/2"
                                },
                                "product":{
                                    "href":"\/api\/v1\/products\/cd843634-6c85-3be0-9c84-7ce7786a394d"
                                },
                                "variant":{
                                    "href":"\/api\/v1\/products\/cd843634-6c85-3be0-9c84-7ce7786a394d\/variants\/cd843634-6c85-3be0-9c84-7ce7786a394d-variant-0"
                                }
                            }
                        },
                        {
                            "id":6,
                            "quantity":4,
                            "unitPrice":660,
                            "total":1610,
                            "units":[
                                {
                                    "id":17,
                                    "adjustments":[
                                        {
                                            "id":18,
                                            "type":"order_promotion",
                                            "label":"Christmas",
                                            "amount":-258
                                        }
                                    ],
                                    "adjustmentsTotal":-258
                                },
                                {
                                    "id":18,
                                    "adjustments":[
                                        {
                                            "id":19,
                                            "type":"order_promotion",
                                            "label":"Christmas",
                                            "amount":-258
                                        }
                                    ],
                                    "adjustmentsTotal":-258
                                },
                                {
                                    "id":19,
                                    "adjustments":[
                                        {
                                            "id":20,
                                            "type":"order_promotion",
                                            "label":"Christmas",
                                            "amount":-257
                                        }
                                    ],
                                    "adjustmentsTotal":-257
                                },
                                {
                                    "id":20,
                                    "adjustments":[
                                        {
                                            "id":21,
                                            "type":"order_promotion",
                                            "label":"Christmas",
                                            "amount":-257
                                        }
                                    ],
                                    "adjustmentsTotal":-257
                                }
                            ],
                            "unitsTotal":1610,
                            "adjustments":[

                            ],
                            "adjustmentsTotal":0,
                            "variant":{
                                "id":45,
                                "code":"c38fef5d-ddf9-31e2-8e05-71618605f381-variant-2",
                                "optionValues":[
                                    {
                                        "code":"mug_type_monster",
                                        "translations":{
                                            "en_US":{
                                                "locale":"en_US",
                                                "id":3,
                                                "value":"Monster mug"
                                            }
                                        }
                                    }
                                ],
                                "position":2,
                                "translations":{
                                    "en_US":{
                                        "locale":"en_US",
                                        "id":45,
                                        "name":"quod"
                                    }
                                },
                                "onHold":0,
                                "onHand":7,
                                "tracked":false,
                                "channelPricings":{
                                    "US_WEB": {
                                        "channelCode":"US_WEB"
                                        "price":660
                                    }
                                },
                                "_links":{
                                    "self":{
                                        "href":"\/api\/v1\/products\/c38fef5d-ddf9-31e2-8e05-71618605f381\/variants\/c38fef5d-ddf9-31e2-8e05-71618605f381-variant-2"
                                    },
                                    "product":{
                                        "href":"\/api\/v1\/products\/c38fef5d-ddf9-31e2-8e05-71618605f381"
                                    }
                                }
                            },
                            "_links":{
                                "order":{
                                    "href":"\/api\/v1\/orders\/2"
                                },
                                "product":{
                                    "href":"\/api\/v1\/products\/c38fef5d-ddf9-31e2-8e05-71618605f381"
                                },
                                "variant":{
                                    "href":"\/api\/v1\/products\/c38fef5d-ddf9-31e2-8e05-71618605f381\/variants\/c38fef5d-ddf9-31e2-8e05-71618605f381-variant-2"
                                }
                            }
                        },
                        {
                            "id":7,
                            "quantity":1,
                            "unitPrice":430,
                            "total":262,
                            "units":[
                                {
                                    "id":21,
                                    "adjustments":[
                                        {
                                            "id":22,
                                            "type":"order_promotion",
                                            "label":"Christmas",
                                            "amount":-168
                                        }
                                    ],
                                    "adjustmentsTotal":-168
                                }
                            ],
                            "unitsTotal":262,
                            "adjustments":[

                            ],
                            "adjustmentsTotal":0,
                            "variant":{
                                "id":20,
                                "code":"4d4ba2e2-7138-3256-a88f-0caa5dc3bb81-variant-1",
                                "optionValues":[
                                    {
                                        "code":"mug_type_double",
                                        "translations":{
                                            "en_US":{
                                                "locale":"en_US",
                                                "id":2,
                                                "value":"Double mug"
                                            }
                                        }
                                    }
                                ],
                                "position":1,
                                "translations":{
                                    "en_US":{
                                        "locale":"en_US",
                                        "id":20,
                                        "name":"nisi"
                                    }
                                },
                                "onHold":0,
                                "onHand":2,
                                "tracked":false,
                                "channelPricings":{
                                    "US_WEB": {
                                        "channelCode":"US_WEB",
                                        "price":430
                                    }
                                },
                                "_links":{
                                    "self":{
                                        "href":"\/api\/v1\/products\/4d4ba2e2-7138-3256-a88f-0caa5dc3bb81\/variants\/4d4ba2e2-7138-3256-a88f-0caa5dc3bb81-variant-1"
                                    },
                                    "product":{
                                        "href":"\/api\/v1\/products\/4d4ba2e2-7138-3256-a88f-0caa5dc3bb81"
                                    }
                                }
                            },
                            "_links":{
                                "order":{
                                    "href":"\/api\/v1\/orders\/2"
                                },
                                "product":{
                                    "href":"\/api\/v1\/products\/4d4ba2e2-7138-3256-a88f-0caa5dc3bb81"
                                },
                                "variant":{
                                    "href":"\/api\/v1\/products\/4d4ba2e2-7138-3256-a88f-0caa5dc3bb81\/variants\/4d4ba2e2-7138-3256-a88f-0caa5dc3bb81-variant-1"
                                }
                            }
                        },
                        {
                            "id":8,
                            "quantity":4,
                            "unitPrice":665,
                            "total":1623,
                            "units":[
                                {
                                    "id":22,
                                    "adjustments":[
                                        {
                                            "id":23,
                                            "type":"order_promotion",
                                            "label":"Christmas",
                                            "amount":-260
                                        }
                                    ],
                                    "adjustmentsTotal":-260
                                },
                                {
                                    "id":23,
                                    "adjustments":[
                                        {
                                            "id":24,
                                            "type":"order_promotion",
                                            "label":"Christmas",
                                            "amount":-259
                                        }
                                    ],
                                    "adjustmentsTotal":-259
                                },
                                {
                                    "id":24,
                                    "adjustments":[
                                        {
                                            "id":25,
                                            "type":"order_promotion",
                                            "label":"Christmas",
                                            "amount":-259
                                        }
                                    ],
                                    "adjustmentsTotal":-259
                                },
                                {
                                    "id":25,
                                    "adjustments":[
                                        {
                                            "id":26,
                                            "type":"order_promotion",
                                            "label":"Christmas",
                                            "amount":-259
                                        }
                                    ],
                                    "adjustmentsTotal":-259
                                }
                            ],
                            "unitsTotal":1623,
                            "adjustments":[

                            ],
                            "adjustmentsTotal":0,
                            "variant":{
                                "id":91,
                                "code":"6864f798-e0e5-339d-91c9-e6036befa414-variant-0",
                                "optionValues":[

                                ],
                                "position":0,
                                "translations":{
                                    "en_US":{
                                        "locale":"en_US",
                                        "id":91,
                                        "name":"maiores"
                                    }
                                },
                                "onHold":0,
                                "onHand":7,
                                "tracked":false,
                                "channelPricings":{
                                    "US_WEB": {
                                        "channelCode":"US_WEB",
                                        "price":665
                                    }
                                },
                                "_links":{
                                    "self":{
                                        "href":"\/api\/v1\/products\/6864f798-e0e5-339d-91c9-e6036befa414\/variants\/6864f798-e0e5-339d-91c9-e6036befa414-variant-0"
                                    },
                                    "product":{
                                        "href":"\/api\/v1\/products\/6864f798-e0e5-339d-91c9-e6036befa414"
                                    }
                                }
                            },
                            "_links":{
                                "order":{
                                    "href":"\/api\/v1\/orders\/2"
                                },
                                "product":{
                                    "href":"\/api\/v1\/products\/6864f798-e0e5-339d-91c9-e6036befa414"
                                },
                                "variant":{
                                    "href":"\/api\/v1\/products\/6864f798-e0e5-339d-91c9-e6036befa414\/variants\/6864f798-e0e5-339d-91c9-e6036befa414-variant-0"
                                }
                            }
                        }
                    ],
                    "itemsTotal":5668,
                    "adjustments":[
                        {
                            "id":27,
                            "type":"shipping",
                            "label":"FedEx",
                            "amount":1530
                        }
                    ],
                    "adjustmentsTotal":1530,
                    "total":7198,
                    "state":"new",
                    "customer":{
                        "id":2,
                        "email":"metz.ted@beer.com",
                        "emailCanonical":"metz.ted@beer.com",
                        "firstName":"Dangelo",
                        "lastName":"Graham",
                        "gender":"u",
                        "user":{
                            "id":2,
                            "username":"metz.ted@beer.com",
                            "usernameCanonical":"metz.ted@beer.com",
                            "roles":[
                                "ROLE_USER"
                            ],
                            "enabled":true
                        },
                        "_links":{
                            "self":{
                                "href":"\/api\/v1\/customers\/2"
                            }
                        }
                    },
                    "channel":{
                        "id":1,
                        "code":"US_WEB",
                        "name":"US Web Store",
                        "hostname":"localhost",
                        "color":"Plum",
                        "createdAt":"2017-02-23T14:53:04+0100",
                        "updatedAt":"2017-02-23T14:53:04+0100",
                        "enabled":true,
                        "taxCalculationStrategy":"order_items_based",
                        "_links":{
                            "self":{
                                "href":"\/api\/v1\/channels\/US_WEB"
                            }
                        }
                    },
                    "shippingAddress":{
                        "id":4,
                        "firstName":"Kay",
                        "lastName":"Abbott",
                        "countryCode":"US",
                        "street":"Walsh Ford",
                        "city":"New Devante",
                        "postcode":"39325"
                    },
                    "billingAddress":{
                        "id":5,
                        "firstName":"Kay",
                        "lastName":"Abbott",
                        "countryCode":"US",
                        "street":"Walsh Ford",
                        "city":"New Devante",
                        "postcode":"39325"
                    },
                    "payments":[
                        {
                            "id":2,
                            "method":{
                                "id":1,
                                "code":"cash_on_delivery",
                                "channels":[
                                    {
                                        "id":1,
                                        "code":"US_WEB",
                                        "name":"US Web Store",
                                        "hostname":"localhost",
                                        "color":"Plum",
                                        "createdAt":"2017-02-23T14:53:04+0100",
                                        "updatedAt":"2017-02-23T14:53:04+0100",
                                        "enabled":true,
                                        "taxCalculationStrategy":"order_items_based",
                                        "_links":{
                                            "self":{
                                                "href":"\/api\/v1\/channels\/US_WEB"
                                            }
                                        }
                                    }
                                ],
                                "_links":{
                                    "self":{
                                        "href":"\/api\/v1\/payment-methods\/cash_on_delivery"
                                    }
                                }
                            },
                            "amount":7198,
                            "state":"new",
                            "_links":{
                                "self":{
                                    "href":"\/api\/v1\/payments\/2"
                                },
                                "payment-method":{
                                    "href":"\/api\/v1\/payment-methods\/cash_on_delivery"
                                },
                                "order":{
                                    "href":"\/api\/v1\/orders\/2"
                                }
                            }
                        }
                    ],
                    "shipments":[
                        {
                            "id":2,
                            "state":"ready",
                            "method":{
                                "id":3,
                                "code":"fedex",
                                "enabled":true,
                                "_links":{
                                    "self":{
                                        "href":"\/api\/v1\/shipping-methods\/fedex"
                                    },
                                    "zone":{
                                        "href":"\/api\/v1\/zones\/US"
                                    }
                                }
                            },
                            "_links":{
                                "self":{
                                    "href":"\/api\/v1\/shipments\/2"
                                },
                                "method":{
                                    "href":"\/api\/v1\/shipping-methods\/fedex"
                                },
                                "order":{
                                    "href":"\/api\/v1\/orders\/2"
                                }
                            }
                        }
                    ],
                    "currencyCode":"USD",
                    "localeCode":"en_US",
                    "checkoutState":"completed"
                }
            ]
        }
    }
