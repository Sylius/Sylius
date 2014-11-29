Orders API
==========

Sylius orders API endpoint is `/api/orders`.

Index of all orders
-------------------

You can retrieve the full list order by making the following request:

.. code-block:: text

    GET /api/orders

Parameters
~~~~~~~~~~

page
    Number of the page, by default = 1
limit
    Number of items to display per page

Response
~~~~~~~~

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "page":1,
        "limit":10,
        "pages":12,
        "total":120,
        "_links":{
            "self":{
                "href":"\/api\/orders\/?page=1"
            },
            "first":{
                "href":"\/api\/orders\/?page=1"
            },
            "last":{
                "href":"\/api\/orders\/?page=12"
            },
            "next":{
                "href":"\/api\/orders\/?page=2"
            }
        },
        "_embedded":{
            "items":[
                {
                    "id":301,
                    "completed_at":"2014-11-26T23:00:33+0000",
                    "number":"000000048",
                    "items":[
                        {
                            "id":1353,
                            "quantity":3,
                            "unit_price":9054,
                            "adjustments":[

                            ],
                            "adjustments_total":0,
                            "total":27162,
                            "immutable":false,
                            "variant":{
                                "id":13099,
                                "master":false,
                                "object":{
                                    "id":2107,
                                    "name":"T-Shirt \"voluptas\"",
                                    "description":"Non molestias voluptas quae nemo omnis totam. Impedit ad perferendis quaerat sint numquam voluptate eum. Facilis sed accusamus enim repellendus officiis rerum at.",
                                    "created_at":"2014-11-26T23:00:17+0000",
                                    "updated_at":"2014-11-26T23:00:17+0000",
                                    "masterVariant":{
                                        "id":13085,
                                        "master":true,
                                        "options":[

                                        ],
                                        "created_at":"2014-11-26T23:00:17+0000",
                                        "updated_at":"2014-11-26T23:00:17+0000",
                                        "available_on":"2014-08-27T08:51:04+0000",
                                        "sku":"43596"
                                    },
                                    "short_description":"Quos in dignissimos in fugit culpa vitae."
                                },
                                "created_at":"2014-11-26T23:00:17+0000",
                                "updated_at":"2014-11-26T23:00:34+0000",
                                "available_on":"2013-12-10T09:16:56+0000",
                                "sku":"8808"
                            },
                            "inventory_units":[
                            ],
                            "_links":{
                                "product":{
                                    "href":"\/api\/products\/2107"
                                },
                                "variant":{
                                    "href":"\/api\/products\/2107\/variants\/13099"
                                }
                            }
                        }
                    ],
                    "items_total":97783,
                    "adjustments":[
                    ],
                    "comments":[

                    ],
                    "adjustments_total":24240,
                    "total":122023,
                    "confirmed":true,
                    "created_at":"2014-04-30T10:41:14+0000",
                    "updated_at":"2014-11-26T23:00:34+0000",
                    "state":"pending",
                    "email":"ygrant@example.com",
                    "expires_at":"2014-11-27T02:00:33+0000",
                    "user":{
                        "id":476,
                        "username":"ygrant@example.com",
                        "username_canonical":"ygrant@example.com",
                        "email":"ygrant@example.com",
                        "email_canonical":"ygrant@example.com",
                        "enabled":false,
                        "groups":[

                        ],
                        "locked":false,
                        "expired":false,
                        "roles":[

                        ],
                        "credentials_expired":false
                    },
                    "channel":{
                        "id":91,
                        "code":"WEB-UK",
                        "name":"UK Webstore",
                        "type":"web",
                        "color":"Red",
                        "enabled":true,
                        "created_at":"2014-11-26T23:00:15+0000",
                        "updated_at":"2014-11-26T23:00:15+0000",
                    },
                    "shipping_address":{
                    },
                    "billing_address":{
                    },
                    "payments":[
                    ],
                    "shipments":[
                    ],
                    "currency":"GBP",
                    "checkout_state":"cart"
                }
            ]
        }
    }

Getting a single order
----------------------

You can view a single order by executing the following request:

.. code-block:: text

    GET /api/orders/24

Response
~~~~~~~~

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "id":301,
        "completed_at":"2014-11-26T23:00:33+0000",
        "number":"000000048",
        "items":[
            {
                "id":1353,
                "quantity":3,
                "unit_price":9054,
                "adjustments":[

                ],
                "adjustments_total":0,
                "total":27162,
                "immutable":false,
                "variant":{
                    "id":13099,
                    "master":false,
                    "object":{
                        "id":2107,
                        "name":"T-Shirt \"voluptas\"",
                        "description":"Non molestias voluptas quae nemo omnis totam. Impedit ad perferendis quaerat sint numquam voluptate eum. Facilis sed accusamus enim repellendus officiis rerum at.",
                        "created_at":"2014-11-26T23:00:17+0000",
                        "updated_at":"2014-11-26T23:00:17+0000",
                        "masterVariant":{
                            "id":13085,
                            "master":true,
                            "options":[

                            ],
                            "created_at":"2014-11-26T23:00:17+0000",
                            "updated_at":"2014-11-26T23:00:17+0000",
                            "available_on":"2014-08-27T08:51:04+0000",
                            "sku":"43596"
                        },
                        "short_description":"Quos in dignissimos in fugit culpa vitae."
                    },
                    "created_at":"2014-11-26T23:00:17+0000",
                    "updated_at":"2014-11-26T23:00:34+0000",
                    "available_on":"2013-12-10T09:16:56+0000",
                    "sku":"8808"
                },
                "inventory_units":[
                    {
                        "id":4061,
                        "inventory_state":"onhold",
                        "created_at":"2014-11-26T23:00:34+0000",
                        "updated_at":"2014-11-26T23:00:34+0000",
                        "_links":{
                            "order":{
                                "href":"\/app_dev.php\/api\/orders\/301"
                            }
                        }
                    },
                    {
                        "id":4062,
                        "inventory_state":"onhold",
                        "created_at":"2014-11-26T23:00:34+0000",
                        "updated_at":"2014-11-26T23:00:34+0000",
                        "_links":{
                            "order":{
                                "href":"\/app_dev.php\/api\/orders\/301"
                            }
                        }
                    },
                    {
                        "id":4063,
                        "inventory_state":"onhold",
                        "created_at":"2014-11-26T23:00:34+0000",
                        "updated_at":"2014-11-26T23:00:34+0000",
                        "_links":{
                            "order":{
                                "href":"\/app_dev.php\/api\/orders\/301"
                            }
                        }
                    }
                ],
                "_links":{
                    "product":{
                        "href":"\/app_dev.php\/api\/products\/2107"
                    },
                    "variant":{
                        "href":"\/app_dev.php\/api\/products\/2107\/variants\/13099"
                    }
                }
            }
        ],
        "items_total":97783,
        "adjustments":[
            {
                "id":1011,
                "label":"tax",
                "description":"EU VAT (23%)",
                "amount":22490,
                "neutral":false,
                "locked":false,
                "created_at":"2014-11-26T23:00:33+0000",
                "updated_at":"2014-11-26T23:00:34+0000"
            },
            {
                "id":1012,
                "label":"shipping",
                "description":"UPS Ground",
                "amount":2500,
                "neutral":false,
                "locked":false,
                "created_at":"2014-11-26T23:00:33+0000",
                "updated_at":"2014-11-26T23:00:34+0000"
            },
            {
                "id":1013,
                "label":"promotion",
                "description":"New Year Sale for 3 and more items.",
                "amount":-500,
                "neutral":false,
                "locked":false,
                "created_at":"2014-11-26T23:00:33+0000",
                "updated_at":"2014-11-26T23:00:34+0000"
            },
            {
                "id":1014,
                "label":"promotion",
                "description":"Christmas Sale for orders over 100 EUR.",
                "amount":-250,
                "neutral":false,
                "locked":false,
                "created_at":"2014-11-26T23:00:33+0000",
                "updated_at":"2014-11-26T23:00:34+0000"
            }
        ],
        "comments":[

        ],
        "adjustments_total":24240,
        "total":122023,
        "confirmed":true,
        "created_at":"2014-04-30T10:41:14+0000",
        "updated_at":"2014-11-26T23:00:34+0000",
        "state":"pending",
        "email":"ygrant@example.com",
        "expires_at":"2014-11-27T02:00:33+0000",
        "user":{
            "id":476,
            "username":"ygrant@example.com",
            "username_canonical":"ygrant@example.com",
            "email":"ygrant@example.com",
            "email_canonical":"ygrant@example.com",
            "enabled":false,
            "groups":[

            ],
            "locked":false,
            "expired":false,
            "roles":[

            ],
            "credentials_expired":false
        },
        "channel":{
            "id":91,
            "code":"WEB-UK",
            "name":"UK Webstore",
            "type":"web",
            "color":"Red",
            "enabled":true,
            "created_at":"2014-11-26T23:00:15+0000",
            "updated_at":"2014-11-26T23:00:15+0000",
        },
        "shipping_address":{
        },
        "billing_address":{
        },
        "payments":[
        ],
        "shipments":[
        ],
        "currency":"GBP",
        "checkout_state":"cart"
    }

Create an order
---------------

To create a new order (cart), you need to execute the following request:

.. code-block:: text

    POST /api/orders

Parameters
~~~~~~~~~~

channel
    The id of channel
user
    The id of customer
currency
    Currency code

Response
~~~~~~~~

The response will contain the newly created order information.

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "id":304,
        "items":[
        ],
        "items_total":0,
        "adjustments":[
        ],
        "comments":[

        ],
        "adjustments_total":0,
        "total":0,
        "confirmed":true,
        "created_at":"2014-11-29T12:29:07+0000",
        "updated_at":"2014-11-29T12:29:08+0000",
        "state":"cart",
        "email":"chelsie.witting@example.com",
        "expires_at":"2014-11-29T15:29:07+0000",
        "user":{
            "id":481,
            "username":"chelsie.witting@example.com",
            "username_canonical":"chelsie.witting@example.com",
            "email":"chelsie.witting@example.com",
            "email_canonical":"chelsie.witting@example.com",
            "enabled":true,
            "groups":[

            ],
            "locked":false,
            "expired":false,
            "roles":[

            ],
            "credentials_expired":false
        },
        "channel":{
            "id":91,
            "code":"WEB-UK",
            "name":"UK Webstore",
            "type":"web",
            "color":"Red",
            "enabled":true,
            "created_at":"2014-11-26T23:00:15+0000",
            "updated_at":"2014-11-26T23:00:15+0000",
        },
        "payments":[
        ],
        "shipments":[
        ],
        "currency":"USD",
        "checkout_state":"cart"
    }
