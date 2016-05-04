Payments API
============

Sylius payment API endpoint is `/api/payments`.

Index of all payments
---------------------

You can retrieve the full list payment by making the following request:

.. code-block:: text

    GET /api/payments/

Parameters
~~~~~~~~~~

page
    Number of the page, by default = 1
limit
    Number of items to display per page
criteria[channel] *(optional)*
    The channel id
criteria[number] *(optional)*
    The order number
criteria[billingAddress] *(optional)*
    First or last name of the customer bill to address
criteria[createdAtFrom] *(optional)*
    Starting date
criteria[createdAtTo] *(optional)*
    End date

Response
~~~~~~~~

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "page":1,
        "limit":10,
        "pages":8,
        "total":80,
        "_links":{
            "self":{
                "href":"\/api\/payments\/?page=1"
            },
            "first":{
                "href":"\/api\/payments\/?page=1"
            },
            "last":{
                "href":"\/api\/payments\/?page=12"
            },
            "next":{
                "href":"\/api\/payments\/?page=2"
            }
        },
        "_embedded":{
            "items":[
                {"to": "do"}
            ]
        }
    }

Getting a single payment
------------------------

You can view a single payment by executing the following request:

.. code-block:: text

    GET /api/payments/251

Response
~~~~~~~~

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {"to": "do"}

Deleting a payment
------------------

You can delete a payment from the system by making the following DELETE call:

.. code-block:: text

    DELETE /api/payments/99

Response
~~~~~~~~

.. code-block:: text

    STATUS: 204 NO CONTENT
