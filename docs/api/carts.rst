Carts API
=========

These endpoints will allow you to easily manage cart and cart items. Base URI is `/api/v1/carts/`.

.. note::

    Remember that a **Cart** in Sylius is an **Order** in the state ``cart``.

    If you don't understand the difference between Cart and Order concepts in Sylius yet, please read :doc:`this article </book/orders/orders>` carefully.

Cart API response structure
----------------------------

If you request a cart via API, you will receive an object with the following fields:

+-------------------+---------------------------------------------------------------------------------------+
| Field             | Description                                                                           |
+===================+=======================================================================================+
| id                | Id of the cart                                                                        |
+-------------------+---------------------------------------------------------------------------------------+
| items             | List of items in the cart                                                             |
+-------------------+---------------------------------------------------------------------------------------+
| itemsTotal        | Sum of all items prices                                                               |
+-------------------+---------------------------------------------------------------------------------------+
| adjustments       | List of adjustments related to the cart                                               |
+-------------------+---------------------------------------------------------------------------------------+
| adjustmentsTotal  | Sum of all cart adjustments values                                                    |
+-------------------+---------------------------------------------------------------------------------------+
| total             | Sum of items total and adjustments total                                              |
+-------------------+---------------------------------------------------------------------------------------+
| customer          | :doc:`The customer object serialized with the default data </api/customers>` for cart |
+-------------------+---------------------------------------------------------------------------------------+
| channel           | :doc:`The channel object serialized with the default data </api/channels>`            |
+-------------------+---------------------------------------------------------------------------------------+
| currencyCode      | Currency of the cart                                                                  |
+-------------------+---------------------------------------------------------------------------------------+
| localeCode        | Locale of the cart                                                                    |
+-------------------+---------------------------------------------------------------------------------------+
| checkoutState     | State of the checkout process of the cart                                             |
+-------------------+---------------------------------------------------------------------------------------+

CartItem API response structure
-------------------------------

Each CartItem in an API response will be build as follows:

+-------------------+--------------------------------------------------------------------------------------------+
| Field             | Description                                                                                |
+===================+============================================================================================+
| id                | Id of the cart item                                                                        |
+-------------------+--------------------------------------------------------------------------------------------+
| quantity          | Quantity of item units                                                                     |
+-------------------+--------------------------------------------------------------------------------------------+
| unitPrice         | Price of each item unit                                                                    |
+-------------------+--------------------------------------------------------------------------------------------+
| total             | Sum of units total and adjustments total of that cart item                                 |
+-------------------+--------------------------------------------------------------------------------------------+
| units             | A collection of units related to the cart item                                             |
+-------------------+--------------------------------------------------------------------------------------------+
| unitsTotal        | Sum of all units prices of the cart item                                                   |
+-------------------+--------------------------------------------------------------------------------------------+
| adjustments       | List of adjustments related to the cart item                                               |
+-------------------+--------------------------------------------------------------------------------------------+
| adjustmentsTotal  | Sum of all item adjustments related to that cart item                                      |
+-------------------+--------------------------------------------------------------------------------------------+
| variant           | :doc:`The product variant object serialized with the default data </api/product_variants>` |
+-------------------+--------------------------------------------------------------------------------------------+
| _link[product]    | Relative link to product                                                                   |
+-------------------+--------------------------------------------------------------------------------------------+
| _link[variant]    | Relative link to variant                                                                   |
+-------------------+--------------------------------------------------------------------------------------------+
| _link[order]      | Relative link to order                                                                     |
+-------------------+--------------------------------------------------------------------------------------------+

CartItemUnit API response structure
-----------------------------------

Each CartItemUnit API response will be build as follows:

+-------------------+------------------------------------------+
| Field             | Description                              |
+===================+==========================================+
| id                | Id of the cart item unit                 |
+-------------------+------------------------------------------+
| adjustments       | List of adjustments related to the unit  |
+-------------------+------------------------------------------+
| adjustmentsTotal  | Sum of all units adjustments of the unit |
+-------------------+------------------------------------------+

Adjustment API response structure
---------------------------------

And each Adjustment will be build as follows:

+--------+----------------------------------------------------------+
| Field  | Description                                              |
+========+==========================================================+
| id     | Id of the adjustment                                     |
+--------+----------------------------------------------------------+
| type   | Type of the adjustment (E.g. *order_promotion* or *tax*) |
+--------+----------------------------------------------------------+
| label  | Label of the adjustment                                  |
+--------+----------------------------------------------------------+
| amount | Amount of the adjustment (value)                         |
+--------+----------------------------------------------------------+

.. note::

    If it is confusing to you, learn more about :doc:`Carts (Orders) in the component docs </components/Order/models>`
    and :doc:`Adjustments concept </book/orders/adjustments>`.

Creating a Cart
---------------

To create a new cart you will need to call the ``/api/v1/carts/`` endpoint with the ``POST`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    POST /api/v1/carts/

+---------------+----------------+----------------------------------------------------------+
| Parameter     | Parameter type | Description                                              |
+===============+================+==========================================================+
| Authorization | header         | Token received during authentication                     |
+---------------+----------------+----------------------------------------------------------+
| customer      | request        | Email of the related customer                            |
+---------------+----------------+----------------------------------------------------------+
| channel       | request        | Code of the related channel                              |
+---------------+----------------+----------------------------------------------------------+
| localeCode    | request        | Code of the locale in which the cart should be created   |
+---------------+----------------+----------------------------------------------------------+

Example
^^^^^^^

To create a new cart for the ``shop@example.com`` user in the ``US_WEB`` channel with the ``en_US`` locale use the below method:

.. warning::

    Remember, that it doesn't replicate the environment of shop usage. It is more like an admin part of cart creation, which will allow you to manage
    the cart from the admin perspective. ShopAPI is still an experimental concept.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/carts/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
                "customer": "shop@example.com",
                "channel": "US_WEB",
                "localeCode": "en_US"
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 201 Created

.. code-block:: json

    {
        "id":21,
        "items":[

        ],
        "itemsTotal":0,
        "adjustments":[

        ],
        "adjustmentsTotal":0,
        "total":0,
        "customer":{
            "id":1,
            "email":"shop@example.com",
            "firstName":"John",
            "lastName":"Doe",
            "user":{
                "id":1,
                "username":"shop@example.com",
                "usernameCanonical":"shop@example.com"
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
                    "href":"\/api\/v1\/channels\/US_WEB"
                }
            }
        },
        "currencyCode":"USD",
        "localeCode":"en_US",
        "checkoutState":"cart"
    }

.. note::

    A currency code will be added automatically based on the channel settings. Read more about channels :doc:`here </book/configuration/channels>`.

.. warning::

    If you try to create a resource without localeCode, channel or customer, you will receive a ``400 Bad Request`` error, that will contain validation errors.

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/carts/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST

Exemplary Response
^^^^^^^^^^^^^^^^^^

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

To retrieve a paginated list of carts you will need to call the ``/api/v1/carts/`` endpoint with the ``GET`` method.

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
| paginate      | query          | *(optional)* Number of carts displayed per page, by default = 10 |
+---------------+----------------+------------------------------------------------------------------+

Example
^^^^^^^

To see the first page of the paginated carts collection use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/carts/ \
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
                    "itemsTotal":0,
                    "adjustments":[

                    ],
                    "adjustmentsTotal":0,
                    "total":0,
                    "customer":{
                        "id":1,
                        "email":"shop@example.com",
                        "firstName":"John",
                        "lastName":"Doe",
                        "user":{
                            "id":1,
                            "username":"shop@example.com",
                            "enabled":true
                        },
                        "_links":{
                            "self":{
                                "href":"\/api\/v1\/customers\/1"
                            }
                        }
                    },
                    "channel":{
                        "id":1,
                        "code":"US_WEB",
                        "_links":{
                            "self":{
                                "href":"\/api\/v1\/channels\/US_WEB"
                            }
                        }
                    },
                    "currencyCode":"USD",
                    "localeCode":"en_US",
                    "checkoutState":"cart"
                }
            ]
        }
    }

Getting a Single Cart
---------------------

To retrieve details of the cart you will need to call the ``/api/v1/carts/{id}`` endpoint with ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/carts/{id}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| id            | url attribute  | Id of the requested cart             |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To see details of the cart with ``id = 21`` use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/carts/21 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

.. note::

    The *21* value was taken from the previous create response. Your value can be different.
    Check in the list of all carts if you are not sure which id should be used.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "id":21,
        "items":[

        ],
        "itemsTotal":0,
        "adjustments":[

        ],
        "adjustmentsTotal":0,
        "total":0,
        "customer":{
            "id":1,
            "email":"shop@example.com",
            "firstName":"John",
            "lastName":"Doe",
            "user":{
                "id":1,
                "username":"shop@example.com",
                "usernameCanonical":"shop@example.com"
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
                    "href":"\/api\/v1\/channels\/US_WEB"
                }
            }
        },
        "currencyCode":"USD",
        "localeCode":"en_US",
        "checkoutState":"cart"
    }

Deleting a Cart
---------------

To delete a cart you will need to call the ``/api/v1/carts/{id}`` endpoint with the ``DELETE`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    DELETE /api/v1/carts/{id}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| id            | url attribute  | Id of the requested cart             |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To delete the cart with ``id = 21`` use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/carts/21 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X DELETE

.. note::

    Remember the *21* value comes from the previous example. Here we are deleting a previously fetched cart, so it is the same id.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

Creating a Cart Item
--------------------

To add a new cart item to an existing cart you will need to call the ``/api/v1/carts/{cartId}/items/`` endpoint with ``POST`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    POST /api/v1/carts/{cartId}/items/

+---------------+----------------+----------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                    |
+===============+================+================================================================+
| Authorization | header         | Token received during authentication                           |
+---------------+----------------+----------------------------------------------------------------+
| cartId        | url attribute  | Id of the requested cart                                       |
+---------------+----------------+----------------------------------------------------------------+
| variant       | request        | Code of the item you want to add to the cart                   |
+---------------+----------------+----------------------------------------------------------------+
| quantity      | request        | Amount of variants you want to add to the cart (cannot be < 1) |
+---------------+----------------+----------------------------------------------------------------+

Example
^^^^^^^

To add a new item of a variant with code ``MEDIUM_MUG_CUP``
to the cart with id = 21 (assuming, that we didn't remove it in the previous example) use the below method:

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

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 201 Created

.. code-block:: json

    {
        "id":57,
        "quantity":1,
        "unitPrice":250,
        "total":250,
        "units":[
            {
                "id":165,
                "adjustments":[

                ],
                "adjustmentsTotal":0
            }
        ],
        "unitsTotal":250,
        "adjustments":[

        ],
        "adjustmentsTotal":0,
        "variant":{
            "id":331,
            "code":"MEDIUM_MUG_CUP",
            "optionValues":[
                {
                    "code":"mug_type_medium",
                    "translations":{
                        "en_US":{
                            "id":1,
                            "value":"Medium mug"
                        }
                    }
                }
            ],
            "position":2,
            "translations":{
                "en_US":{
                    "id":331,
                    "name":"Medium Mug"
                }
            },
            "tracked":false,
            "channelPricings":{
                "US_WEB": {
                    "channelCode": "US_WEB",
                    "price":250
                }
            }
        },
        "_links":{
            "order":{
                "href":"\/api\/v1\/orders\/21"
            },
            "product":{
                "href":"\/api\/v1\/products\/07f2044a-855d-3c56-9274-b5167c2d5809"
            },
            "variant":{
                "href":"\/api\/v1\/products\/07f2044a-855d-3c56-9274-b5167c2d5809\/variants\/MEDIUM_MUG_CUP"
            }
        }
    }
.. tip::

    In Sylius the prices are stored as an integers (``1059`` represents ``10.59$``).
    So in order to present a proper amount to the end user, you should divide price by 100 by default.

Updating a Cart Item
--------------------

To change the quantity of a cart item you will need to call the ``/api/v1/carts/{cartId}/items/{cartItemId}`` endpoint with the ``PUT`` or ``PATCH`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    PUT /api/v1/carts/{cartId}/items/{cartItemId}

+---------------+----------------+--------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                  |
+===============+================+==============================================================+
| Authorization | header         | Token received during authentication                         |
+---------------+----------------+--------------------------------------------------------------+
| cartId        | url attribute  | Id of the requested cart                                     |
+---------------+----------------+--------------------------------------------------------------+
| cartItemId    | url attribute  | Id of the requested cart item                                |
+---------------+----------------+--------------------------------------------------------------+
| quantity      | request        | Amount of items you want to have in the cart (cannot be < 1) |
+---------------+----------------+--------------------------------------------------------------+

Example
^^^^^^^

To change the quantity of the cart item with ``id = 57`` in the cart of ``id = 21`` to 3 use the below method:


.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/carts/21/items/57 \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PUT \
        --data '{"quantity": 3}'

.. tip::

    If you are not sure where does the value **58** come from, check the previous response, and look for the cart item id.


Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

Now we can check how does the cart look like after changing the quantity of a cart item.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/carts/21 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "id":21,
        "items":[
            {
                "id":57,
                "quantity":3,
                "unitPrice":250,
                "total":750,
                "units":[
                    {
                        "id":165,
                        "adjustments":[

                        ],
                        "adjustmentsTotal":0
                    },
                    {
                        "id":166,
                        "adjustments":[

                        ],
                        "adjustmentsTotal":0
                    },
                    {
                        "id":167,
                        "adjustments":[

                        ],
                        "adjustmentsTotal":0
                    }
                ],
                "unitsTotal":750,
                "adjustments":[

                ],
                "adjustmentsTotal":0,
                "variant":{
                    "id":331,
                    "code":"MEDIUM_MUG_CUP",
                    "optionValues":[
                        {
                            "code":"mug_type_medium",
                            "translations":{
                                "en_US":{
                                    "id":1,
                                    "value":"Medium mug"
                                }
                            }
                        }
                    ],
                    "position":2,
                    "translations":{
                        "en_US":{
                            "id":331,
                            "name":"Medium Mug"
                        }
                    },
                    "tracked":false,
                    "channelPricings":{
                        "US_WEB": {
                            "channelCode": "US_WEB",
                            "price":250
                        }
                    }
                },
                "_links":{
                    "order":{
                        "href":"\/api\/v1\/orders\/21"
                    },
                    "product":{
                        "href":"\/api\/v1\/products\/07f2044a-855d-3c56-9274-b5167c2d5809"
                    },
                    "variant":{
                        "href":"\/api\/v1\/products\/07f2044a-855d-3c56-9274-b5167c2d5809\/variants\/MEDIUM_MUG_CUP"
                    }
                }
            }
        ],
        "itemsTotal":750,
        "adjustments":[
            {
                "id":181,
                "type":"shipping",
                "label":"UPS",
                "amount":157
            }
        ],
        "adjustmentsTotal":157,
        "total":907,
        "customer":{
            "id":1,
            "email":"shop@example.com",
            "firstName":"John",
            "lastName":"Doe",
            "user":{
                "id":1,
                "username":"shop@example.com",
                "usernameCanonical":"shop@example.com"
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
                    "href":"\/api\/v1\/channels\/US_WEB"
                }
            }
        },
        "currencyCode":"USD",
        "localeCode":"en_US",
        "checkoutState":"cart"
    }

.. tip::

    In this response you can see that promotion and shipping have been taken into account to calculate the appropriate price.

Deleting a Cart Item
--------------------

To delete a cart item from a cart you will need to call the ``/api/v1/carts/{cartId}/items/{cartItemId}`` endpoint with the ``DELETE`` method.

Definition
^^^^^^^^^^

To delete the cart item with ``id = 58`` from the cart with ``id = 21`` use the below method:

.. code-block:: text

    DELETE /api/v1/carts/{cartId}/items/{cartItemId}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| cartId        | url attribute  | Id of the requested cart             |
+---------------+----------------+--------------------------------------+
| cartItemId    | url attribute  | Id of the requested cart item        |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/carts/21/items/58 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X DELETE

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content
