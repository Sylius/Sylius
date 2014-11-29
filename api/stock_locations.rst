StockLocation API
=================

Sylius stock locations API endpoint is ``/api/stock-locations``.

Index of all stock locations
---------------------

To browse all stock locations configured, use the following request:

.. code-block:: text

    GET /api/stock-locations

Parameters
~~~~~~~~~~

page
    Number of the page, by default = 1
limit
    Number of items to display per page

Response
~~~~~~~~

Response will contain a paginated list of stock locations.

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "_embedded": {
            "items": [
                {
                    "_links": {
                        "self": {
                            "href": "/api/stock-locations/57"
                        }
                    },
                    "address": {
                        "_links": {
                            "country": {
                                "href": "/api/countries/7517"
                            }
                        },
                        "city": "Naderton",
                        "created_at": "2014-11-26T23:00:21+0000",
                        "first_name": "Sabrina",
                        "id": 519,
                        "last_name": "Roberts",
                        "postcode": "45449-6358",
                        "street": "75382 Larkin Junctions",
                        "updated_at": "2014-11-26T23:00:21+0000"
                    },
                    "code": "LONDON-1",
                    "created_at": "2014-11-26T23:00:21+0000",
                    "enabled": true,
                    "id": 57,
                    "name": "London Werehouse",
                    "updated_at": "2014-11-26T23:00:21+0000"
                }
            ]
        },
        "_links": {
            "first": {
                "href": "/api/stock-locations/?page=1&limit=10"
            },
            "last": {
                "href": "/api/stock-locations/?page=1&limit=10"
            },
            "self": {
                "href": "/api/stock-locations/?page=1&limit=10"
            }
        },
        "limit": 10,
        "page": 1,
        "pages": 1,
        "total": 4
    }


Getting a single stock location
-------------------------------

You can view a single stock location by executing the following request:

.. code-block:: text

    GET /api/stock-locations/519

Response
~~~~~~~~

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "_links": {
            "self": {
                "href": "/api/stock-locations/57"
            }
        },
        "address": {
            "_links": {
                "country": {
                    "href": "/api/countries/7517"
                }
            },
            "city": "Naderton",
            "created_at": "2014-11-26T23:00:21+0000",
            "first_name": "Sabrina",
            "id": 519,
            "last_name": "Roberts",
            "postcode": "45449-6358",
            "street": "75382 Larkin Junctions",
            "updated_at": "2014-11-26T23:00:21+0000"
        },
        "code": "LONDON-1",
        "created_at": "2014-11-26T23:00:21+0000",
        "enabled": true,
        "id": 57,
        "name": "London Werehouse",
        "updated_at": "2014-11-26T23:00:21+0000"
    }

Create a new stock location
---------------------------

To create a new stock location, you must execute the following request:

.. code-block:: text

    POST /api/stock-locations

Parameters
~~~~~~~~~~

code
    Unique code
name
    The name of location
enabled *(optional)*
    Is enabled? (boolean)

Response
~~~~~~~~

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "_links": {
            "self": {
                "href": "/api/stock-locations/58"
            }
        },
        "code": "LONDON-2",
        "created_at": "2014-11-26T23:00:21+0000",
        "enabled": true,
        "id": 58,
        "name": "London Werehouse II",
        "updated_at": "2014-11-26T23:00:21+0000"
    }

Updating a stock location
-------------------------

You can update an existing stock location using PUT or PATCH method:

.. code-block:: text

    PUT /api/stock-locations/92

.. code-block:: text

    PATCH /api/stock-locations/92

Parameters
~~~~~~~~~~

code
    Unique code
name
    The name of location
enabled *(optional)*
    Is enabled? (boolean)

Response
~~~~~~~~

.. code-block:: text

    STATUS: 204 NO CONTENT

Deleting a stock location
-------------------------

You can remove a stock location from the system by making the following DELETE call:

.. code-block:: text

    DELETE /api/stock-locations/92

Response
~~~~~~~~

.. code-block:: text

    STATUS: 204 NO CONTENT
