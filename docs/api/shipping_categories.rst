Shipping Categories API
=======================

These endpoints will allow you to easily manage shipping categories. Base URI is `/api/v1/shipping-categories`.

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

    Read more about :doc:`Shipping Categories in the component docs</components/Shipping/models>`.

Creating Shipping Category
--------------------------

To create a new shipping category you will need to call the ``/api/v1/shipping-categories/`` endpoint with the ``POST`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    POST /api/v1/shipping-categories/

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
^^^^^^^

To create a new shipping category use the below method.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/shipping-categories/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
                "name": "Light",
                "description": "Light weight items",
                "code": "SC3"
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

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
                "href": "\/api\/shipping-categories\/SC3"
            }
        }
    }

If you try to create a resource without name or code, you will receive a ``400 Bad Request`` error.

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/shipping-categories/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST

Exemplary Response
^^^^^^^^^^^^^^^^^^

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

Getting a Single Shipping Category
----------------------------------

To retrieve the details of a shipping category you will need to call the ``/api/v1/shipping-categories/{code}`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/shipping-categories/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Code of requested resource           |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To see the details of the shipping category with ``code = SC3`` use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/shipping-categories/SC3 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

.. note::

    The *SC3* code is just an example. Your value can be different.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "id": 1,
        "code": "SC3",
        "name": "Light",
        "createdAt": "2017-03-06T12:41:33+0100",
        "updatedAt": "2017-03-06T12:44:01+0100",
        "_links": {
            "self": {
                "href": "\/api\/v1\/shipping-categories\/SC3"
            }
        }
    }

Collection of Shipping Categories
---------------------------------

To retrieve a paginated list of shipping categories you will need to call the ``/api/v1/shipping-categories/`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/shipping-categories/

+---------------+----------------+-------------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                       |
+===============+================+===================================================================+
| Authorization | header         | Token received during authentication                              |
+---------------+----------------+-------------------------------------------------------------------+
| page          | query          | *(optional)* Number of the page, by default = 1                   |
+---------------+----------------+-------------------------------------------------------------------+
| limit         | query          | *(optional)* Number of items to display per page, by default = 10 |
+---------------+----------------+-------------------------------------------------------------------+

To see the first page of all shipping categories assigned to the promotion with ``code = HOLIDAY-SALE`` use the below method:

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/shipping-categories/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "page": 1,
        "limit": 4,
        "pages": 1,
        "total": 2,
        "_links": {
            "self": {
                "href": "\/api\/v1\/shipping-categories\/?sorting%5Bcode%5D=desc&page=1&limit=4"
            },
            "first": {
                "href": "\/api\/v1\/shipping-categories\/?sorting%5Bcode%5D=desc&page=1&limit=4"
            },
            "last": {
                "href": "\/api\/v1\/shipping-categories\/?sorting%5Bcode%5D=desc&page=1&limit=4"
            }
        },
        "_embedded": {
            "items": [
                {
                    "id": 1,
                    "code": "SC3",
                    "name": "Light",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/shipping-categories\/SC3"
                        }
                    }
                },
                {
                    "id": 2,
                    "code": "SC1",
                    "name": "Regular",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/shipping-categories\/SC1"
                        }
                    }
                }
            ]
        }
    }

Updating Shipping Category
--------------------------

To fully update a shipping category you will need to call the ``/api/v1/shipping-categories/{code}`` endpoint with the ``PUT`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    PUT /api/v1/shipping-categories/{code}

+---------------+----------------+-------------------------------------------+
| Parameter     | Parameter type | Description                               |
+===============+================+===========================================+
| Authorization | header         | Token received during authentication      |
+---------------+----------------+-------------------------------------------+
| code          | url attribute  | Code of requested resource                |
+---------------+----------------+-------------------------------------------+
| name          | request        | Name of creating shipping category        |
+---------------+----------------+-------------------------------------------+
| description   | request        | Description of creating shipping category |
+---------------+----------------+-------------------------------------------+

Example
^^^^^^^

To fully update the shipping category with ``code = SC3`` use the below method.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/shipping-categories/SC3 \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PUT \
        --data '
            {
                "name": "Ultra light",
                "description": "Ultra light weight items"
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

If you try to perform full shipping category update without all the required fields specified, you will receive a ``400 Bad Request`` error.

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/shipping-categories/SC3 \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PUT

Exemplary Response
^^^^^^^^^^^^^^^^^^

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

To partially update a shipping category you will need to call the ``/api/v1/shipping-categories/{code}`` endpoint with the ``PATCH`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    PATCH /api/v1/shipping-categories/{code}

+---------------+----------------+--------------------------------------------------------+
| Parameter     | Parameter type | Description                                            |
+===============+================+========================================================+
| Authorization | header         | Token received during authentication                   |
+---------------+----------------+--------------------------------------------------------+
| code          | url attribute  | Code of requested resource                             |
+---------------+----------------+--------------------------------------------------------+
| name          | request        | *(optional)* Name of creating shipping category        |
+---------------+----------------+--------------------------------------------------------+
| description   | request        | *(optional)* Description of creating shipping category |
+---------------+----------------+--------------------------------------------------------+

Example
^^^^^^^

To partially update the shipping category with ``code = SC3`` use the below method.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/shipping-categories/SC3 \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PATCH \
        --data '
            {
                "name": "Light"
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

Deleting Shipping Category
--------------------------

To delete a shipping category you will need to call the ``/api/v1/shipping-categories/{code}`` endpoint with the ``DELETE`` method.


Definition
^^^^^^^^^^

.. code-block:: text

    DELETE /api/v1/shipping-categories/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Code of requested resource           |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To delete the shipping category with ``code = SC3`` use the below method.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/shipping-categories/SC3 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X DELETE

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content
