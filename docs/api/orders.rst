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
| items_total           | Sum of all items prices                                            |
+-----------------------+--------------------------------------------------------------------+
| adjustments           | List of adjustments related to the order                           |
+-----------------------+--------------------------------------------------------------------+
| adjustments_total     | Sum of all order adjustments                                       |
+-----------------------+--------------------------------------------------------------------+
| total                 | Sum of items total and adjustments total                           |
+-----------------------+--------------------------------------------------------------------+
| customer              | :doc:`Customer detailed serialization </api/customers>` for order  |
+-----------------------+--------------------------------------------------------------------+
| channel               | :doc:`Default channel serialization </api/channels>`               |
+-----------------------+--------------------------------------------------------------------+
| currency_code         | Currency of the order                                              |
+-----------------------+--------------------------------------------------------------------+
| checkout_state        | :doc:`State of the checkout process </book/orders/checkout>`       |
+-----------------------+--------------------------------------------------------------------+
| state                 | :doc:`State of the order </components/Order/state_machine>`        |
+-----------------------+--------------------------------------------------------------------+
| checkout_completed_at | Date when the checkout has been completed                          |
+-----------------------+--------------------------------------------------------------------+
| number                | Serial number of the order                                         |
+-----------------------+--------------------------------------------------------------------+
| shipping_address      | Detailed address serialization                                     |
+-----------------------+--------------------------------------------------------------------+
| billing_address       | Detailed address serialization                                     |
+-----------------------+--------------------------------------------------------------------+
| shipments             | Detailed serialization of all related shipments                    |
+-----------------------+--------------------------------------------------------------------+
| payments              | Detailed serialization of all related payments                     |
+-----------------------+--------------------------------------------------------------------+

Getting a single order
----------------------

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
| id            | url attribute  | Id of requested order                |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/orders/21 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

.. note::

    The value *21* was taken from previous responses, where we managed the cart.
    Your value can be different. If you need more information about Cart API please, check :doc:`this article </api/carts>`.

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "id":21,
        "checkout_completed_at":"2017-01-27T16:15:08+0100",
        "number":"000000020",
        "items":[
            {
                "id":54,
                "quantity":3,
                "unit_price":70,
                "total":198,
                "units":[
                    {
                        "id":166,
                        "adjustments":[
                            {
                                "id":139,
                                "type":"order_promotion",
                                "label":"Christmas",
                                "amount":-4
                            }
                        ],
                        "adjustments_total":-4,
                        "_links":{
                            "order":{
                                "href":"\/api\/v1\/orders\/21"
                            }
                        }
                    },
                    {
                        "id":167,
                        "adjustments":[
                            {
                                "id":140,
                                "type":"order_promotion",
                                "label":"Christmas",
                                "amount":-4
                            }
                        ],
                        "adjustments_total":-4,
                        "_links":{
                            "order":{
                                "href":"\/api\/v1\/orders\/21"
                            }
                        }
                    },
                    {
                        "id":168,
                        "adjustments":[
                            {
                                "id":141,
                                "type":"order_promotion",
                                "label":"Christmas",
                                "amount":-4
                            }
                        ],
                        "adjustments_total":-4,
                        "_links":{
                            "order":{
                                "href":"\/api\/v1\/orders\/21"
                            }
                        }
                    }
                ],
                "units_total":198,
                "adjustments":[

                ],
                "adjustments_total":0,
                "variant":{
                    "id":37,
                    "on_hold":0,
                    "tracked":false
                },
                "_links":{
                    "product":{
                        "href":"\/api\/v1\/products\/13"
                    },
                    "variant":{
                        "href":"\/api\/v1\/products\/13\/variants\/37"
                    }
                }
            },
            {
                "id":55,
                "quantity":1,
                "unit_price":818,
                "total":769,
                "units":[
                    {
                        "id":169,
                        "adjustments":[
                            {
                                "id":142,
                                "type":"order_promotion",
                                "label":"Christmas",
                                "amount":-49
                            }
                        ],
                        "adjustments_total":-49,
                        "_links":{
                            "order":{
                                "href":"\/api\/v1\/orders\/21"
                            }
                        }
                    }
                ],
                "units_total":769,
                "adjustments":[

                ],
                "adjustments_total":0,
                "variant":{
                    "id":289,
                    "on_hold":0,
                    "tracked":false
                },
                "_links":{
                    "product":{
                        "href":"\/api\/v1\/products\/58"
                    },
                    "variant":{
                        "href":"\/api\/v1\/products\/58\/variants\/289"
                    }
                }
            },
            {
                "id":56,
                "quantity":2,
                "unit_price":338,
                "total":635,
                "units":[
                    {
                        "id":170,
                        "adjustments":[
                            {
                                "id":143,
                                "type":"order_promotion",
                                "label":"Christmas",
                                "amount":-21
                            }
                        ],
                        "adjustments_total":-21,
                        "_links":{
                            "order":{
                                "href":"\/api\/v1\/orders\/21"
                            }
                        }
                    },
                    {
                        "id":171,
                        "adjustments":[
                            {
                                "id":144,
                                "type":"order_promotion",
                                "label":"Christmas",
                                "amount":-20
                            }
                        ],
                        "adjustments_total":-20,
                        "_links":{
                            "order":{
                                "href":"\/api\/v1\/orders\/21"
                            }
                        }
                    }
                ],
                "units_total":635,
                "adjustments":[

                ],
                "adjustments_total":0,
                "variant":{
                    "id":12,
                    "on_hold":0,
                    "tracked":false
                },
                "_links":{
                    "product":{
                        "href":"\/api\/v1\/products\/4"
                    },
                    "variant":{
                        "href":"\/api\/v1\/products\/4\/variants\/12"
                    }
                }
            },
            {
                "id":57,
                "quantity":3,
                "unit_price":520,
                "total":1466,
                "units":[
                    {
                        "id":172,
                        "adjustments":[
                            {
                                "id":145,
                                "type":"order_promotion",
                                "label":"Christmas",
                                "amount":-32
                            }
                        ],
                        "adjustments_total":-32,
                        "_links":{
                            "order":{
                                "href":"\/api\/v1\/orders\/21"
                            }
                        }
                    },
                    {
                        "id":173,
                        "adjustments":[
                            {
                                "id":146,
                                "type":"order_promotion",
                                "label":"Christmas",
                                "amount":-31
                            }
                        ],
                        "adjustments_total":-31,
                        "_links":{
                            "order":{
                                "href":"\/api\/v1\/orders\/21"
                            }
                        }
                    },
                    {
                        "id":174,
                        "adjustments":[
                            {
                                "id":147,
                                "type":"order_promotion",
                                "label":"Christmas",
                                "amount":-31
                            }
                        ],
                        "adjustments_total":-31,
                        "_links":{
                            "order":{
                                "href":"\/api\/v1\/orders\/21"
                            }
                        }
                    }
                ],
                "units_total":1466,
                "adjustments":[

                ],
                "adjustments_total":0,
                "variant":{
                    "id":56,
                    "on_hold":0,
                    "tracked":false
                },
                "_links":{
                    "product":{
                        "href":"\/api\/v1\/products\/19"
                    },
                    "variant":{
                        "href":"\/api\/v1\/products\/19\/variants\/56"
                    }
                }
            }
        ],
        "items_total":3068,
        "adjustments":[
            {
                "id":148,
                "type":"shipping",
                "label":"DHL Express",
                "amount":2160
            }
        ],
        "adjustments_total":2160,
        "total":5228,
        "state":"new",
        "customer":{
            "id":8,
            "email":"eturner@senger.com",
            "email_canonical":"eturner@senger.com",
            "first_name":"Ricky",
            "last_name":"Swift",
            "gender":"u",
            "user":{
                "id":8,
                "username":"eturner@senger.com",
                "username_canonical":"eturner@senger.com",
                "roles":[
                    "ROLE_USER"
                ],
                "enabled":true
            },
            "_links":{
                "self":{
                    "href":"\/api\/v1\/customers\/8"
                }
            }
        },
        "channel":{
            "id":1,
            "code":"US_WEB",
            "name":"US Web Store",
            "hostname":"localhost:8000",
            "color":"MediumSpringGreen",
            "created_at":"2017-01-27T16:15:02+0100",
            "updated_at":"2017-01-27T16:15:02+0100",
            "enabled":true,
            "tax_calculation_strategy":"order_items_based",
            "_links":{
                "self":{
                    "href":"\/api\/v1\/channels\/1"
                }
            }
        },
        "shipping_address":{
            "id":58,
            "first_name":"Mittie",
            "last_name":"Schoen",
            "country_code":"US",
            "street":"Gutmann Parkways",
            "city":"West Devonte",
            "postcode":"68192-0107",
            "created_at":"2017-01-27T16:15:08+0100",
            "updated_at":"2017-01-27T16:15:08+0100"
        },
        "billing_address":{
            "id":59,
            "first_name":"Mittie",
            "last_name":"Schoen",
            "country_code":"US",
            "street":"Gutmann Parkways",
            "city":"West Devonte",
            "postcode":"68192-0107",
            "created_at":"2017-01-27T16:15:08+0100",
            "updated_at":"2017-01-27T16:15:08+0100"
        },
        "payments":[
            {
                "id":20,
                "method":{
                    "id":1,
                    "code":"cash_on_delivery",
                    "created_at":"2017-01-27T16:15:02+0100",
                    "updated_at":"2017-01-27T16:15:02+0100",
                    "channels":[
                        {
                            "id":1,
                            "code":"US_WEB",
                            "name":"US Web Store",
                            "hostname":"localhost:8000",
                            "color":"MediumSpringGreen",
                            "created_at":"2017-01-27T16:15:02+0100",
                            "updated_at":"2017-01-27T16:15:02+0100",
                            "enabled":true,
                            "tax_calculation_strategy":"order_items_based",
                            "_links":{
                                "self":{
                                    "href":"\/api\/v1\/channels\/1"
                                }
                            }
                        }
                    ],
                    "_links":{
                        "self":{
                            "href":"\/api\/v1\/payment-methods\/1"
                        }
                    }
                },
                "amount":5228,
                "state":"new",
                "created_at":"2017-01-27T16:15:08+0100",
                "updated_at":"2017-01-27T16:15:08+0100",
                "_links":{
                    "self":{
                        "href":"\/api\/v1\/payments\/20"
                    },
                    "payment-method":{
                        "href":"\/api\/v1\/payment-methods\/1"
                    },
                    "order":{
                        "href":"\/api\/v1\/orders\/21"
                    }
                }
            }
        ],
        "shipments":[
            {
                "id":20,
                "state":"ready",
                "method":{
                    "id":2,
                    "code":"dhl_express",
                    "category_requirement":1,
                    "calculator":"flat_rate",
                    "configuration":{
                        "US_WEB":{
                            "amount":2160
                        }
                    },
                    "created_at":"2017-01-27T16:15:02+0100",
                    "updated_at":"2017-01-27T16:15:02+0100",
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
                "created_at":"2017-01-27T16:15:08+0100",
                "updated_at":"2017-01-27T16:15:08+0100",
                "_links":{
                    "self":{
                        "href":"\/api\/v1\/shipments\/20"
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
        "currency_code":"USD",
        "checkout_state":"completed"
    }
