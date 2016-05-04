Shipments API
=============

Sylius shipments API endpoint is `/api/shipments`.

Index of all shipments
----------------------

You can retrieve the full list shipment by making the following request:

.. code-block:: text

    GET /api/shipments/

Parameters
~~~~~~~~~~

page
    Number of the page, by default = 1
limit
    Number of items to display per page
criteria[channel] *(optional)*
    The channel id
criteria[stockLocation] *(optional)*
    The id of stock location
criteria[number] *(optional)*
    The order number
criteria[shippingAddress] *(optional)*
    First or last name of the customer ship to address
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
                "href":"\/api\/shipments\/?page=1"
            },
            "first":{
                "href":"\/api\/shipments\/?page=1"
            },
            "last":{
                "href":"\/api\/shipments\/?page=12"
            },
            "next":{
                "href":"\/api\/shipments\/?page=2"
            }
        },
        "_embedded":{
            "items":[
                {
                    "_links": {
                        "method": {
                            "href": "/api/shipping-methods/120"
                        },
                        "order": {
                            "href": "/api/orders/302"
                        },
                        "self": {
                            "href": "/api/shipments/251"
                        }
                    },
                    "created_at": "2014-11-26T23:00:34+0000",
                    "id": 251,
                    "method": {
                        "_links": {
                            "self": {
                                "href": "/api/shipping-methods/120"
                            },
                            "zone": {
                                "href": "/api/zones/120"
                            }
                        },
                        "calculator": "flexible_rate",
                        "category_requirement": 1,
                        "configuration": {
                            "additional_item_cost": 500,
                            "additional_item_limit": 10,
                            "first_item_cost": 4000
                        },
                        "created_at": "2014-11-26T23:00:15+0000",
                        "enabled": true,
                        "id": 120,
                        "name": "FedEx World Shipping",
                        "updated_at": "2014-11-26T23:00:15+0000"
                    },
                    "state": "backordered",
                    "updated_at": "2014-11-26T23:00:34+0000"
                }
            ]
        }
    }

Getting a single shipment
-------------------------

You can view a single shipment by executing the following request:

.. code-block:: text

    GET /api/shipments/251

Response
~~~~~~~~

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "_links": {
            "method": {
                "href": "/api/shipping-methods/120"
            },
            "order": {
                "href": "/api/orders/302"
            },
            "self": {
                "href": "/api/shipments/251"
            }
        },
        "created_at": "2014-11-26T23:00:34+0000",
        "id": 251,
        "method": {
            "_links": {
                "self": {
                    "href": "/api/shipping-methods/120"
                },
                "zone": {
                    "href": "/api/zones/120"
                }
            },
            "calculator": "flexible_rate",
            "category_requirement": 1,
            "configuration": {
                "additional_item_cost": 500,
                "additional_item_limit": 10,
                "first_item_cost": 4000
            },
            "created_at": "2014-11-26T23:00:15+0000",
            "enabled": true,
            "id": 120,
            "name": "FedEx World Shipping",
            "updated_at": "2014-11-26T23:00:15+0000"
        },
        "state": "backordered",
        "updated_at": "2014-11-26T23:00:34+0000"
    }

Deleting a shipment
-------------------

You can delete a shipment from the system by making the following DELETE call:

.. code-block:: text

    DELETE /api/shipments/24

Response
~~~~~~~~

.. code-block:: text

    STATUS: 204 NO CONTENT
