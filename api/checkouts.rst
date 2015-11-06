Checkouts API
=============

After you create a cart (an empty order) and add some items to it, you can start the checkout via API.
This basically means updating the order with concrete information, step by step, in a correct order.

Default Sylius checkout via API is constructed from the following steps:

addressing
    You enter customer shipping and billing address
shipping
    Shipments are proposed and you can select methods
payment
    Payments are calculated and methods proposed
finalize
    Final order is built and you can confirm it, cart will become an order
purchase
    You provide Sylius with payment information and order is paid

Sylius API endpoint is `/api/orders`.

Addressing step
---------------

After you added some items to the cart, to start the checkout you simply need to provide a shipping address. You can also specify a different billing address if needed.

You need to pass order id in the following url and make a PUT call:

.. code-block:: text

    PUT /api/checkouts/44

Parameters
~~~~~~~~~~

shippingAddress[firstName]
    Firstname for shipping address
shippingAddress[lastName]
    Lastname for shipping address
shippingAddress[city]
    City name
shippingAddress[postcode]
    Postcode
shippingAddress[street]
    Address line 1
shippingAddress[country]
    Id of the country
shippingAddress[province] *(optional)*
    Id of the province

If you do not specify the billing address block, shipping address will be used for that purpose.

billingAddress[firstName]
    Firstname for billing address
billingAddress[lastName]
    Lastname for billing address
billingAddress[city]
    City name
billingAddress[postcode]
    Postcode
billingAddress[street]
    Address line 1
billingAddress[country]
    Id of the country
billingAddress[province] *(optional)*
    Id of the province

Response
~~~~~~~~

The response will contain the updated order information.

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "adjustments": ,
        "adjustments_total": -250,
        "shipping_address": {
            "_links": {
                "country": {
                    "href": "/app_dev.php/api/countries/9"
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
        "billing_address": {
            "_links": {
                "country": {
                    "href": "/app_dev.php/api/countries/9"
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
                    "href": "/app_dev.php/api/channels/3"
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
        "checkout_state": "addressing",
        "comments": [],
        "confirmed": true,
        "created_at": "2014-12-15T13:15:22+0000",
        "currency": "USD",
        "email": "xschaefer@example.com",
        "expires_at": "2014-12-15T16:15:22+0000",
        "id": 52,
        "items": [],
        "items_total": 1500000,
        "payments": [],
        "shipments": [],
        "state": "cart",
        "total": 1499750,
        "updated_at": "2014-12-15T13:37:29+0000",
        "user": {
            "credentials_expired": false,
            "email": "xschaefer@example.com",
            "email_canonical": "xschaefer@example.com",
            "enabled": true,
            "expired": false,
            "groups": [],
            "id": 5,
            "locked": false,
            "roles": [],
            "username": "xschaefer@example.com",
            "username_canonical": "xschaefer@example.com"
        }
    }


Shipping step
-------------

When order contains the address information, we are able to determine the stock locations and available shipping methods.
You can get these informations by first calling a GET request on the checkout unique URL.

.. code-block:: text

    GET /api/checkouts/44

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    [
        {
            "methods": [
                {
                    "_links": {
                        "self": {
                            "href": "/app_dev.php/api/shipping-methods/4"
                        },
                        "zone": {
                            "href": "/app_dev.php/api/zones/4"
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
                }
            ],
            "shipment": {
                "_links": {
                    "order": {
                        "href": "/app_dev.php/api/orders/52"
                    }
                },
                "created_at": "2014-12-15T14:11:32+0000",
                "state": "checkout"
            }
        }
    ]

Response contains the proposed shipments and for each, it also has a list of shipping methods available.

Next step is updating the order with the types of shipping method that we have selected.
To do so, you need to call another PUT request, but this time with different set of parameters.

You need to pass an id of shipping method for every id, you should obtain them in the previous request.

.. code-block:: text

    PUT /api/checkouts/44

Parameters
~~~~~~~~~~

shipments[X][method]
    The id of the shipping method, where X is the shipment number

Response
~~~~~~~~

Response will contain an updated order information.

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "adjustments": {
        },
        "adjustments_total": 4750,
        "billing_address": {
        },
        "channel": {
        },
        "checkout_state": "shipping",
        "comments": [],
        "confirmed": true,
        "created_at": "2014-12-15T13:15:22+0000",
        "currency": "USD",
        "email": "xschaefer@example.com",
        "expires_at": "2014-12-15T16:15:22+0000",
        "id": 52,
        "items": [
        ],
        "items_total": 1500000,
        "payments": [],
        "shipments": [
            {
                "_links": {
                    "method": {
                        "href": "/app_dev.php/api/shipping-methods/4"
                    },
                    "order": {
                        "href": "/app_dev.php/api/orders/52"
                    },
                    "self": {
                        "href": "/app_dev.php/api/shipments/51"
                    }
                },
                "created_at": "2014-12-15T14:30:40+0000",
                "id": 51,
                "method": {
                    "_links": {
                        "self": {
                            "href": "/app_dev.php/api/shipping-methods/4"
                        },
                        "zone": {
                            "href": "/app_dev.php/api/zones/4"
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
        },
        "state": "cart",
        "total": 1504750,
        "updated_at": "2014-12-15T14:30:41+0000",
        "user": {
        }
    }


Payment step
------------

When we are done with shipping choices and we know the final price of an order, we can select a payment method.

To obtain a list of available payment methods for this order, simply call a GET request again:

.. code-block:: text

    GET /api/checkouts/44

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "methods": {
            "1": {
                "_links": {
                    "self": {
                        "href": "/app_dev.php/api/payment-methods/1"
                    }
                },
                "created_at": "2014-12-03T09:54:28+0000",
                "id": 1,
                "name": "Dummy",
                "updated_at": "2014-12-03T09:54:28+0000"
            },
            "2": {
                "_links": {
                    "self": {
                        "href": "/app_dev.php/api/payment-methods/2"
                    }
                },
                "created_at": "2014-12-03T09:54:28+0000",
                "id": 2,
                "name": "Paypal Express Checkout",
                "updated_at": "2014-12-03T09:54:28+0000"
            },
            "3": {
                "_links": {
                    "self": {
                        "href": "/app_dev.php/api/payment-methods/3"
                    }
                },
                "created_at": "2014-12-03T09:54:28+0000",
                "id": 3,
                "name": "Stripe",
                "updated_at": "2014-12-03T09:54:28+0000"
            },
            "4": {
                "_links": {
                    "self": {
                        "href": "/app_dev.php/api/payment-methods/4"
                    }
                },
                "created_at": "2014-12-03T09:54:28+0000",
                "id": 4,
                "name": "Be2bill",
                "updated_at": "2014-12-03T09:54:28+0000"
            },
            "5": {
                "_links": {
                    "self": {
                        "href": "/app_dev.php/api/payment-methods/5"
                    }
                },
                "created_at": "2014-12-03T09:54:28+0000",
                "id": 5,
                "name": "Stripe Checkout",
                "updated_at": "2014-12-03T09:54:28+0000"
            }
        },
        "payment": {
            "_links": {
                "order": {
                    "href": "/app_dev.php/api/orders/52"
                }
            },
            "amount": 1504750,
            "created_at": "2014-12-15T14:57:28+0000",
            "currency": "USD",
            "state": "new"
        }
    }


With that information, another PUT request with the id of payment method is enough to proceed:

.. code-block:: text

    PUT /api/checkouts/44

Parameters
~~~~~~~~~~

paymentMethod
    The id of the payment method you prefer

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
        "currency": "USD",
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
                        "href": "/app_dev.php/api/orders/52"
                    },
                    "payment-method": {
                        "href": "/app_dev.php/api/payment-methods/1"
                    },
                    "self": {
                        "href": "/app_dev.php/api/payments/51"
                    }
                },
                "amount": 1504750,
                "created_at": "2014-12-15T15:02:54+0000",
                "currency": "USD",
                "id": 51,
                "method": {
                    "_links": {
                        "self": {
                            "href": "/app_dev.php/api/payment-methods/1"
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

    GET /api/checkouts/44

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
                    "href": "/app_dev.php/api/countries/9"
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
                    "href": "/app_dev.php/api/channels/3"
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
        "checkout_state": "payment",
        "comments": [],
        "confirmed": true,
        "created_at": "2014-12-15T13:15:22+0000",
        "currency": "USD",
        "email": "xschaefer@example.com",
        "expires_at": "2014-12-15T16:15:22+0000",
        "id": 52,
        "items": [
            {
                "_links": {
                    "product": {
                        "href": "/app_dev.php/api/products/101"
                    },
                    "variant": {
                        "href": "/app_dev.php/api/products/101/variants/779"
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
                                "href": "/app_dev.php/api/orders/52"
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
                                "href": "/app_dev.php/api/orders/52"
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
                                "href": "/app_dev.php/api/orders/52"
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
                        "href": "/app_dev.php/api/orders/52"
                    },
                    "payment-method": {
                        "href": "/app_dev.php/api/payment-methods/1"
                    },
                    "self": {
                        "href": "/app_dev.php/api/payments/51"
                    }
                },
                "amount": 1504750,
                "created_at": "2014-12-15T15:02:54+0000",
                "currency": "USD",
                "id": 51,
                "method": {
                    "_links": {
                        "self": {
                            "href": "/app_dev.php/api/payment-methods/1"
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
                        "href": "/app_dev.php/api/shipping-methods/4"
                    },
                    "order": {
                        "href": "/app_dev.php/api/orders/52"
                    },
                    "self": {
                        "href": "/app_dev.php/api/shipments/51"
                    }
                },
                "created_at": "2014-12-15T14:30:40+0000",
                "id": 51,
                "method": {
                    "_links": {
                        "self": {
                            "href": "/app_dev.php/api/shipping-methods/4"
                        },
                        "zone": {
                            "href": "/app_dev.php/api/zones/4"
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
                    "href": "/app_dev.php/api/countries/9"
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
        "total": 1504750,
        "updated_at": "2014-12-15T15:02:55+0000",
        "user": {
            "credentials_expired": false,
            "email": "xschaefer@example.com",
            "email_canonical": "xschaefer@example.com",
            "enabled": true,
            "expired": false,
            "groups": [],
            "id": 5,
            "locked": false,
            "roles": [],
            "username": "xschaefer@example.com",
            "username_canonical": "xschaefer@example.com"
        }
    }

This is how your final order looks, if you are happy with that response, simply call another PUT to confirm the checkout, which will became a real order and appear in the backend.

.. code-block:: text

    PUT /api/checkouts/44

Response
~~~~~~~~

Final response contains the full order information, now you can call the purchase action to actually pay for the order.

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {"to": "do"}

Purchase step
-------------

TODO.

.. code-block:: text

    PUT /api/checkouts/44

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
