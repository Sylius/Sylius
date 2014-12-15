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

    {"to": "do"}

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

    {"to": "do"}

Payment step
------------

When we are done with shipping choices and we know the final price of an order, we can select a payment method.

To obtain a list of available payment methods for this order, simply call a GET request again:

.. code-block:: text

    GET /api/checkouts/44

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {"to": "do"}

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

    {"to": "do"}

Finalize step
-------------

Now your order is fully constructed, you can get its latest snapshot by calling your last GET request:

.. code-block:: text

    GET /api/checkouts/44

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {"to": "do"}

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
