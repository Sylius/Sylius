Countries API
=============

These endpoints will allow you to easily manage countries. Base URI is `/api/v1/countries`.

Country API response structure
------------------------------

If you request a country via API, you will receive an object with the following fields:

+-------+---------------------------+
| Field | Description               |
+=======+===========================+
| id    | Id of the country         |
+-------+---------------------------+
| code  | Unique country identifier |
+-------+---------------------------+

If you request for more detailed data, you will receive an object with the following fields:

+-----------+-------------------------------------------------------------+
| Field     | Description                                                 |
+===========+=============================================================+
| id        | Id of the country                                           |
+-----------+-------------------------------------------------------------+
| code      | Unique country identifier                                   |
+-----------+-------------------------------------------------------------+
| enabled   | Information says if the country is enabled (default: false) |
+-----------+-------------------------------------------------------------+
| provinces | Collection of the country's provinces                       |
+-----------+-------------------------------------------------------------+

.. note::

    Read more about :doc:`Countries in the component docs</components/Addressing/models>`.

Creating a Country
------------------

To create a new country you will need to call the ``/api/v1/countries/`` endpoint with the ``POST`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    POST /api/v1/countries/

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | request        | **(unique)** Country identifier      |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/countries/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
                "code": "PL"
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "id": 2,
        "code": "PL",
        "provinces": [],
        "enabled": false,
        "_links": {
            "self": {
                "href": "\/api\/v1\/countries\/PL"
            }
        }
    }

If you try to create a country without code you will receive a ``400 Bad Request`` error.

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/countries/ \
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
                "provinces": {},
                "enabled": {},
                "code": {
                    "errors": [
                      "Please enter country ISO code."
                    ]
                }
            }
        }
    }

You can also create a country with additional (not required) fields:

+---------------+----------------+-------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                 |
+===============+================+=============================================================+
| enabled       | request        | Information says if the country is enabled (default: false) |
+---------------+----------------+-------------------------------------------------------------+
| provinces     | request        | Collection of the country's provinces                       |
+---------------+----------------+-------------------------------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/countries/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
                "code":"PL",
                "enabled": true,
                "provinces": [
                    {
                        "name": "mazowieckie",
                        "code": "PL-MZ"
                    }
                ]
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "id": 4,
        "code": "PL",
        "provinces": [
            {
                "id": 1,
                "code": "PL-MZ",
                "name": "mazowieckie",
                "_links": {
                    "self": {
                        "href": "\/api\/v1\/countries\/PL\/provinces\/PL-MZ"
                    },
                    "country": {
                        "href": "\/api\/v1\/countries\/PL"
                    }
                }
            }
        ],
        "enabled": true,
        "_links": {
            "self": {
                "href": "\/api\/v1\/countries\/PL"
            }
        }
    }

Getting a Single Country
------------------------

To retrieve the details of a country you will need to call the ``/api/v1/countries/{code}`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/countries/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Code of the requested country        |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To see the details of the country with ``code = US`` use the below method:

.. code-block:: bash

     $ curl http://demo.sylius.org/api/v1/countries/US \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

.. note::

    The *US* code is just an example. Your value can be different.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

     STATUS: 200 OK

.. code-block:: json

    {
        "id": 1,
        "code": "US",
        "provinces": [],
        "enabled": true,
        "_links": {
            "self": {
                "href": "\/api\/v1\/countries\/US"
            }
        }
    }

Collection of Countries
-----------------------

To retrieve a paginated list of countries you will need to call the ``/api/v1/countries/`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/countries/

+---------------+----------------+-------------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                       |
+===============+================+===================================================================+
| Authorization | header         | Token received during authentication                              |
+---------------+----------------+-------------------------------------------------------------------+
| page          | query          | *(optional)* Number of the page, by default = 1                   |
+---------------+----------------+-------------------------------------------------------------------+
| paginate      | query          | *(optional)* Number of items to display per page, by default = 10 |
+---------------+----------------+-------------------------------------------------------------------+

To see the first page of all countries use the below method:

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/countries/ \
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
        "total": 2,
        "_links": {
            "self": {
                "href": "\/api\/v1\/countries\/?page=1&limit=10"
            },
            "first": {
                "href": "\/api\/v1\/countries\/?page=1&limit=10"
            },
            "last": {
                "href": "\/api\/v1\/countries\/?page=1&limit=10"
            }
        },
        "_embedded": {
            "items": [
                {
                    "id": 1,
                    "code": "US",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/countries\/US"
                        }
                    }
                },
                {
                    "id": 4,
                    "code": "PL",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/countries\/PL"
                        }
                    }
                }
            ]
        }
    }

Deleting a Country
------------------

To delete a country you will need to call the ``/api/v1/countries/{code}`` endpoint with the ``DELETE`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    DELETE /api/v1/countries/{code}

+---------------+----------------+-------------------------------------------+
| Parameter     | Parameter type | Description                               |
+===============+================+===========================================+
| Authorization | header         | Token received during authentication      |
+---------------+----------------+-------------------------------------------+
| code          | url attribute  | Code of the removed country               |
+---------------+----------------+-------------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/countries/PL \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X DELETE

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content
