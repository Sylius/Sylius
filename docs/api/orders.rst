Orders API
==========

Sylius orders API endpoint is `/api/v1/orders`.

If you request an order via API, you will receive an object with the following fields:

+-----------------------+--------------------------------------------------------------------+
| Field                 | Description                                                        |
+=======================+====================================================================+
| id                    | Id of the order                                                    |
+-----------------------+--------------------------------------------------------------------+
| items                 | List of items related to the order                                 |
+-----------------------+--------------------------------------------------------------------+
| itemsTotal            | Sum of all items prices                                            |
+-----------------------+--------------------------------------------------------------------+
| adjustments           | List of adjustments related to the order                           |
+-----------------------+--------------------------------------------------------------------+
| adjustmentsTotal      | Sum of all order adjustments                                       |
+-----------------------+--------------------------------------------------------------------+
| total                 | Sum of items total and adjustments total                           |
+-----------------------+--------------------------------------------------------------------+
| customer              | :doc:`Customer detailed serialization </api/customers>` for order  |
+-----------------------+--------------------------------------------------------------------+
| channel               | :doc:`Default channel serialization </api/channels>`               |
+-----------------------+--------------------------------------------------------------------+
| currencyCode          | Currency of the order                                              |
+-----------------------+--------------------------------------------------------------------+
| checkoutState         | :doc:`State of the checkout process </book/orders/checkout>`       |
+-----------------------+--------------------------------------------------------------------+
| state                 | :doc:`State of the order </components/Order/state_machine>`        |
+-----------------------+--------------------------------------------------------------------+
| checkoutCompletedAt   | Date when the checkout has been completed                          |
+-----------------------+--------------------------------------------------------------------+
| number                | Serial number of the order                                         |
+-----------------------+--------------------------------------------------------------------+
| shippingAddress       | Detailed address serialization                                     |
+-----------------------+--------------------------------------------------------------------+
| billingAddress        | Detailed address serialization                                     |
+-----------------------+--------------------------------------------------------------------+
| shipments             | Detailed serialization of all related shipments                    |
+-----------------------+--------------------------------------------------------------------+
| payments              | Detailed serialization of all related payments                     |
+-----------------------+--------------------------------------------------------------------+

Orders endpoint gives an access point to finalized carts, so to the orders that have been placed. At this moment only certain actions are allowed:

+------------------------+------------------------------+
| Action                 | Description                  |
+========================+==============================+
| Show                   | Presenting of the order      |
+------------------------+------------------------------+
| Cancelling             | Cancelling of the order      |
+------------------------+------------------------------+
| Shipping               | Shipping of the order        |
+------------------------+------------------------------+
| Completing the payment | Complete the order's payment |
+------------------------+------------------------------+

Show Action
-----------

You can request detailed order information by executing the following request:

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/orders/{id}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| id            | url attribute  | Id of the requested order            |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/orders/21 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

.. note::

    The value *21* was taken from previous responses, where we managed the cart and proceed the checkout.
    Your value can be different. If you need more information about Cart API please, check :doc:`this article </api/carts>`.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "id":21,
        "checkoutCompletedAt":"2017-02-15T13:31:33+0100",
        "number":"000000021",
        "items":[
            {
                "id":74,
                "quantity":1,
                "unitPrice":100000,
                "total":100000,
                "units":[
                    {
                        "id":228,
                        "adjustments":[

                        ],
                        "adjustmentsTotal":0,
                        "_links":{
                            "order":{
                                "href":"\/api\/v1\/orders\/21"
                            }
                        }
                    }
                ],
                "unitsTotal":100000,
                "adjustments":[

                ],
                "adjustmentsTotal":0,
                "variant":{
                    "id":331,
                    "code":"MEDIUM_MUG_CUP",
                    "optionValues":[
                        {
                            "name":"Mug type",
                            "code":"mug_type_medium"
                        }
                    ],
                    "position":2,
                    "translations":{
                        "en_US":{
                            "locale":"en_US",
                            "id":331,
                            "name":"Medium Mug"
                        }
                    },
                    "version": 1,
                    "onHold":0,
                    "onHand":10,
                    "tracked":false,
                    "channelPricings":{
                        "US_WEB":{
                            "channelCode":"US_WEB",
                            "price":100000
                        }
                    },
                    "_links":{
                        "self":{
                            "href":"\/api\/v1\/products\/5\/variants\/331"
                        },
                        "product":{
                            "href":"\/api\/v1\/products\/5"
                        }
                    }
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
        "itemsTotal":100000,
        "adjustments":[
            {
                "id":252,
                "type":"shipping",
                "label":"DHL Express",
                "amount":3549
            }
        ],
        "adjustmentsTotal":3549,
        "total":103549,
        "state":"new",
        "customer":{
            "id":1,
            "email":"shop@example.com",
            "emailCanonical":"shop@example.com",
            "firstName":"John",
            "lastName":"Doe",
            "gender":"u",
            "user":{
                "id":1,
                "username":"shop@example.com",
                "usernameCanonical":"shop@example.com",
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
            "createdAt":"2017-02-14T11:10:02+0100",
            "updatedAt":"2017-02-14T11:10:02+0100",
            "enabled":true,
            "taxCalculationStrategy":"order_items_based",
            "_links":{
                "self":{
                    "href":"\/api\/v1\/channels\/1"
                }
            }
        },
        "shippingAddress":{
            "id":71,
            "firstName":"Frederick D.",
            "lastName":"Gregory",
            "countryCode":"US",
            "street":"300 E St SW",
            "city":"\u2019Washington",
            "postcode":"DC 20546",
            "createdAt":"2017-02-14T11:55:40+0100",
            "updatedAt":"2017-02-14T17:00:17+0100"
        },
        "billingAddress":{
            "id":72,
            "firstName":"Frederick D.",
            "lastName":"Gregory",
            "countryCode":"US",
            "street":"300 E St SW",
            "city":"\u2019Washington",
            "postcode":"DC 20546",
            "createdAt":"2017-02-14T11:55:40+0100",
            "updatedAt":"2017-02-14T17:00:17+0100"
        },
        "payments":[
            {
                "id":21,
                "method":{
                    "id":2,
                    "code":"bank_transfer",
                    "createdAt":"2017-02-14T11:10:02+0100",
                    "updatedAt":"2017-02-14T11:10:02+0100",
                    "channels":[
                        {
                            "id":1,
                            "code":"US_WEB",
                            "name":"US Web Store",
                            "hostname":"localhost",
                            "color":"MediumPurple",
                            "createdAt":"2017-02-14T11:10:02+0100",
                            "updatedAt":"2017-02-14T11:10:02+0100",
                            "enabled":true,
                            "taxCalculationStrategy":"order_items_based",
                            "_links":{
                                "self":{
                                    "href":"\/api\/v1\/channels\/1"
                                }
                            }
                        }
                    ],
                    "_links":{
                        "self":{
                            "href":"\/api\/v1\/payment-methods\/bank_transfer"
                        }
                    }
                },
                "amount":103549,
                "state":"new",
                "createdAt":"2017-02-14T11:53:41+0100",
                "updatedAt":"2017-02-15T13:31:33+0100",
                "_links":{
                    "self":{
                        "href":"\/api\/v1\/payments\/21"
                    },
                    "payment-method":{
                        "href":"\/api\/v1\/payment-methods\/bank_transfer"
                    },
                    "order":{
                        "href":"\/api\/v1\/orders\/21"
                    }
                }
            }
        ],
        "shipments":[
            {
                "id":21,
                "state":"ready",
                "method":{
                    "id":2,
                    "code":"dhl_express",
                    "category_requirement":1,
                    "calculator":"flat_rate",
                    "configuration":{
                        "US_WEB":{
                            "amount":3549
                        }
                    },
                    "createdAt":"2017-02-14T11:10:02+0100",
                    "updatedAt":"2017-02-14T11:10:02+0100",
                    "enabled":true,
                    "_links":{
                        "self":{
                            "href":"\/api\/v1\/shipping-methods\/dhl_express"
                        },
                        "zone":{
                            "href":"\/api\/v1\/zones\/US"
                        }
                    }
                },
                "createdAt":"2017-02-14T11:53:41+0100",
                "updatedAt":"2017-02-15T13:31:33+0100",
                "_links":{
                    "self":{
                        "href":"\/api\/v1\/shipments\/21"
                    },
                    "method":{
                        "href":"\/api\/v1\/shipping-methods\/dhl_express"
                    },
                    "order":{
                        "href":"\/api\/v1\/orders\/21"
                    }
                }
            }
        ],
        "currencyCode":"USD",
        "localeCode":"en_US",
        "checkoutState":"completed"
    }

Cancel Action
-------------

You can cancel an already placed order by executing the following request:

Definition
^^^^^^^^^^

.. code-block:: text

    PUT /api/v1/orders/{id}/cancel

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| id            | url attribute  | Id of the requested order            |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/orders/21/cancel \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X PUT

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 NO CONTENT

Ship Action
-----------

You can ship an already placed order by executing the following request:

Definition
^^^^^^^^^^

.. code-block:: text

    PUT /api/v1/orders/{orderId}/shipments/{id}/ship

+---------------+----------------+------------------------------------------------+
| Parameter     | Parameter type | Description                                    |
+===============+================+================================================+
| Authorization | header         | Token received during authentication           |
+---------------+----------------+------------------------------------------------+
| orderId       | url attribute  | Id of the requested order                      |
+---------------+----------------+------------------------------------------------+
| id            | url attribute  | Id of the shipped shipment                     |
+---------------+----------------+------------------------------------------------+
| tracking      | request        | *(optional)* The tracking code of the shipment |
+---------------+----------------+------------------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/orders/21/shipments/21/ship \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X PUT

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

.. note::

    It is important to emphasise that in this example the shipment id is the same value as for the order, but it is a coincidence rather than a rule.

Complete The Payment Action
---------------------------

You can complete the payment of an already placed order by executing the following request:

Definition
^^^^^^^^^^

.. code-block:: text

    PUT /api/v1/orders/{orderId}/payments/{id}/complete

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| orderId       | url attribute  | Id of the requested order            |
+---------------+----------------+--------------------------------------+
| id            | url attribute  | Id of payment to complete            |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/orders/21/payments/21/complete \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X PUT

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No content
