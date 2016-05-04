Channels API
============

Sylius channels API endpoint is ``/api/channels``.

Index of all channels
---------------------

To browse all channels available in the Sylius e-commerce platform you can call the following GET request:

.. code-block:: text

    GET /api/channels/

Parameters
~~~~~~~~~~

page
    Number of the page, by default = 1
limit
    Number of items to display per page

Response
~~~~~~~~

Response will contain a paginated list of channels.

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "page":1,
        "limit":10,
        "pages":1,
        "total":3,
        "_links":{
            "self":{
                "href":"\/api\/channels\/?page=1"
            },
            "first":{
                "href":"\/api\/channels\/?page=1"
            },
            "last":{
                "href":"\/api\/channels\/?page=12"
            },
            "next":{
                "href":"\/api\/channels\/?page=2"
            }
        },
        "_embedded":{
            "items":[
                {
                    "code": "WEB-UK",
                    "color": "Red",
                    "created_at": "2014-11-26T23:00:15+0000",
                    "currencies": [
                    ],
                    "enabled": true,
                    "id": 91,
                    "locales": [
                    ],
                    "name": "UK Webstore",
                    "payment_methods": [
                    ],
                    "shipping_methods": [
                    ],
                    "type": "web",
                    "updated_at": "2014-11-26T23:00:15+0000"
                }
            ]
        }
    }

Getting a single channel
------------------------

You can view a single channel by executing the following request:

.. code-block:: text

    GET /api/channels/91

Response
~~~~~~~~

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "code": "WEB-UK",
        "color": "Red",
        "created_at": "2014-11-26T23:00:15+0000",
        "currencies": [
        ],
        "enabled": true,
        "id": 91,
        "locales": [
        ],
        "name": "UK Webstore",
        "payment_methods": [
        ],
        "shipping_methods": [
        ],
        "type": "web",
        "updated_at": "2014-11-26T23:00:15+0000"
    }

Creating a channel
------------------

To create a new channel, you can execute the following request:

.. code-block:: text

    POST /api/channels/

Parameters
~~~~~~~~~~

code
    Unique code
color
    Color used in the backend
enabled *(optional)*
    Is enabled? (boolean)
locales *(optional)*
    Array of Locale id
currencies *(optional)*
    Array of Currency id
paymentMethods *(optional)*
    Array of PaymentMethod id
shippingMethods *(optional)*
    Array of ShippingMethod id

Response
~~~~~~~~

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "code": "WEB-US",
        "color": "Blue",
        "created_at": "2014-11-26T23:00:15+0000",
        "currencies": [
        ],
        "enabled": true,
        "id": 92,
        "locales": [
        ],
        "name": "US Webstore",
        "payment_methods": [
        ],
        "shipping_methods": [
        ],
        "type": "web",
        "updated_at": "2014-11-26T23:00:15+0000"
    }

Updating a channel
------------------

You can update an existing channel using PUT or PATCH method:

.. code-block:: text

    PUT /api/channels/92

.. code-block:: text

    PATCH /api/channels/92

Parameters
~~~~~~~~~~

code
    Unique code
color
    Color used in the backend
enabled *(optional)*
    Is enabled? (boolean)
locales *(optional)*
    Array of Locale id
currencies *(optional)*
    Array of Currency id
paymentMethods *(optional)*
    Array of PaymentMethod id
shippingMethods *(optional)*
    Array of ShippingMethod id

Response
~~~~~~~~

.. code-block:: text

    STATUS: 204 NO CONTENT

Deleting a channel
------------------

You can delete (soft) a channel from the system by making the following DELETE call:

.. code-block:: text

    DELETE /api/channels/92

Response
~~~~~~~~

.. code-block:: text

    STATUS: 204 NO CONTENT
