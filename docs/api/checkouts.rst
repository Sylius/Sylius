Checkouts API
=============

These endpoints will allow you to go through the order checkout from the admin perspective. It can be useful for integration such as Twillo or inspiration for your custom Shop API.
Base URI is `/api/v1/checkouts/`.

After you create a cart (an empty order) and add some items to it, you can start the checkout via API.
This basically means updating the order with concrete information, step by step, in a correct order.

Sylius checkout flow is build on 4 steps which has to be done in given order (unless one will customize it)

+------------+--------------------------------------------------+
| Step       | Description                                      |
+============+==================================================+
| addressing | Shipping and billing address is assigned to cart |
+------------+--------------------------------------------------+
| shipping   | Choosing a shipping method from available ones   |
+------------+--------------------------------------------------+
| payment    | Choosing a payment method from available ones    |
+------------+--------------------------------------------------+
| finalize   | The order is built and one can confirm its data  |
+------------+--------------------------------------------------+

After the order will be finished following action will be available for admin

+------------------------+-------------------------+
| Action                 | Description             |
+========================+=========================+
| Cancelling             | Cancelling the order    |
+------------------------+-------------------------+
| Shipping               | Ship the order          |
+------------------------+-------------------------+
| Completing the payment | Complete orders payment |
+------------------------+-------------------------+

.. tip::

    If you are not familiar with concept of checkout in Sylius, please read :doc:`this article </book/orders/checkout>` carefully first.

.. note::

    We do not present a order serialization in this chapter, because it is the same order serialization as described in :doc:`article about orders </api/orders>`.

Addressing step
---------------

After you added some items to the cart, to start the checkout you simply need to provide a shipping address. You can also specify a different billing address if needed.

Definition
^^^^^^^^^^

.. code-block:: text

    PUT /api/v1/checkouts/addressing/{id}

+------------------------------+----------------+------------------------------------------------------------------------------------------------------+
| Parameter                    | Parameter type | Description                                                                                          |
+==============================+================+======================================================================================================+
| Authorization                | header         | Token received during authentication                                                                 |
+------------------------------+----------------+------------------------------------------------------------------------------------------------------+
| id                           | url attribute  | Id of the requested cart                                                                             |
+------------------------------+----------------+------------------------------------------------------------------------------------------------------+
| customer[email]              | request        | Email of customer which is finishing                                                                 |
+------------------------------+----------------+------------------------------------------------------------------------------------------------------+
| different_billing_address    | request        | If false a billing address fields will not be required and data from shipping address will be copied |
+------------------------------+----------------+------------------------------------------------------------------------------------------------------+
| shipping_address[first_name] | request        | Firstname for shipping address                                                                       |
+------------------------------+----------------+------------------------------------------------------------------------------------------------------+
| shipping_address[last_name]  | request        | Lastname for shipping address                                                                        |
+------------------------------+----------------+------------------------------------------------------------------------------------------------------+
| shipping_address[city]       | request        | City name                                                                                            |
+------------------------------+----------------+------------------------------------------------------------------------------------------------------+
| shipping_address[postcode]   | request        | Postcode                                                                                             |
+------------------------------+----------------+------------------------------------------------------------------------------------------------------+
| shipping_address[street]     | request        | Address line 1                                                                                       |
+------------------------------+----------------+------------------------------------------------------------------------------------------------------+
| shipping_address[country]    | request        | Id of the country                                                                                    |
+------------------------------+----------------+------------------------------------------------------------------------------------------------------+
| shipping_address[province]   | request        | *(optional)* Id of the province                                                                      |
+------------------------------+----------------+------------------------------------------------------------------------------------------------------+
| billing_address[first_name]  | request        | *(optional)* Firstname for billing address                                                           |
+------------------------------+----------------+------------------------------------------------------------------------------------------------------+
| billing_address[last_name]   | request        | *(optional)* Lastname for billing address                                                            |
+------------------------------+----------------+------------------------------------------------------------------------------------------------------+
| billing_address[city]        | request        | *(optional)* City name                                                                               |
+------------------------------+----------------+------------------------------------------------------------------------------------------------------+
| billing_address[postcode]    | request        | *(optional)* Postcode                                                                                |
+------------------------------+----------------+------------------------------------------------------------------------------------------------------+
| billing_address[street]      | request        | *(optional)* Address line 1                                                                          |
+------------------------------+----------------+------------------------------------------------------------------------------------------------------+
| billing_address[country]     | request        | *(optional)* Id of the country                                                                       |
+------------------------------+----------------+------------------------------------------------------------------------------------------------------+
| billing_address[province]    | request        | *(optional)* Id of the province                                                                      |
+------------------------------+----------------+------------------------------------------------------------------------------------------------------+

.. note::

    Remember a cart with `id = 21` from the cart docs? We will take the same cart as a exemplary cart for checkout process

Example
^^^^^^^

To address the cart for user with which lives in `Los Angeles` in United States, one can use following snippet:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/checkouts/addressing/21 \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PUT \
        --data '
            {
                "shipping_address": {
                    "first_name": "Elon",
                    "last_name": "Musk",
                    "street": "10941 Savona Rd",
                    "country_code": "US",
                    "city": "’Los Angeles",
                    "postcode": "CA 90077"
                },
                "different_billing_address": false
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

Now you can check the state of order asking for checkout summary:

Example
^^^^^^^

To check the checkout process state for cart with `id = 21`, we need to execute this command:

.. code-block:: bash

    $ curl http://127.0.0.1:8000/api/v1/checkouts/21 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 Ok

.. code-block:: json

    {
        "id":21,
        "items":[
            {
                "id":74,
                "quantity":1,
                "unit_price":100000,
                "total":100000,
                "units":[
                    {
                        "id":228,
                        "adjustments":[

                        ],
                        "adjustments_total":0
                    }
                ],
                "units_total":100000,
                "adjustments":[

                ],
                "adjustments_total":0,
                "variant":{
                    "id":331,
                    "code":"MEDIUM_MUG_CUP",
                    "option_values":[
                        {
                            "code":"mug_type_medium"
                        }
                    ],
                    "position":2,
                    "translations":{
                        "en_US":{

                        }
                    },
                    "on_hold":0,
                    "on_hand":10,
                    "tracked":false,
                    "channel_pricings":[
                        {
                            "channel":{
                                "id":1,
                                "code":"US_WEB",
                                "name":"US Web Store",
                                "hostname":"localhost",
                                "color":"MediumPurple",
                                "created_at":"2017-02-14T11:10:02+0100",
                                "updated_at":"2017-02-14T11:10:02+0100",
                                "enabled":true,
                                "tax_calculation_strategy":"order_items_based",
                                "_links":{
                                    "self":{
                                        "href":"\/api\/v1\/channels\/1"
                                    }
                                }
                            },
                            "price":100000
                        }
                    ]
                },
                "_links":{
                    "product":{
                        "href":"\/api\/v1\/products\/5"
                    },
                    "variant":{
                        "href":"\/api\/v1\/products\/5\/variants\/331"
                    }
                }
            }
        ],
        "items_total":100000,
        "adjustments":[
            {
                "id":249,
                "type":"shipping",
                "label":"UPS",
                "amount":8787
            }
        ],
        "adjustments_total":8787,
        "total":108787,
        "state":"cart",
        "customer":{
            "id":1,
            "email":"shop@example.com",
            "email_canonical":"shop@example.com",
            "first_name":"John",
            "last_name":"Doe",
            "gender":"u",
            "user":{
                "id":1,
                "username":"shop@example.com",
                "username_canonical":"shop@example.com",
                "roles":[
                    "ROLE_USER"
                ],
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
            "name":"US Web Store",
            "hostname":"localhost",
            "color":"MediumPurple",
            "created_at":"2017-02-14T11:10:02+0100",
            "updated_at":"2017-02-14T11:10:02+0100",
            "enabled":true,
            "tax_calculation_strategy":"order_items_based",
            "_links":{
                "self":{
                    "href":"\/api\/v1\/channels\/1"
                }
            }
        },
        "shipping_address":{
            "first_name":"Elon",
            "last_name":"Musk",
            "country_code":"US",
            "street":"10941 Savona Rd",
            "city":"\u2019Los Angeles",
            "postcode":"CA 90077"
        },
        "billing_address":{
            "first_name":"Elon",
            "last_name":"Musk",
            "country_code":"US",
            "street":"10941 Savona Rd",
            "city":"\u2019Los Angeles",
            "postcode":"CA 90077"
        },
        "payments":[
            {
                "id":21,
                "method":{
                    "id":1,
                    "code":"cash_on_delivery"
                },
                "amount":108787,
                "state":"cart"
            }
        ],
        "shipments":[
            {
                "id":21,
                "state":"cart",
                "method":{
                    "code":"ups",
                    "enabled":true
                }
            }
        ],
        "currency_code":"USD",
        "locale_code":"en_US",
        "checkout_state":"addressed"
    }

Of course you can specify different shipping and billing address. If Elon would like to to send a present to NASA administrator, Frederick D. Gregory, he could send the following request:

.. code-block:: bash

    $ curl http://127.0.0.1:8000/api/v1/checkouts/addressing/21 \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PUT \
        --data '
            {
                "shipping_address": {
                    "first_name": " Frederick D.",
                    "last_name": "Gregory",
                    "street": "300 E St SW",
                    "country_code": "US",
                    "city": "’Washington",
                    "postcode": "DC 20546"
                },
                "different_billing_address": false,
                "billing_address": {
                    "first_name": "Elon",
                    "last_name": "Musk",
                    "street": "10941 Savona Rd",
                    "country_code": "US",
                    "city": "’Los Angeles",
                    "postcode": "CA 90077"
                }
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

Now you can check the state of order asking for checkout summary:

Example
^^^^^^^

To check the checkout process state for cart with `id = 21`, we need to execute this command:

.. code-block:: bash

    $ curl http://127.0.0.1:8000/api/v1/checkouts/21 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 Ok

.. code-block:: json

    {
        "id":21,
        "items":[
            {
                "id":74,
                "quantity":1,
                "unit_price":100000,
                "total":100000,
                "units":[
                    {
                        "id":228,
                        "adjustments":[

                        ],
                        "adjustments_total":0
                    }
                ],
                "units_total":100000,
                "adjustments":[

                ],
                "adjustments_total":0,
                "variant":{
                    "id":331,
                    "code":"MEDIUM_MUG_CUP",
                    "option_values":[
                        {
                            "code":"mug_type_medium"
                        }
                    ],
                    "position":2,
                    "translations":{
                        "en_US":{

                        }
                    },
                    "on_hold":0,
                    "on_hand":10,
                    "tracked":false,
                    "channel_pricings":[
                        {
                            "channel":{
                                "id":1,
                                "code":"US_WEB",
                                "name":"US Web Store",
                                "hostname":"localhost",
                                "color":"MediumPurple",
                                "created_at":"2017-02-14T11:10:02+0100",
                                "updated_at":"2017-02-14T11:10:02+0100",
                                "enabled":true,
                                "tax_calculation_strategy":"order_items_based",
                                "_links":{
                                    "self":{
                                        "href":"\/api\/v1\/channels\/1"
                                    }
                                }
                            },
                            "price":100000
                        }
                    ]
                },
                "_links":{
                    "product":{
                        "href":"\/api\/v1\/products\/5"
                    },
                    "variant":{
                        "href":"\/api\/v1\/products\/5\/variants\/331"
                    }
                }
            }
        ],
        "items_total":100000,
        "adjustments":[
            {
                "id":250,
                "type":"shipping",
                "label":"UPS",
                "amount":8787
            }
        ],
        "adjustments_total":8787,
        "total":108787,
        "state":"cart",
        "customer":{
            "id":1,
            "email":"shop@example.com",
            "email_canonical":"shop@example.com",
            "first_name":"John",
            "last_name":"Doe",
            "gender":"u",
            "user":{
                "id":1,
                "username":"shop@example.com",
                "username_canonical":"shop@example.com",
                "roles":[
                    "ROLE_USER"
                ],
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
            "name":"US Web Store",
            "hostname":"localhost",
            "color":"MediumPurple",
            "created_at":"2017-02-14T11:10:02+0100",
            "updated_at":"2017-02-14T11:10:02+0100",
            "enabled":true,
            "tax_calculation_strategy":"order_items_based",
            "_links":{
                "self":{
                    "href":"\/api\/v1\/channels\/1"
                }
            }
        },
        "shipping_address":{
            "first_name":"Frederick D.",
            "last_name":"Gregory",
            "country_code":"US",
            "street":"300 E St SW",
            "city":"\u2019Washington",
            "postcode":"DC 20546"
        },
        "billing_address":{
            "first_name":"Frederick D.",
            "last_name":"Gregory",
            "country_code":"US",
            "street":"300 E St SW",
            "city":"\u2019Washington",
            "postcode":"DC 20546"
        },
        "payments":[
            {
                "id":21,
                "method":{
                    "id":1,
                    "code":"cash_on_delivery"
                },
                "amount":108787,
                "state":"cart"
            }
        ],
        "shipments":[
            {
                "id":21,
                "state":"cart",
                "method":{
                    "code":"ups",
                    "enabled":true
                }
            }
        ],
        "currency_code":"USD",
        "locale_code":"en_US",
        "checkout_state":"addressed"
    }

Shipping step
-------------

When order contains the address information, we are able to determine the stock locations and available shipping methods.
First we need to get information about available shipping methods to know our choices:

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/checkouts/select-shipping/{id}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| id            | url attribute  | Id of the requested cart             |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To check available shipping methods for previously addressed cart, one could use following command:

.. code-block:: bash

    $ curl http://127.0.0.1:8000/api/v1/checkouts/select-shipping/21 \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json"

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "shipments":[
            {
                "methods":[
                    {
                        "id":1,
                        "code":"ups",
                        "name":"UPS",
                        "description":"Dolorem consequatur itaque neque non voluptas dolor.",
                        "price":8787
                    },
                    {
                        "id":2,
                        "code":"dhl_express",
                        "name":"DHL Express",
                        "description":"Voluptatem ipsum dolor vitae corrupti eum repellat.",
                        "price":3549
                    },
                    {
                        "id":3,
                        "code":"fedex",
                        "name":"FedEx",
                        "description":"Qui nostrum minus accusantium molestiae voluptatem eaque.",
                        "price":3775
                    }
                ]
            }
        ]
    }

Response contains the proposed shipments and for each, it also has a list of shipping methods available with calculated price.

.. warning::

    Because of custom calculation logic for this endpoint the regular rules of overriding does not apply for this endpoint. In order to achieve different message one has to provide custom controller and build the message by his own. Exemplary implementation can be found `here`__

Next step is updating the order with the types of shipping method that one has selected. A PUT request has to be send for each available shipment,

Definition
^^^^^^^^^^

.. code-block:: text

    PUT /api/v1/checkouts/select-shipping/{id}

+------------------------+----------------+--------------------------------------+
| Parameter              | Parameter type | Description                          |
+========================+================+======================================+
| Authorization          | header         | Token received during authentication |
+------------------------+----------------+--------------------------------------+
| id                     | url attribute  | Id of the requested cart             |
+------------------------+----------------+--------------------------------------+
| shipments[X]['method'] | request        | Code of chosen shipping method       |
+------------------------+----------------+--------------------------------------+

Where X is a number of shipment in returned array.

Example
^^^^^^^

To choose a `DHL Express` method for our shipment (the cheapest one), one could use the following code:

.. code-block:: bash

    $ curl http://127.0.0.1:8000/api/v1/checkouts/select-shipping/21 \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PUT \
        --data '
        {
            "shipments": [
                {
                    "method": "dhl_express"
                }
            ]
        }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

Now you can check the state of order asking for checkout summary just like before:

Example
^^^^^^^

To check the checkout process state for cart with `id = 21`, we need to execute this command:

.. code-block:: bash

    $ curl http://127.0.0.1:8000/api/v1/checkouts/21 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 Ok

    {
        "id":21,
        "items":[
            {
                "id":74,
                "quantity":1,
                "unit_price":100000,
                "total":100000,
                "units":[
                    {
                        "id":228,
                        "adjustments":[

                        ],
                        "adjustments_total":0
                    }
                ],
                "units_total":100000,
                "adjustments":[

                ],
                "adjustments_total":0,
                "variant":{
                    "id":331,
                    "code":"MEDIUM_MUG_CUP",
                    "option_values":[
                        {
                            "code":"mug_type_medium"
                        }
                    ],
                    "position":2,
                    "translations":{
                        "en_US":{

                        }
                    },
                    "on_hold":0,
                    "on_hand":10,
                    "tracked":false,
                    "channel_pricings":[
                        {
                            "channel":{
                                "id":1,
                                "code":"US_WEB",
                                "name":"US Web Store",
                                "hostname":"localhost",
                                "color":"MediumPurple",
                                "created_at":"2017-02-14T11:10:02+0100",
                                "updated_at":"2017-02-14T11:10:02+0100",
                                "enabled":true,
                                "tax_calculation_strategy":"order_items_based",
                                "_links":{
                                    "self":{
                                        "href":"\/api\/v1\/channels\/1"
                                    }
                                }
                            },
                            "price":100000
                        }
                    ]
                },
                "_links":{
                    "product":{
                        "href":"\/api\/v1\/products\/5"
                    },
                    "variant":{
                        "href":"\/api\/v1\/products\/5\/variants\/331"
                    }
                }
            }
        ],
        "items_total":100000,
        "adjustments":[
            {
                "id":251,
                "type":"shipping",
                "label":"DHL Express",
                "amount":3549
            }
        ],
        "adjustments_total":3549,
        "total":103549,
        "state":"cart",
        "customer":{
            "id":1,
            "email":"shop@example.com",
            "email_canonical":"shop@example.com",
            "first_name":"John",
            "last_name":"Doe",
            "gender":"u",
            "user":{
                "id":1,
                "username":"shop@example.com",
                "username_canonical":"shop@example.com",
                "roles":[
                    "ROLE_USER"
                ],
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
            "name":"US Web Store",
            "hostname":"localhost",
            "color":"MediumPurple",
            "created_at":"2017-02-14T11:10:02+0100",
            "updated_at":"2017-02-14T11:10:02+0100",
            "enabled":true,
            "tax_calculation_strategy":"order_items_based",
            "_links":{
                "self":{
                    "href":"\/api\/v1\/channels\/1"
                }
            }
        },
        "shipping_address":{
            "first_name":"Frederick D.",
            "last_name":"Gregory",
            "country_code":"US",
            "street":"300 E St SW",
            "city":"\u2019Washington",
            "postcode":"DC 20546"
        },
        "billing_address":{
            "first_name":"Frederick D.",
            "last_name":"Gregory",
            "country_code":"US",
            "street":"300 E St SW",
            "city":"\u2019Washington",
            "postcode":"DC 20546"
        },
        "payments":[
            {
                "id":21,
                "method":{
                    "id":1,
                    "code":"cash_on_delivery"
                },
                "amount":103549,
                "state":"cart"
            }
        ],
        "shipments":[
            {
                "id":21,
                "state":"cart",
                "method":{
                    "code":"dhl_express",
                    "enabled":true
                }
            }
        ],
        "currency_code":"USD",
        "locale_code":"en_US",
        "checkout_state":"shipping_selected"
    }

Payment step
------------

When we are done with shipping choices and we know the final price of an order, we can select a payment method.

To obtain a list of available payment methods for this order, simply call a GET request again:

.. code-block:: text

    GET /api/v1/checkouts/44

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "methods": {
            "1": {
                "_links": {
                    "self": {
                        "href": "/app_dev.php/api/v1/payment-methods/1"
                    }
                },
                "id": 1,
                "code": "dummy",
                "created_at": "2014-12-03T09:54:28+0000",
                "updated_at": "2014-12-03T09:54:28+0000"
            },
            "2": {
                "_links": {
                    "self": {
                        "href": "/app_dev.php/api/v1/payment-methods/2"
                    }
                },
                "id": 2,
                "code": "paypal_express_checkout",
                "created_at": "2014-12-03T09:54:28+0000",
                "updated_at": "2014-12-03T09:54:28+0000"
            },
            "3": {
                "_links": {
                    "self": {
                        "href": "/app_dev.php/api/v1/payment-methods/3"
                    }
                },
                "id": 3,
                "code": "stripe",
                "created_at": "2014-12-03T09:54:28+0000",
                "updated_at": "2014-12-03T09:54:28+0000"
            },
            "4": {
                "_links": {
                    "self": {
                        "href": "/app_dev.php/api/v1/payment-methods/4"
                    }
                },
                "id": 4,
                "code": "be_2_bill",
                "created_at": "2014-12-03T09:54:28+0000",
                "updated_at": "2014-12-03T09:54:28+0000"
            },
            "5": {
                "_links": {
                    "self": {
                        "href": "/app_dev.php/api/v1/payment-methods/5"
                    }
                },
                "id": 5,
                "code": "stripe_checkout",
                "created_at": "2014-12-03T09:54:28+0000",
                "updated_at": "2014-12-03T09:54:28+0000"
            }
        },
        "payment": {
            "_links": {
                "self": {
                  "href": "/app_dev.php/api/v1/payments/2"
                },
                "order": {
                    "href": "/app_dev.php/api/v1/orders/52"
                }
            },
            "id": 2,
            "amount": 1504750,
            "created_at": "2014-12-15T14:57:28+0000",
            "updated_at": "2014-12-15T14:57:28+0000",
            "state": "new"
        }
    }


With that information, another PUT request with the id of payment method is enough to proceed:

.. code-block:: text

    PUT /api/v1/checkouts/44

Parameters
~~~~~~~~~~

payments[X][method]
    The id of the payment method, where X is the payment number.
    Leave empty to add new

Response
~~~~~~~~

Response will contain the updated order information.

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "adjustments": [
        ],
        "adjustments_total": 4750,
        "billing_address": {
        },
        "channel": {
        },
        "checkout_state": "payment",
        "comments": [],
        "confirmed": true,
        "created_at": "2014-12-15T13:15:22+0000",
        "email": "xschaefer@example.com",
        "expires_at": "2014-12-15T16:15:22+0000",
        "id": 52,
        "items": [
        ],
        "items_total": 1500000,
        "payments": [
            {
                "_links": {
                    "order": {
                        "href": "/app_dev.php/api/v1/orders/52"
                    },
                    "payment-method": {
                        "href": "/app_dev.php/api/v1/payment-methods/1"
                    },
                    "self": {
                        "href": "/app_dev.php/api/v1/payments/51"
                    }
                },
                "amount": 1504750,
                "created_at": "2014-12-15T15:02:54+0000",
                "id": 51,
                "method": {
                    "_links": {
                        "self": {
                            "href": "/app_dev.php/api/v1/payment-methods/1"
                        }
                    },
                    "created_at": "2014-12-03T09:54:28+0000",
                    "id": 1,
                    "name": "Dummy",
                    "updated_at": "2014-12-03T09:54:28+0000"
                },
                "state": "new",
                "updated_at": "2014-12-15T15:02:55+0000"
            }
        ],
        "shipments": [
        ],
        "shipping_address": {
        },
        "state": "cart",
        "total": 1504750,
        "updated_at": "2014-12-15T15:02:55+0000",
        "user": {
        }
    }

Finalize step
-------------

Now your order is fully constructed, you can get its latest snapshot by calling your last GET request:

.. code-block:: text

    GET /api/v1/checkouts/44

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "adjustments": [
            {
                "amount": 0,
                "created_at": "2014-12-15T13:37:29+0000",
                "description": "No tax (0%)",
                "id": 205,
                "type": "tax",
                "locked": false,
                "neutral": false,
                "updated_at": "2014-12-15T13:37:29+0000"
            },
            {
                "amount": 5000,
                "created_at": "2014-12-15T14:30:41+0000",
                "description": "FedEx World Shipping",
                "id": 207,
                "type": "shipping",
                "locked": false,
                "neutral": false,
                "updated_at": "2014-12-15T14:30:41+0000"
            },
            {
                "amount": -250,
                "created_at": "2014-12-15T14:30:41+0000",
                "description": "Christmas Sale for orders over 100 EUR.",
                "id": 208,
                "type": "promotion",
                "locked": false,
                "neutral": false,
                "updated_at": "2014-12-15T14:30:41+0000"
            }
        ],
        "adjustments_total": 4750,
        "billing_address": {
            "_links": {
                "country": {
                    "href": "/app_dev.php/api/v1/countries/9"
                }
            },
            "city": "New York",
            "created_at": "2014-12-15T13:37:28+0000",
            "first_name": "John",
            "id": 106,
            "last_name": "Doe",
            "postcode": "12435",
            "street": "Test",
            "updated_at": "2014-12-15T13:37:29+0000"
        },
        "channel": {
            "_links": {
                "self": {
                    "href": "/app_dev.php/api/v1/channels/3"
                }
            },
            "code": "WEB-US",
            "color": "Pink",
            "created_at": "2014-12-03T09:54:28+0000",
            "enabled": true,
            "id": 3,
            "name": "United States Webstore",
            "type": "web",
            "updated_at": "2014-12-03T09:58:29+0000"
        },
        "checkout_state": "payment_selected",
        "comments": [],
        "created_at": "2014-12-15T13:15:22+0000",
        "updated_at": "2014-12-15T15:02:55+0000",
        "expires_at": "2014-12-15T16:15:22+0000",
        "id": 52,
        "items": [
            {
                "_links": {
                    "product": {
                        "href": "/app_dev.php/api/v1/products/101"
                    },
                    "variant": {
                        "href": "/app_dev.php/api/v1/products/101/variants/779"
                    }
                },
                "adjustments": [],
                "adjustments_total": 0,
                "id": 277,
                "immutable": false,
                "inventory_units": [
                    {
                        "_links": {
                            "order": {
                                "href": "/app_dev.php/api/v1/orders/52"
                            }
                        },
                        "created_at": "2014-12-15T13:18:48+0000",
                        "id": 828,
                        "inventory_state": "checkout",
                        "updated_at": "2014-12-15T14:30:41+0000"
                    },
                    {
                        "_links": {
                            "order": {
                                "href": "/app_dev.php/api/v1/orders/52"
                            }
                        },
                        "created_at": "2014-12-15T13:18:48+0000",
                        "id": 829,
                        "inventory_state": "checkout",
                        "updated_at": "2014-12-15T14:30:41+0000"
                    },
                    {
                        "_links": {
                            "order": {
                                "href": "/app_dev.php/api/v1/orders/52"
                            }
                        },
                        "created_at": "2014-12-15T13:18:48+0000",
                        "id": 830,
                        "inventory_state": "checkout",
                        "updated_at": "2014-12-15T14:30:41+0000"
                    }
                ],
                "quantity": 3,
                "total": 1500000,
                "unit_price": 500000,
                "variant": {
                    "available_on": "2014-04-01T06:43:02+0000",
                    "created_at": "2014-12-03T09:54:35+0000",
                    "id": 779,
                    "master": true,
                    "object": {
                        "attributes": [
                            {
                                "id": 238,
                                "name": "Book author",
                                "presentation": "Author",
                                "value": "Marlen Yost"
                            },
                            {
                                "id": 239,
                                "name": "Book ISBN",
                                "presentation": "ISBN",
                                "value": "326ccbc7-92d1-3aec-b3af-df8afdc5651d"
                            },
                            {
                                "id": 240,
                                "name": "Book pages",
                                "presentation": "Number of pages",
                                "value": "149"
                            }
                        ],
                        "created_at": "2014-12-03T09:54:35+0000",
                        "description": "Et eveniet voluptas ut magni vero temporibus nihil. Omnis possimus accusantium quia corporis culpa. Et recusandae asperiores qui architecto culpa autem sint accusantium. Officiis iusto accusantium perferendis aliquid ducimus.",
                        "id": 101,
                        "name": "Book \"Quidem\" by \"Marlen Yost\"",
                        "options": [],
                        "short_description": "Distinctio quos est eaque fugit totam repellendus.",
                        "updated_at": "2014-12-03T09:54:35+0000"
                    },
                    "options": [],
                    "sku": "326ccbc7-92d1-3aec-b3af-df8afdc5651d",
                    "updated_at": "2014-12-03T09:54:35+0000"
                }
            }
        ],
        "items_total": 1500000,
        "payments": [
            {
                "_links": {
                    "order": {
                        "href": "/app_dev.php/api/v1/orders/52"
                    },
                    "payment-method": {
                        "href": "/app_dev.php/api/v1/payment-methods/1"
                    },
                    "self": {
                        "href": "/app_dev.php/api/v1/payments/51"
                    }
                },
                "amount": 1504750,
                "created_at": "2014-12-15T15:02:54+0000",
                "id": 51,
                "method": {
                    "_links": {
                        "self": {
                            "href": "/app_dev.php/api/v1/payment-methods/1"
                        }
                    },
                    "created_at": "2014-12-03T09:54:28+0000",
                    "id": 1,
                    "name": "Dummy",
                    "updated_at": "2014-12-03T09:54:28+0000"
                },
                "state": "new",
                "updated_at": "2014-12-15T15:02:55+0000"
            }
        ],
        "shipments": [
            {
                "_links": {
                    "method": {
                        "href": "/app_dev.php/api/v1/shipping-methods/4"
                    },
                    "order": {
                        "href": "/app_dev.php/api/v1/orders/52"
                    },
                    "self": {
                        "href": "/app_dev.php/api/v1/shipments/51"
                    }
                },
                "created_at": "2014-12-15T14:30:40+0000",
                "id": 51,
                "method": {
                    "_links": {
                        "self": {
                            "href": "/app_dev.php/api/v1/shipping-methods/4"
                        },
                        "zone": {
                            "href": "/app_dev.php/api/v1/zones/4"
                        }
                    },
                    "calculator": "flexible_rate",
                    "category_requirement": 1,
                    "configuration": {
                        "additional_item_cost": 500,
                        "additional_item_limit": 10,
                        "first_item_cost": 4000
                    },
                    "created_at": "2014-12-03T09:54:28+0000",
                    "enabled": true,
                    "id": 4,
                    "name": "FedEx World Shipping",
                    "updated_at": "2014-12-03T09:54:28+0000"
                },
                "state": "checkout",
                "updated_at": "2014-12-15T14:30:41+0000"
            }
        ],
        "shipping_address": {
            "_links": {
                "country": {
                    "href": "/app_dev.php/api/v1/countries/9"
                }
            },
            "city": "New York",
            "created_at": "2014-12-15T13:37:28+0000",
            "first_name": "John",
            "id": 105,
            "last_name": "Doe",
            "postcode": "12435",
            "street": "Test",
            "updated_at": "2014-12-15T13:37:29+0000"
        },
        "state": "cart",
        "total": 1504750
    }

This is how your final order looks, if you are happy with that response, simply call another PUT to confirm the checkout, which will became a real order and appear in the backend.

.. code-block:: text

    PUT /api/v1/checkouts/44

Response
~~~~~~~~

Final response contains the full order information, now you can call the purchase action to actually pay for the order.

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "adjustments": [
            {
                "amount": 0,
                "created_at": "2014-12-15T13:37:29+0000",
                "label": "No tax (0%)",
                "id": 205,
                "type": "tax",
                "locked": false,
                "neutral": false,
                "updated_at": "2014-12-15T13:37:29+0000"
            },
            {
                "amount": 5000,
                "created_at": "2014-12-15T14:30:41+0000",
                "label": "FedEx World Shipping",
                "id": 207,
                "type": "shipping",
                "locked": false,
                "neutral": false,
                "updated_at": "2014-12-15T14:30:41+0000"
            },
            {
                "amount": -250,
                "created_at": "2014-12-15T14:30:41+0000",
                "description": "Christmas Sale for orders over 100 EUR.",
                "id": 208,
                "type": "order_promotion",
                "locked": false,
                "neutral": false,
                "updated_at": "2014-12-15T14:30:41+0000"
            }
        ],
        "adjustments_total": 4750,
        "billing_address": {
            "_links": {
                "country": {
                    "href": "/app_dev.php/api/v1/countries/9"
                }
            },
            "city": "New York",
            "created_at": "2014-12-15T13:37:28+0000",
            "first_name": "John",
            "id": 106,
            "last_name": "Doe",
            "postcode": "12435",
            "street": "Test",
            "updated_at": "2014-12-15T13:37:29+0000"
        },
        "channel": {
            "_links": {
                "self": {
                    "href": "/app_dev.php/api/v1/channels/3"
                }
            },
            "code": "WEB-US",
            "color": "Pink",
            "created_at": "2014-12-03T09:54:28+0000",
            "enabled": true,
            "id": 3,
            "name": "United States Webstore",
            "type": "web",
            "updated_at": "2014-12-03T09:58:29+0000"
        },
        "comments": [],
        "created_at": "2014-12-15T13:15:22+0000",
        "updated_at": "2014-12-15T15:02:55+0000",
        "expires_at": "2014-12-15T16:15:22+0000",
        "id": 52,
        "items": [
            {
                "_links": {
                    "product": {
                        "href": "/app_dev.php/api/v1/products/101"
                    },
                    "variant": {
                        "href": "/app_dev.php/api/v1/products/101/variants/779"
                    }
                },
                "adjustments": [],
                "adjustments_total": 0,
                "id": 277,
                "immutable": false,
                "inventory_units": [
                    {
                        "_links": {
                            "order": {
                                "href": "/app_dev.php/api/v1/orders/52"
                            }
                        },
                        "created_at": "2014-12-15T13:18:48+0000",
                        "id": 828,
                        "inventory_state": "checkout",
                        "updated_at": "2014-12-15T14:30:41+0000"
                    },
                    {
                        "_links": {
                            "order": {
                                "href": "/app_dev.php/api/v1/orders/52"
                            }
                        },
                        "created_at": "2014-12-15T13:18:48+0000",
                        "id": 829,
                        "inventory_state": "checkout",
                        "updated_at": "2014-12-15T14:30:41+0000"
                    },
                    {
                        "_links": {
                            "order": {
                                "href": "/app_dev.php/api/v1/orders/52"
                            }
                        },
                        "created_at": "2014-12-15T13:18:48+0000",
                        "id": 830,
                        "inventory_state": "checkout",
                        "updated_at": "2014-12-15T14:30:41+0000"
                    }
                ],
                "quantity": 3,
                "total": 1500000,
                "unit_price": 500000,
                "variant": {
                    "available_on": "2014-04-01T06:43:02+0000",
                    "created_at": "2014-12-03T09:54:35+0000",
                    "id": 779,
                    "master": true,
                    "object": {
                        "attributes": [
                            {
                                "id": 238,
                                "name": "Book author",
                                "presentation": "Author",
                                "value": "Marlen Yost"
                            },
                            {
                                "id": 239,
                                "name": "Book ISBN",
                                "presentation": "ISBN",
                                "value": "326ccbc7-92d1-3aec-b3af-df8afdc5651d"
                            },
                            {
                                "id": 240,
                                "name": "Book pages",
                                "presentation": "Number of pages",
                                "value": "149"
                            }
                        ],
                        "created_at": "2014-12-03T09:54:35+0000",
                        "description": "Et eveniet voluptas ut magni vero temporibus nihil. Omnis possimus accusantium quia corporis culpa. Et recusandae asperiores qui architecto culpa autem sint accusantium. Officiis iusto accusantium perferendis aliquid ducimus.",
                        "id": 101,
                        "name": "Book \"Quidem\" by \"Marlen Yost\"",
                        "options": [],
                        "short_description": "Distinctio quos est eaque fugit totam repellendus.",
                        "updated_at": "2014-12-03T09:54:35+0000"
                    },
                    "options": [],
                    "sku": "326ccbc7-92d1-3aec-b3af-df8afdc5651d",
                    "updated_at": "2014-12-03T09:54:35+0000"
                }
            }
        ],
        "items_total": 1500000,
        "payments": [
            {
                "_links": {
                    "order": {
                        "href": "/app_dev.php/api/v1/orders/52"
                    },
                    "payment-method": {
                        "href": "/app_dev.php/api/v1/payment-methods/1"
                    },
                    "self": {
                        "href": "/app_dev.php/api/v1/payments/51"
                    }
                },
                "amount": 1504750,
                "created_at": "2014-12-15T15:02:54+0000",
                "id": 51,
                "method": {
                    "_links": {
                        "self": {
                            "href": "/app_dev.php/api/v1/payment-methods/1"
                        }
                    },
                    "created_at": "2014-12-03T09:54:28+0000",
                    "id": 1,
                    "name": "Dummy",
                    "updated_at": "2014-12-03T09:54:28+0000"
                },
                "state": "new",
                "updated_at": "2014-12-15T15:02:55+0000"
            }
        ],
        "shipments": [
            {
                "_links": {
                    "method": {
                        "href": "/app_dev.php/api/v1/shipping-methods/4"
                    },
                    "order": {
                        "href": "/app_dev.php/api/v1/orders/52"
                    },
                    "self": {
                        "href": "/app_dev.php/api/v1/shipments/51"
                    }
                },
                "created_at": "2014-12-15T14:30:40+0000",
                "id": 51,
                "method": {
                    "_links": {
                        "self": {
                            "href": "/app_dev.php/api/v1/shipping-methods/4"
                        },
                        "zone": {
                            "href": "/app_dev.php/api/v1/zones/4"
                        }
                    },
                    "calculator": "flexible_rate",
                    "category_requirement": 1,
                    "configuration": {
                        "additional_item_cost": 500,
                        "additional_item_limit": 10,
                        "first_item_cost": 4000
                    },
                    "created_at": "2014-12-03T09:54:28+0000",
                    "enabled": true,
                    "id": 4,
                    "name": "FedEx World Shipping",
                    "updated_at": "2014-12-03T09:54:28+0000"
                },
                "state": "onhold",
                "updated_at": "2014-12-15T14:30:41+0000"
            }
        ],
        "shipping_address": {
            "_links": {
                "country": {
                    "href": "/app_dev.php/api/v1/countries/9"
                }
            },
            "city": "New York",
            "created_at": "2014-12-15T13:37:28+0000",
            "first_name": "John",
            "id": 105,
            "last_name": "Doe",
            "postcode": "12435",
            "street": "Test",
            "updated_at": "2014-12-15T13:37:29+0000"
        },
        "total": 1504750,
        "state": "new",
        "number": "000000001",
        "checkout_completed_at": "2016-06-24T10:55:28+0200",
        "checkout_state": "completed",
    }

Purchase step
-------------

TODO.

.. code-block:: text

    PUT /api/v1/checkouts/44

Parameters
~~~~~~~~~~

type
    Card type
cardholderName
    Card holder name
number
    Card number
securityCode
    Card security code
expiryMonth
    Month expire number
expiryYear
    Year of card expiration

Response
~~~~~~~~

You can check the payment status in the payment lists on order response.

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {"to": "do"}

__ https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/ApiBundle/Controller/ShowAvailableShippingMethodsController.php