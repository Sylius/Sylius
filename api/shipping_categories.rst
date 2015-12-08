Shipping Categories
===================

These endpoints will allow you to easily manage shipping categories. Base URI is `/api/shipping-categories`.

When you get a collection of resources, "Default" serialization group will be used and following fields will be exposed:

+-------+-------------------------------------+
| Field | Description                         |
+=======+=====================================+
| id    | Id of shipping category             |
+-------+-------------------------------------+
| name  | Name of shipping category           |
+-------+-------------------------------------+
| code  | Unique shipping category identifier |
+-------+-------------------------------------+

If you request for a more detailed data, you will receive an object with following fields:

+-------------+-------------------------------------+
| Field       | Description                         |
+=============+=====================================+
| id          | Id of shipping category             |
+-------------+-------------------------------------+
| name        | Name of shipping category           |
+-------------+-------------------------------------+
| code        | Unique shipping category identifier |
+-------------+-------------------------------------+
| description | Description of shipping category    |
+-------------+-------------------------------------+

.. note::

    Read more about `Shipping Categories`__

__ http://docs.sylius.org/en/latest/components/Shipping/models.html#shippingcategory

Collection of Shipping Categories
---------------------------------

You can retrieve the full shipment categories list by making the following request:

Definition
..........

.. code-block:: text

    GET /api/shipping-categories/

+---------------+----------------+-------------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                       |
+===============+================+===================================================================+
| Authorization | header         | Token received during authentication                              |
+---------------+----------------+-------------------------------------------------------------------+
| page          | query          | *(optional)* Number of the page, by default = 1                   |
+---------------+----------------+-------------------------------------------------------------------+
| limit         | query          | *(optional)* Number of items to display per page, by default = 10 |
+---------------+----------------+-------------------------------------------------------------------+


Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/shipping-categories/
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng"
        -H “Accept: application/json”

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "page": 1,
        "limit": 10,
        "pages": 1,
        "total": 2,
        "_links": {
            "self": {
                "href": "\/api\/shipping-categories\/?page=1&limit=10"
            },
            "first": {
                "href": "\/api\/shipping-categories\/?page=1&limit=10"
            },
            "last": {
                "href": "\/api\/shipping-categories\/?page=1&limit=10"
            }
        },
        "_embedded": {
            "items": [
                {
                    "id": 1,
                    "code": "SC1",
                    "name": "Regular",
                    "_links": {
                        "self": {
                            "href": "\/api\/shipping-categories\/1"
                        }
                    }
                },
                {
                    "id": 2,
                    "code": "SC2",
                    "name": "Heavy",
                    "_links": {
                        "self": {
                            "href": "\/api\/shipping-categories\/2"
                        }
                    }
                }
            ]
        }
    }

Getting a Single Shipping Category
----------------------------------

You can request detailed shipping category information by executing the following request:

Definition
..........

.. code-block:: text

    GET /api/shipping-categories/{id}

+---------------+----------------+-------------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                       |
+===============+================+===================================================================+
| Authorization | header         | Token received during authentication                              |
+---------------+----------------+-------------------------------------------------------------------+
| id            | url attribute  | Id of requested resource                                          |
+---------------+----------------+-------------------------------------------------------------------+
| page          | query          | *(optional)* Number of the page, by default = 1                   |
+---------------+----------------+-------------------------------------------------------------------+
| limit         | query          | *(optional)* Number of items to display per page, by default = 10 |
+---------------+----------------+-------------------------------------------------------------------+

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/shipping-categories/1
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng"
        -H “Accept: application/json”

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "id": 1,
        "code": "SC1",
        "name": "Regular",
        "description": "Regular weight items",
        "_links": {
            "self": {
                "href": "\/api\/shipping-categories\/1"
            }
        }
    }

Creating Shipping Category
--------------------------

Definition
..........

.. code-block:: text

    POST /api/shipping-categories/

+---------------+----------------+--------------------------------------------------------+
| Parameter     | Parameter type | Description                                            |
+===============+================+========================================================+
| Authorization | header         | Token received during authentication                   |
+---------------+----------------+--------------------------------------------------------+
| name          | request        | Name of creating shipping category                     |
+---------------+----------------+--------------------------------------------------------+
| code          | request        | **(unique)** Shipping category identifier              |
+---------------+----------------+--------------------------------------------------------+
| description   | request        | *(optional)* Description of creating shipping category |
+---------------+----------------+--------------------------------------------------------+

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/shipping-categories/
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng"
        -H "Content-Type: application/json"
        -X POST
        --data '
            {
                "name": "Light",
                "description": "Light weight items",
                "code": "SC3"
            }
        '

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 201 Created

.. code-block:: json

    {
        "id": 3,
        "code": "SC3",
        "name": "Light",
        "description": "Light weight items",
        "_links": {
            "self": {
                "href": "\/api\/shipping-categories\/3"
            }
        }
    }

If you try to create a resource without name or code, you will receive a 400 error.

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/shipping-categories/-1
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng"
        -H “Accept: application/json”
        -X POST

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 400 Bad Request

.. code-block:: json

    {
        "code": 400,
        "message": "Validation Failed",
        "errors": {
            "children": {
                "name": {
                    "errors": [
                        "Please enter shipping category name."
                    ]
                },
                "code":  {
                    "errors":  [
                        "Please enter shipping category code."
                    ]
                },
                "description": []
            }
        }
    }

Updating Shipping Category
--------------------------

You can request full or partial update of resource. For full shipping category update, you should use PUT method.

Definition
..........

.. code-block:: text

    PUT /api/shipping-categories/{id}

+---------------+----------------+-------------------------------------------+
| Parameter     | Parameter type | Description                               |
+===============+================+===========================================+
| Authorization | header         | Token received during authentication      |
+---------------+----------------+-------------------------------------------+
| id            | url attribute  | Id of requested resource                  |
+---------------+----------------+-------------------------------------------+
| name          | request        | Name of creating shipping category        |
+---------------+----------------+-------------------------------------------+
| description   | request        | Description of creating shipping category |
+---------------+----------------+-------------------------------------------+

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/shipping-categories/3
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng"
        -H "Content-Type: application/json"
        -X PUT
        --data '
            {
                "name": "Ultra light",
                "description": "Ultra light weight items"
            }
        '

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 204 No Content

If you try to perform full shipping category update without all required fields specified, you will receive a 400 error.

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/shipping-categories/-1
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng"
        -H “Accept: application/json”
        -X PUT

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 400 Bad Request

.. code-block:: json

    {
        "code": 400,
        "message": "Validation Failed",
        "errors": {
            "children": {
                "name": {
                    "errors": [
                        "Please enter shipping category name."
                    ]
                },
                "description": []
            }
        }
    }

In order to perform a partial update, you should use a PATCH method.

Definition
..........

.. code-block:: text

    PATCH /api/shipping-categories/{id}

+---------------+----------------+--------------------------------------------------------+
| Parameter     | Parameter type | Description                                            |
+===============+================+========================================================+
| Authorization | header         | Token received during authentication                   |
+---------------+----------------+--------------------------------------------------------+
| id            | url attribute  | Id of requested resource                               |
+---------------+----------------+--------------------------------------------------------+
| name          | request        | *(optional)* Name of creating shipping category        |
+---------------+----------------+--------------------------------------------------------+
| description   | request        | *(optional)* Description of creating shipping category |
+---------------+----------------+--------------------------------------------------------+

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/shipping-categories/3
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng"
        -H "Content-Type: application/json"
        -X PATCH
        --data '{"name": "Light"}'

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 204 No Content

Deleting Shipping Category
--------------------------

Definition
..........

.. code-block:: text

    DELETE /api/shipping-categories/{id}

+---------------+----------------+-------------------------------------------+
| Parameter     | Parameter type | Description                               |
+===============+================+===========================================+
| Authorization | header         | Token received during authentication      |
+---------------+----------------+-------------------------------------------+
| id            | url attribute  | Id of requested resource                  |
+---------------+----------------+-------------------------------------------+

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/shipping-categories/3
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng"
        -H “Accept: application/json”
        -X DELETE

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 204 No Content
