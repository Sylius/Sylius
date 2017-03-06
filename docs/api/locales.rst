Locales API
===========

These endpoints will allow you to easily manage locales. Base URI is `/api/v1/locales`.

Locale API response structure
-----------------------------

If you request a locale via API, you will receive an object with the following fields:

+-------+--------------------------+
| Field | Description              |
+=======+==========================+
| id    | Id of the locale         |
+-------+--------------------------+
| code  | Unique locale identifier |
+-------+--------------------------+

If you request for more detailed data, you will receive an object with the following fields:

+-----------+--------------------------------+
| Field     | Description                    |
+===========+================================+
| id        | Id of the locale               |
+-----------+--------------------------------+
| code      | Unique locale identifier       |
+-----------+--------------------------------+
| updatedAt | Last update date of the locale |
+-----------+--------------------------------+
| createdAt | Creation date of the locale    |
+-----------+--------------------------------+

.. note::

    Read more about :doc:`Locales in the component docs</components/Locale/models>`.

Creating a Locale
-----------------

To create a new locale you will need to call the ``/api/v1/locales/`` endpoint with the ``POST`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    POST /api/v1/locales/

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | request        | **(unique)** Locale identifier       |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/locales/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
                "code": "pl"
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "id": 4,
        "code": "pl",
        "createdAt": "2017-02-14T12:49:38+0100",
        "updatedAt": "2017-02-14T12:49:39+0100",
        "_links": {
            "self": {
                "href": "\/api\/v1\/locales\/pl"
            }
        }
    }

If you try to create a locale without code you will receive a ``400 Bad Request`` error.

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/locales/ \
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
                "code": {
                    "errors": [
                        "Please enter locale code."
                    ]
                }
            }
        }
    }

Getting a Single Locale
-----------------------

To retrieve the details of a locale you will need to call the ``/api/v1/locales/code`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/locales/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Code of the requested locale         |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To see the details of the locale with ``code = pl`` use the below method:

.. code-block:: bash

     $ curl http://demo.sylius.org/api/v1/locales/pl \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

.. note::

    The *pl* code is just an example. Your value can be different.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

     STATUS: 200 OK

.. code-block:: json

    {
        "id": 4,
        "code": "pl",
        "createdAt": "2017-02-14T12:49:38+0100",
        "updatedAt": "2017-02-14T12:49:39+0100",
        "_links": {
            "self": {
                "href": "\/api\/v1\/locales\/pl"
            }
        }
    }

Collection of Locales
---------------------

To retrieve a paginated list of locales you will need to call the ``/api/v1/locales/`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/locales/

+---------------+----------------+-------------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                       |
+===============+================+===================================================================+
| Authorization | header         | Token received during authentication                              |
+---------------+----------------+-------------------------------------------------------------------+
| page          | query          | *(optional)* Number of the page, by default = 1                   |
+---------------+----------------+-------------------------------------------------------------------+
| paginate      | query          | *(optional)* Number of items to display per page, by default = 10 |
+---------------+----------------+-------------------------------------------------------------------+

To see the first page of all locales use the below method:

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/locales/ \
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
        "total": 3,
        "_links": {
            "self": {
                "href": "\/api\/v1\/locales\/?page=1&limit=10"
            },
            "first": {
                "href": "\/api\/v1\/locales\/?page=1&limit=10"
            },
            "last": {
                "href": "\/api\/v1\/locales\/?page=1&limit=10"
            }
        },
        "_embedded": {
            "items": [
                {
                    "id": 2,
                    "code": "en_US",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/locales\/en_US"
                        }
                    }
                },
                {
                    "id": 3,
                    "code": "af",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/locales\/af"
                        }
                    }
                },
                {
                    "id": 4,
                    "code": "pl",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/locales\/pl"
                        }
                    }
                }
            ]
        }
    }

Deleting a Locale
-----------------

To delete a locale you will need to call the ``/api/v1/locales/code`` endpoint with the ``DELETE`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    DELETE /api/v1/locales/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Code of the removed locale           |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/locales/pl \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X DELETE

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content
