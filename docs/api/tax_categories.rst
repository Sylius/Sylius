Tax Categories API
==================

These endpoints will allow you to easily manage tax categories. Base URI is `/api/v1/tax-categories`.

Tax Category structure
----------------------

Tax Category API response structure
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

If you request a tax category via API, you will receive an object with the following fields:

+-------+--------------------------------+
| Field | Description                    |
+=======+================================+
| id    | Id of the tax category         |
+-------+--------------------------------+
| code  | Unique tax category identifier |
+-------+--------------------------------+
| name  | Name of the tax category       |
+-------+--------------------------------+


If you request for more detailed data, you will receive an object with the following fields:

+-------------+---------------------------------+
| Field       | Description                     |
+=============+=================================+
| id          | Id of the tax category          |
+-------------+---------------------------------+
| code        | Unique tax category identifier  |
+-------------+---------------------------------+
| name        | Name of the tax category        |
+-------------+---------------------------------+
| description | Description of the tax category |
+-------------+---------------------------------+
| createdAt   | Date of creation                |
+-------------+---------------------------------+
| updatedAt   | Date of last update             |
+-------------+---------------------------------+

.. note::

    Read more about :doc:`the Tax Category model in the component docs</components/Taxation/models>`.

Creating a Tax Category
-----------------------

To create a new tax category you will need to call the ``/api/v1/tax-categories/`` endpoint with the ``POST`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    POST /api/v1/tax-categories/

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | request        | **(unique)** Tax category identifier |
+---------------+----------------+--------------------------------------+
| name          | request        | Name of the tax category             |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To create a new tax category use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/tax-categories/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
                "code": "food",
                "name": "Food"
            }
    '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "id": 4,
        "code": "food",
        "name": "Food",
        "createdAt": "2017-02-21T12:49:48+0100",
        "updatedAt": "2017-02-21T12:49:50+0100",
        "_links": {
            "self": {
                "href": "\/api\/v1\/tax-categories\/food"
            }
        }
    }

.. warning::

    If you try to create a tax category without name or code you will receive a ``400 Bad Request`` error, that will contain validation errors.

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/tax-categories/ \
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
                        "Please enter tax category name."
                    ]
                },
                "description": {},
                "code": {
                    "errors": [
                        "Please enter tax category code."
                    ]
                }
            }
        }
    }

You can also create a tax category with additional (not required) fields:

+---------------+----------------+---------------------------------+
| Parameter     | Parameter type | Description                     |
+===============+================+=================================+
| description   | request        | Description of the tax category |
+---------------+----------------+---------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/tax-categories/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
                "code": "food",
                "name": "Food",
                "description": "The food category."
            }
         '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "id": 5,
        "code": "food",
        "name": "Food",
        "description": "The food category.",
        "createdAt": "2017-02-21T12:58:41+0100",
        "updatedAt": "2017-02-21T12:58:42+0100",
        "_links": {
            "self": {
                "href": "\/api\/v1\/tax-categories\/food"
            }
        }
    }

Getting a Single Tax Category
-----------------------------

To retrieve the details of a tax category you will need to call the ``/api/v1/tax-categories/{code}`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/tax-categories/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Unique tax category identifier       |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/tax-categories/food \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

.. note::

    The *food* is an exemplary value. Your value can be different.
    Check in the list of all tax categories if you are not sure which code should be used.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "id": 5,
        "code": "food",
        "name": "Food",
        "description": "The food category.",
        "createdAt": "2017-02-21T12:58:41+0100",
        "updatedAt": "2017-02-21T12:58:42+0100",
        "_links": {
            "self": {
                "href": "\/api\/v1\/tax-categories\/food"
            }
        }
    }

Collection of Tax Categories
----------------------------

To retrieve a paginated list of tax categories you will need to call the ``/api/v1/tax-categories/`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/tax-categories/

+-------------------------------------+----------------+---------------------------------------------------+
| Parameter                           | Parameter type | Description                                       |
+=====================================+================+===================================================+
| Authorization                       | header         | Token received during authentication              |
+-------------------------------------+----------------+---------------------------------------------------+
| limit                               | query          | *(optional)* Number of items to display per page, |
|                                     |                | by default = 10                                   |
+-------------------------------------+----------------+---------------------------------------------------+
| sorting['nameOfField']['direction'] | query          | *(optional)* Field and direction of sorting,      |
|                                     |                | by default 'desc' and 'createdAt'                 |
+-------------------------------------+----------------+---------------------------------------------------+

To see the first page of all tax categories use the below method:

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/tax-categories/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "page": 1,
        "limit": 10,
        "pages": 1,
        "total": 4,
        "_links": {
            "self": {
                "href": "\/api\/v1\/tax-categories\/?page=1&limit=10"
            },
            "first": {
                "href": "\/api\/v1\/tax-categories\/?page=1&limit=10"
            },
            "last": {
                "href": "\/api\/v1\/tax-categories\/?page=1&limit=10"
            }
        },
        "_embedded": {
            "items": [
                {
                    "id": 1,
                    "code": "clothing",
                    "name": "Clothing",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/tax-categories\/clothing"
                        }
                    }
                },
                {
                    "id": 2,
                    "code": "books",
                    "name": "Books",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/tax-categories\/books"
                        }
                    }
                },
                {
                    "id": 3,
                    "code": "other",
                    "name": "Other",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/tax-categories\/other"
                        }
                    }
                },
                {
                    "id": 5,
                    "code": "food",
                    "name": "Food",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/tax-categories\/food"
                        }
                    }
                }
            ]
        }
    }

Updating a Tax Category
-----------------------

To fully update a tax category you will need to call the ``/api/v1/tax-categories/{code}`` endpoint with the ``PUT`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    PUT /api/v1/tax-categories/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Unique tax category identifier       |
+---------------+----------------+--------------------------------------+
| name          | request        | Name of the tax category             |
+---------------+----------------+--------------------------------------+
| description   | request        | Description of the tax category      |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

 To fully update the tax category with ``code = food`` use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/tax-categories/food \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PUT \
        --data '
            {
                "name": "Vegetables",
                "description": "The category of food: vegetables"
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

If you try to perform a full tax category update without all the required fields specified, you will receive a ``400 Bad Request`` error.

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/tax-categories/food \
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
                        "Please enter tax category name."
                    ]
                },
                "description": {},
                "code": {}
            }
        }
    }


To update a tax category partially you will need to call the ``/api/v1/tax-categories/{code}`` endpoint with the ``PATCH`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    PATCH /api/v1/tax-categories/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Unique tax category identifier       |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To partially update the tax category with ``code = food`` use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/tax-categories/food \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PATCH \
        --data '
            {
                "description": "The category of food: vegetables"
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

Deleting a Tax Category
-----------------------

To delete a tax category you will need to call the ``/api/v1/tax-categories/{code}`` endpoint with the ``DELETE`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    DELETE /api/v1/tax-categories/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Unique tax category identifier       |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/tax-categories/food \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X DELETE

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content
