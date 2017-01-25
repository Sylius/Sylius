Carts API
=========

These endpoints will allow you to easily manage cart and cart items. Base URI is `/api/v1/carts/`.

.. note::

    If you still don't know the difference between Cart and Order concepts in Sylius, please read :doc:`this article </book/orders/orders>` carefully.

Cart structure
--------------

Cart API response structure
^^^^^^^^^^^^^^^^^^^^^^^^^^^

If you request a cart, you will receive an object with the following fields:

+-------------------+-------------------------------------------------------------------+
| Field             | Description                                                       |
+===================+===================================================================+
| id                | Id of cart                                                        |
+-------------------+-------------------------------------------------------------------+
| items             | List of items related to cart                                     |
+-------------------+-------------------------------------------------------------------+
| items_total       | Sum of all items prices                                           |
+-------------------+-------------------------------------------------------------------+
| adjustments       | List of adjustments related to cart                               |
+-------------------+-------------------------------------------------------------------+
| adjustments_total | Sum of all order adjustments                                      |
+-------------------+-------------------------------------------------------------------+
| total             | Sum of items total and adjustments total                          |
+-------------------+-------------------------------------------------------------------+
| customer          | :doc:`Customer detailed serialization </api/customers>` for order |
+-------------------+-------------------------------------------------------------------+
| channel           | :doc:`Default channel serialization </api/channels>`              |
+-------------------+-------------------------------------------------------------------+
| currency_code     | Currency of the cart                                              |
+-------------------+-------------------------------------------------------------------+
| checkout_state    | State of checkout process                                         |
+-------------------+-------------------------------------------------------------------+

CartItem API response structure
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Each cart item will be build as follows:

+-------------------+------------------------------------------+
| Field             | Description                              |
+===================+==========================================+
| id                | Id of cart item                          |
+-------------------+------------------------------------------+
| quantity          | Quantity of item units                   |
+-------------------+------------------------------------------+
| unit_price        | Price of each item unit                  |
+-------------------+------------------------------------------+
| total             | Sum of units total and adjustments total |
+-------------------+------------------------------------------+
| units             | List of units related to cart            |
+-------------------+------------------------------------------+
| units_total       | Sum of all units prices                  |
+-------------------+------------------------------------------+
| adjustments       | List of adjustments related to item      |
+-------------------+------------------------------------------+
| adjustments_total | Sum of all item adjustments              |
+-------------------+------------------------------------------+
| variant           | Default variant serialization            |
+-------------------+------------------------------------------+

CartItemUnit API response structure
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Each cart item unit will be build as follows:

+-------------------+-------------------------------------+
| Field             | Description                         |
+===================+=====================================+
| id                | Id of cart item unit                |
+-------------------+-------------------------------------+
| adjustments       | List of adjustments related to unit |
+-------------------+-------------------------------------+
| adjustments_total | Sum of all units adjustments        |
+-------------------+-------------------------------------+

Adjustment API response structure
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

And each adjustment will be build as follows:

+--------+---------------------------------------------------------+
| Field  | Description                                             |
+========+=========================================================+
| id     | Id of cart item unit                                    |
+--------+---------------------------------------------------------+
| type   | Type of an adjustment (E.g. *order_promotion* or *tax*) |
+--------+---------------------------------------------------------+
| label  | Label of adjustment                                     |
+--------+---------------------------------------------------------+
| amount | Amount of adjustment                                    |
+--------+---------------------------------------------------------+

.. note::

    If it is still confusing to you, learn more about :doc:`Carts (Orders) </components/Order/models>` and :doc:`Adjustments </book/orders/adjustments>`.

Creating a Cart
---------------

To create a new cart you will need to call the ``/api/v1/carts/`` endpoint with ``POST`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    POST /api/v1/carts/

+--------------------+----------------+----------------------------------------------------+
| Parameter          | Parameter type | Description                                        |
+====================+================+====================================================+
| Authorization      | header         | Token received during authentication               |
+--------------------+----------------+----------------------------------------------------+
| customer           | request        | Email of related customer                          |
+--------------------+----------------+----------------------------------------------------+
| channel            | request        | Code of related channel                            |
+--------------------+----------------+----------------------------------------------------+
| locale_code        | request        | Code of locale in which the cart should be created |
+--------------------+----------------+----------------------------------------------------+
| criteria[customer] | query          | Code of locale in which the cart should be created |
+--------------------+----------------+----------------------------------------------------+

Example
^^^^^^^

To create a new cart for the ``shop@example.com`` user in the ``US_WEB`` channel in the ``en_US`` locale use the below method.

.. warning::

    Remember, that it doesn't replicate the environment of shop usage. It is more like an admin part of cart creation, which will allow you to manage
    cart from admin perspective. ShopAPI is still an experimental concept.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/carts/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
                "customer": "shop@example.com",
                "channel": "US_WEB",
                "locale_code": "en_US"
            }
        '

Example Response
^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 201 Created

.. code-block:: json

    {
        "id":21,
        "items":[

        ],
        "items_total":0,
        "adjustments":[

        ],
        "adjustments_total":0,
        "total":0,
        "customer":{
            "id":1,
            "email":"shop@example.com",
            "first_name":"John",
            "last_name":"Doe",
            "user":{
                "id":1,
                "username":"shop@example.com",
                "username_canonical":"shop@example.com"
            }
        },
        "channel":{
            "code":"US_WEB",
            "_links":{
                "self":{
                    "href":"\/api\/v1\/channels\/1"
                }
            }
        },
        "currency_code":"USD",
        "checkout_state":"cart"
    }

.. note::

    A currency code will be added automatically based on a channel settings. :doc:`Read more about channels </book/configuration/channels>`

.. warning::

    If you try to create a resource without name or code, you will receive a 400 Bad Request error.

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/carts/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X POST

Example Response
^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 400 Bad Request

.. code-block:: json

    {
        "code":400,
        "message":"Validation Failed",
        "errors":{
            "children":{
                "customer":{
                    "errors":[
                        "This value should not be blank."
                    ]
                },
                "localeCode":{
                    "errors":[
                        "This value should not be blank."
                    ]
                },
                "channel":{
                    "errors":[
                        "This value should not be blank."
                    ]
                }
            }
        }
    }

Collection of Carts
-------------------

To retrieve the paginated list of carts you will need to call the ``/api/v1/carts/`` endpoint with ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/carts/

+---------------+----------------+------------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                      |
+===============+================+==================================================================+
| Authorization | header         | Token received during authentication                             |
+---------------+----------------+------------------------------------------------------------------+
| page          | query          | *(optional)* Number of the page, by default = 1                  |
+---------------+----------------+------------------------------------------------------------------+
| limit         | query          | *(optional)* Number of carts displayed per page, by default = 10 |
+---------------+----------------+------------------------------------------------------------------+

Example
^^^^^^^

To see the first page of all carts use the method below.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/carts/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

Example Response
^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "page":1,
        "limit":10,
        "pages":1,
        "total":1,
        "_links":{
            "self":{
                "href":"\/api\/v1\/carts\/?page=1&limit=10"
            },
            "first":{
                "href":"\/api\/v1\/carts\/?page=1&limit=10"
            },
            "last":{
                "href":"\/api\/v1\/carts\/?page=1&limit=10"
            }
        },
        "_embedded":{
            "items":[
                {
                    "id":21,
                    "items":[

                    ],
                    "items_total":0,
                    "adjustments":[

                    ],
                    "adjustments_total":0,
                    "total":0,
                    "customer":{
                        "id":1,
                        "email":"shop@example.com",
                        "first_name":"John",
                        "last_name":"Doe",
                        "user":{
                            "id":1,
                            "username":"shop@example.com",
                            "enabled":true
                        }
                    },
                    "channel":{
                        "code":"US_WEB",
                        "_links":{
                            "self":{
                                "href":"\/api\/v1\/channels\/1"
                            }
                        }
                    },
                    "currency_code":"USD",
                    "checkout_state":"cart"
                }
            ]
        }
    }

Getting a Single Cart
---------------------

To retrieve the details of the cart you will need to call the ``/api/v1/carts/{id}`` endpoint with ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/carts/{id}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| id            | url attribute  | Id of requested resource             |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To see the details of the cart with id 21 use the method below.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/carts/21 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

.. note::

    The value *21* was taken from create response. Your value can be different. Check list of all carts if you are not sure which id should be used.

Example Response
^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "id":21,
        "items":[

        ],
        "items_total":0,
        "adjustments":[

        ],
        "adjustments_total":0,
        "total":0,
        "customer":{
            "id":1,
            "email":"shop@example.com",
            "first_name":"John",
            "last_name":"Doe",
            "user":{
                "id":1,
                "username":"shop@example.com",
                "username_canonical":"shop@example.com"
            }
        },
        "channel":{
            "code":"US_WEB",
            "_links":{
                "self":{
                    "href":"\/api\/v1\/channels\/1"
                }
            }
        },
        "currency_code":"USD",
        "checkout_state":"cart"
    }

Deleting a Cart
---------------

To delete a cart you will need to call the ``/api/v1/carts/{id}`` endpoint with ``DELETE`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    DELETE /api/v1/carts/{id}

+---------------+----------------+-------------------------------------------+
| Parameter     | Parameter type | Description                               |
+===============+================+===========================================+
| Authorization | header         | Token received during authentication      |
+---------------+----------------+-------------------------------------------+
| id            | url attribute  | Id of requested resource                  |
+---------------+----------------+-------------------------------------------+

Example
^^^^^^^

To delete the cart with id 21 use the method below.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/carts/21 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X DELETE

.. note::

    Remember the *21* value from the previous example. Here we are deleting a previously fetch cart, so it is the same id.

Example Response
^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

Creating a Cart Item
--------------------

To add a new cart item to the existing cart you will need to call the ``/api/v1/carts/{cartId}/items/`` endpoint with ``POST`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    POST /api/v1/carts/{cartId}/items/

+---------------+----------------+---------------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                         |
+===============+================+=====================================================================+
| Authorization | header         | Token received during authentication                                |
+---------------+----------------+---------------------------------------------------------------------+
| cartId        | url attribute  | Id of requested cart                                                |
+---------------+----------------+---------------------------------------------------------------------+
| variant       | request        | Code of item you want to add to cart                                |
+---------------+----------------+---------------------------------------------------------------------+
| quantity      | request        | Amount of variants you want to add to cart (cannot be lower than 1) |
+---------------+----------------+---------------------------------------------------------------------+

Example
^^^^^^^

To add a new item with one variant with code MEDIUM_MUG_CUP the cart with id 21(assume, that we didn't remove it in a previous example) use the method below.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/carts/21/items/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
                "variant": "MEDIUM_MUG_CUP",
                "quantity": 1
            }
        '

Example Response
^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 201 Created

.. code-block:: json

    {
        "id":58,
        "order":{
            "id":21,
            "items":[

            ],
            "items_total":175,
            "adjustments":[
                {

                }
            ],
            "adjustments_total":7515,
            "total":7690,
            "customer":{
                "id":1,
                "email":"shop@example.com",
                "first_name":"John",
                "last_name":"Doe",
                "user":{
                    "id":1,
                    "username":"shop@example.com",
                    "username_canonical":"shop@example.com"
                },
                "_links":{
                    "self":{
                        "href":"\/api\/v1\/customers\/1"
                    }
                }
            },
            "channel":{
                "code":"US_WEB",
                "_links":{
                    "self":{
                        "href":"\/api\/v1\/channels\/2"
                    }
                }
            },
            "currency_code":"USD",
            "checkout_state":"cart"
        },
        "quantity":1,
        "unit_price":175,
        "total":175,
        "units":[
            {
                "id":194,
                "adjustments":[

                ],
                "adjustments_total":0
            }
        ],
        "units_total":175,
        "adjustments":[

        ],
        "adjustments_total":0,
        "variant":{

        },
        "_links":{
            "product":{
                "href":"\/api\/v1\/products\/21"
            },
            "variant":{
                "href":"\/api\/v1\/products\/21\/variants\/61"
            }
        }
    }

.. tip::

    In Sylius the prices are stored as an integers. So in order to present a proper amount to the end user, you should divide price by 100 by default

Updating a Cart Item
--------------------

To change the quantity of cart item you will need to call the ``/api/v1/carts/{cartId}/items/{cartItemId}`` endpoint with ``PUT`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    PUT /api/v1/carts/{cartId}/items/{id}

+---------------+----------------+---------------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                         |
+===============+================+=====================================================================+
| Authorization | header         | Token received during authentication                                |
+---------------+----------------+---------------------------------------------------------------------+
| cartId        | url attribute  | Id of requested cart                                                |
+---------------+----------------+---------------------------------------------------------------------+
| cartItemId    | url attribute  | Id of requested cart item                                           |
+---------------+----------------+---------------------------------------------------------------------+
| quantity      | request        | Amount of variants you want to add to cart (cannot be lower than 1) |
+---------------+----------------+---------------------------------------------------------------------+

Example
^^^^^^^

.. code-block:: bash

To change a quantity to 3 of the cart item with id 58 of cart 21 use the method below.

    $ curl http://demo.sylius.org/api/v1/carts/21/items/58 \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PUT \
        --data '{"quantity": 3}'

.. note::

    If you are not sure where did the value 58 came from, check the previous response, and look for cart item id

.. tip::

    This action can be send with *PATCH* method as well

Example Response
^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

Now we can check what does the cart look like after changing quality

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/carts/21 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

Example Response
^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "id":21,
        "items":[
            {
                "id":58,
                "quantity":3,
                "unit_price":175,
                "total":73,
                "units":[
                    {
                        "id":194,
                        "adjustments":[
                            {
                                "id":215,
                                "type":"order_promotion",
                                "label":"Christmas",
                                "amount":-151
                            }
                        ],
                        "adjustments_total":-151
                    },
                    {
                        "id":195,
                        "adjustments":[
                            {
                                "id":216,
                                "type":"order_promotion",
                                "label":"Christmas",
                                "amount":-151
                            }
                        ],
                        "adjustments_total":-151
                    },
                    {
                        "id":196,
                        "adjustments":[
                            {
                                "id":217,
                                "type":"order_promotion",
                                "label":"Christmas",
                                "amount":-150
                            }
                        ],
                        "adjustments_total":-150
                    }
                ],
                "units_total":73,
                "adjustments":[

                ],
                "adjustments_total":0,
                "variant":{

                },
                "_links":{
                    "product":{
                        "href":"\/api\/v1\/products\/21"
                    },
                    "variant":{
                        "href":"\/api\/v1\/products\/21\/variants\/61"
                    }
                }
            }
        ],
        "items_total":73,
        "adjustments":[
            {
                "id":218,
                "type":"shipping",
                "label":"UPS",
                "amount":7515
            }
        ],
        "adjustments_total":7515,
        "total":7588,
        "customer":{
            "id":1,
            "email":"shop@example.com",
            "first_name":"John",
            "last_name":"Doe",
            "user":{
                "id":1,
                "username":"shop@example.com",
                "username_canonical":"shop@example.com"
            },
            "_links":{
                "self":{
                    "href":"\/api\/v1\/customers\/1"
                }
            }
        },
        "channel":{
            "code":"US_WEB",
            "_links":{
                "self":{
                    "href":"\/api\/v1\/channels\/2"
                }
            }
        },
        "currency_code":"USD",
        "checkout_state":"cart"
    }

.. tip::

    In this response you can see that promotion and shipping have been taken into account to calculate the appropriate price

Deleting a Cart Item
--------------------

To delete the cart item you will need to call the ``/api/v1/carts/{cartId}/items/{cartItemId}`` endpoint with ``DELETE`` method.

Definition
^^^^^^^^^^

.. code-block:: text

To delete the cart item with id 58 of cart 21 use the method below.

    DELETE /api/v1/carts/{cartId}/items/{id}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| cartId        | url attribute  | Id of requested cart                 |
+---------------+----------------+--------------------------------------+
| cartItemId    | url attribute  | Id of requested cart item            |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/carts/21/items/58 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X DELETE

Example Response
^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content
