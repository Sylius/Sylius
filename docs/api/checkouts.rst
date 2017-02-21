Checkout API
============

These endpoints will allow you to go through the order checkout from the admin perspective. It can be useful for integrations with tools like `Twillo <https://www.twilio.com/docs/>`_ or an inspiration for your custom Shop API.
Base URI is `/api/v1/checkouts/`.

After you create a cart (an empty order) and add some items to it, you can start the checkout via API.
This basically means updating the order with concrete information, step by step, in a correct order.

Sylius checkout flow is built from 4 steps, which have to be done in a certain order (unless you will customize it).

+------------+---------------------------------------------------------+
| Step       | Description                                             |
+============+=========================================================+
| addressing | Shipping and billing addresses are assigned to the cart |
+------------+---------------------------------------------------------+
| shipping   | Choosing a shipping method from the available ones      |
+------------+---------------------------------------------------------+
| payment    | Choosing a payment method from the available ones       |
+------------+---------------------------------------------------------+
| finalize   | The order is built and its data can be confirmed        |
+------------+---------------------------------------------------------+

.. tip::

    If you are not familiar with the concept of checkout in Sylius, please carefully read :doc:`this article </book/orders/checkout>` first.

.. note::

    We do not present the order serialization in this chapter, because it is the same order serialization as described in :doc:`the article about orders </api/orders>`.

Addressing step
---------------

After you added some items to the cart, to start the checkout you simply need to provide a shipping address. You can also specify a different billing address if needed.

Definition
^^^^^^^^^^

.. code-block:: text

    PUT /api/v1/checkouts/addressing/{id}

+------------------------------+----------------+-----------------------------------------------------------------------------------------------------+
| Parameter                    | Parameter type | Description                                                                                         |
+==============================+================+=====================================================================================================+
| Authorization                | header         | Token received during authentication                                                                |
+------------------------------+----------------+-----------------------------------------------------------------------------------------------------+
| id                           | url attribute  | Id of the requested cart                                                                            |
+------------------------------+----------------+-----------------------------------------------------------------------------------------------------+
| different_billing_address    | request        | If false, the billing address fields are not required and data from the shipping address is copied  |
+------------------------------+----------------+-----------------------------------------------------------------------------------------------------+
| shipping_address[first_name] | request        | First name for the shipping address                                                                 |
+------------------------------+----------------+-----------------------------------------------------------------------------------------------------+
| shipping_address[last_name]  | request        | Last name for the shipping address                                                                  |
+------------------------------+----------------+-----------------------------------------------------------------------------------------------------+
| shipping_address[city]       | request        | City name                                                                                           |
+------------------------------+----------------+-----------------------------------------------------------------------------------------------------+
| shipping_address[postcode]   | request        | Postcode                                                                                            |
+------------------------------+----------------+-----------------------------------------------------------------------------------------------------+
| shipping_address[street]     | request        | Street                                                                                              |
+------------------------------+----------------+-----------------------------------------------------------------------------------------------------+
| shipping_address[country]    | request        | Id of the country                                                                                   |
+------------------------------+----------------+-----------------------------------------------------------------------------------------------------+
| shipping_address[province]   | request        | *(optional)* Id of the province                                                                     |
+------------------------------+----------------+-----------------------------------------------------------------------------------------------------+
| billing_address[first_name]  | request        | *(optional)* First name for the billing address                                                     |
+------------------------------+----------------+-----------------------------------------------------------------------------------------------------+
| billing_address[last_name]   | request        | *(optional)* Last name for the billing address                                                      |
+------------------------------+----------------+-----------------------------------------------------------------------------------------------------+
| billing_address[city]        | request        | *(optional)* City name                                                                              |
+------------------------------+----------------+-----------------------------------------------------------------------------------------------------+
| billing_address[postcode]    | request        | *(optional)* Postcode                                                                               |
+------------------------------+----------------+-----------------------------------------------------------------------------------------------------+
| billing_address[street]      | request        | *(optional)* Street                                                                                 |
+------------------------------+----------------+-----------------------------------------------------------------------------------------------------+
| billing_address[country]     | request        | *(optional)* Id of the country                                                                      |
+------------------------------+----------------+-----------------------------------------------------------------------------------------------------+
| billing_address[province]    | request        | *(optional)* Id of the province                                                                     |
+------------------------------+----------------+-----------------------------------------------------------------------------------------------------+

.. note::

    Remember a cart with `id = 21` :doc:`for the Cart API documentation </api/carts>`? We will take the same cart as an exemplary cart for checkout process.

Example
^^^^^^^

To address the cart for a user that lives in ``Los Angeles`` in the United States, the following snippet can be used:

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

Now you can check the state of the order, by asking for the checkout summary:

Example
^^^^^^^

To check the checkout process state for the cart with `id = 21`, we need to execute this command:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/checkouts/21 \
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

Of course, you can specify different shipping and billing addresses. If our user Elon would like to send a gift to the NASA administrator, Frederick D. Gregory, he could send the following request:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/checkouts/addressing/21 \
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
                "different_billing_address": true,
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

Shipping step
-------------

When the order contains the address information, we are able to determine the available shipping methods.
First, we need to get the available shipping methods to have our choice list:

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

To check available shipping methods for the previously addressed cart, you can use the following command:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/checkouts/select-shipping/21 \
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

The response contains proposed shipments and for each of them, it has a list of the available shipping methods alongside their calculated prices.

.. warning::

    Because of the custom calculation logic, the regular rules of overriding do not apply for this endpoint.
    In order to have a different response, you have to provide a custom controller and build the message on your own.
    Exemplary implementation can be found `here <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/ApiBundle/Controller/ShowAvailableShippingMethodsController.php>`__

Next step is updating the order with the types of shipping methods that have been selected. A PUT request has to be send for each available shipment.

Definition
^^^^^^^^^^

.. code-block:: text

    PUT /api/v1/checkouts/select-shipping/{id}

+------------------------+----------------+----------------------------------------------------------------------------------------------+
| Parameter              | Parameter type | Description                                                                                  |
+========================+================+==============================================================================================+
| Authorization          | header         | Token received during authentication                                                         |
+------------------------+----------------+----------------------------------------------------------------------------------------------+
| id                     | url attribute  | Id of the requested cart                                                                     |
+------------------------+----------------+----------------------------------------------------------------------------------------------+
| shipments[X]['method'] | request        | Code of the chosen shipping method (Where X is the number of shipment in the returned array) |
+------------------------+----------------+----------------------------------------------------------------------------------------------+

Example
^^^^^^^

To choose the `DHL Express` method for our shipment (the cheapest one), we can use the following snippet:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/checkouts/select-shipping/21 \
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

While checking for the checkout process state of the cart with `id = 21`, you will get the following response:

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

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/checkouts/select-payment/{id}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| id            | url attribute  | Id of the requested cart             |
+---------------+----------------+--------------------------------------+

.. warning::

    Similar to the shipping step, this one has its own controller, which has to be replaced if you want to make some changes. Exemplary implementation can be found `here <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/ApiBundle/Controller/ShowAvailablePaymentMethodsController.php>`__

Example
^^^^^^^

To check available payment methods for the cart that has a shipping methods assigned, we need to execute this curl command:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/checkouts/select-payment/21 \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json"

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "payments":[
            {
                "methods":[
                    {
                        "id":1,
                        "code":"cash_on_delivery",
                        "name":"Cash on delivery",
                        "description":"Ipsum dolor non esse quia sit."
                    },
                    {
                        "id":2,
                        "code":"bank_transfer",
                        "name":"Bank transfer",
                        "description":"Perspiciatis itaque earum quisquam ut dolor."
                    }
                ]
            }
        ]
    }


With that information, another ``PUT`` request with the id of payment method is enough to proceed:

Definition
^^^^^^^^^^

.. code-block:: text

    PUT /api/v1/checkouts/select-payment/{id}

+----------------------+----------------+--------------------------------------+
| Parameter            | Parameter type | Description                          |
+======================+================+======================================+
| Authorization        | header         | Token received during authentication |
+----------------------+----------------+--------------------------------------+
| id                   | url attribute  | Id of the requested cart             |
+----------------------+----------------+--------------------------------------+
| payment[X]['method'] | request        | Code of chosen payment method        |
+----------------------+----------------+--------------------------------------+

Example
^^^^^^^

To choose the ``Bank transfer`` method for our shipment, simply use the following code:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/checkouts/select-payment/21 \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PUT \
        --data '
            {
                "payments": [
                    {
                        "method": "bank_transfer"
                    }
                ]
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

Finalize step
-------------

After choosing the payment method we are ready to finalize the cart and make an order. Now, you can get its snapshot by calling a ``GET`` request:

.. tip::

    The same definition has been used over this chapter, to see the current state of the gorder.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/checkouts/{id}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| id            | url attribute  | Id of the requested cart             |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To check the fully constructed cart with `id = 21`, use the following command:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/checkouts/21 \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json"

.. code-block:: text

    STATUS: 200 OK

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
                "id":252,
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
                    "id":2,
                    "code":"bank_transfer"
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
        "checkout_state":"payment_selected"
    }

This is how your final order will look like. If you are satisfied with that response, simply call another ``PUT`` request to confirm the checkout, which will become a real order and appear in the backend.

Definition
^^^^^^^^^^

.. code-block:: text

    PUT /api/v1/checkouts/complete/{id}

+---------------+----------------+---------------------------------------------------------+
| Parameter     | Parameter type | Description                                             |
+===============+================+=========================================================+
| Authorization | header         | Token received during authentication                    |
+---------------+----------------+---------------------------------------------------------+
| id            | url attribute  | Id of the requested cart                                |
+---------------+----------------+---------------------------------------------------------+
| notes         | request        | *(optional)* Notes that should be attached to the order |
+---------------+----------------+---------------------------------------------------------+

Example
^^^^^^^

To finalize the previously built order, execute the following command:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/checkouts/complete/21 \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PUT

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

The order has been placed, from now on you can manage it only via orders endpoint.

Of course the same result can be achieved when the order will be completed with some additional notes:

Example
^^^^^^^

To finalize  the previously built order (assuming that, the previous example has not been executed), try the following command:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/checkouts/complete/21 \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PUT \
        --data '
            {
                "notes": "Please, call me before delivery"
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content
