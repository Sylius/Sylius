Provinces API
=============

These endpoints will allow you to easily manage provinces. Base URI is `/api/v1/provinces`.

Province API response structure
-------------------------------

If you request a province via API, you will receive an object with the following fields:

+-------+----------------------------+
| Field | Description                |
+=======+============================+
| id    | Id of the province         |
+-------+----------------------------+
| code  | Unique province identifier |
+-------+----------------------------+
| name  | Name of the province       |
+-------+----------------------------+

If you request for more detailed data, you will receive an object with the following fields:

+--------------+-----------------------------------+
| Field        | Description                       |
+==============+===================================+
| id           | Id of the province                |
+--------------+-----------------------------------+
| code         | Unique province identifier        |
+--------------+-----------------------------------+
| name         | Name of the province              |
+--------------+-----------------------------------+
| abbreviation | Abbreviation of the province      |
+--------------+-----------------------------------+
| createdAt    | The province's creation date      |
+--------------+-----------------------------------+
| updatedAt    | The province's last updating date |
+--------------+-----------------------------------+

.. note::

    Read more about :doc:`Provinces in the component docs</components/Addressing/models>`.

Getting a Single Province
-------------------------

To retrieve the details of a specific province you will need to call the ``/api/v1/countries/{countryCode}/provinces/{code}`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/countries/{countryCode}/provinces/{code}

+---------------+----------------+---------------------------------------------------+
| Parameter     | Parameter type | Description                                       |
+===============+================+===================================================+
| Authorization | header         | Token received during authentication              |
+---------------+----------------+---------------------------------------------------+
| countryCode   | url attribute  | Code of the country to which the province belongs |
+---------------+----------------+---------------------------------------------------+
| code          | url attribute  | Code of the requested province                    |
+---------------+----------------+---------------------------------------------------+

Example
^^^^^^^

To see the details of the province with ``code = PL-MZ`` which belongs to the country with ``code = PL`` use the below method:

.. code-block:: bash

     $ curl http://demo.sylius.org/api/v1/countries/PL/provinces/PL-MZ \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

.. note::

    The *PL* ans *PL-MZ* codes are just examples. Your value can be different.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

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

Deleting a Province
-------------------

To delete a province you will need to call the ``/api/v1/countries/{countryCode}/provinces/{code}`` endpoint with the ``DELETE`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    DELETE /api/v1/countries/{countryCode}/provinces/{code}

+---------------+----------------+---------------------------------------------------+
| Parameter     | Parameter type | Description                                       |
+===============+================+===================================================+
| Authorization | header         | Token received during authentication              |
+---------------+----------------+---------------------------------------------------+
| countryCode   | url attribute  | Code of the country to which the province belongs |
+---------------+----------------+---------------------------------------------------+
| code          | url attribute  | Code of the requested province                    |
+---------------+----------------+---------------------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://sylius.dev/api/v1/countries/PL/provinces/PL-MZ \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X DELETE

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content
