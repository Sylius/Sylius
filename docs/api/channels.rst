Channels API
============

These endpoints will allow you to easily manage channels. Base URI is `/api/v1/channels`.

Channel API response structure
------------------------------

If you request a channel via API, you will receive an object with the following fields:

+--------------+---------------------------+
| Field        | Description               |
+==============+===========================+
| id           | Id of the channel         |
+--------------+---------------------------+
| code         | Unique channel identifier |
+--------------+---------------------------+

If you request for more detailed data, you will receive an object with the following fields:

+------------------------+------------------------------------------------------------------------+
| Field                  | Description                                                            |
+========================+========================================================================+
| id                     | Id of the channel                                                      |
+------------------------+------------------------------------------------------------------------+
| code                   | Unique channel identifier                                              |
+------------------------+------------------------------------------------------------------------+
| taxCalculationStrategy | Strategy which will be applied during processing orders in the channel |
+------------------------+------------------------------------------------------------------------+
| name                   | Name of the channel                                                    |
+------------------------+------------------------------------------------------------------------+
| hostname               | Name of the host for the channel                                       |
+------------------------+------------------------------------------------------------------------+
| defaultLocale          | Locale code which is already saved in Locales menu                     |
+------------------------+------------------------------------------------------------------------+
| enabled                | Gives an information about channel availability                        |
+------------------------+------------------------------------------------------------------------+
| description            | Description of the channel                                             |
+------------------------+------------------------------------------------------------------------+
| color                  | Allows to recognize orders made in the channel                         |
+------------------------+------------------------------------------------------------------------+
| createdAt              | The channel's creation date                                            |
+------------------------+------------------------------------------------------------------------+
| updatedAt              | The channel's last updating date                                       |
+------------------------+------------------------------------------------------------------------+

.. note::

    Read more about :doc:`Channels docs </components_and_bundles/components/Channel/models>`.

Creating a Channel
------------------

To create a new channel you will need to call the ``/api/v1/channels/`` endpoint with the ``POST`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    POST /api/v1/channels/

+-----------------------------+----------------+------------------------------------------------------------------------+
| Parameter                   | Parameter type | Description                                                            |
+=============================+================+========================================================================+
| Authorization               | header         | Token received during authentication                                   |
+-----------------------------+----------------+------------------------------------------------------------------------+
| code                        | request        | **(unique)** Channel identifier                                        |
+-----------------------------+----------------+------------------------------------------------------------------------+
| name                        | request        | Name of the Channel                                                    |
+-----------------------------+----------------+------------------------------------------------------------------------+
| taxCalculationStrategy      | request        | Strategy which will be applied during processing orders in the channel |
+-----------------------------+----------------+------------------------------------------------------------------------+
| defaultLocale               | request        | Locale code which is already saved in Locales menu                     |
+-----------------------------+----------------+------------------------------------------------------------------------+
| baseCurrency                | request        | Currency in which will be stored all money values in system            |
+-----------------------------+----------------+------------------------------------------------------------------------+
| currencies                  | request        | List of available currencies                                           |
+-----------------------------+----------------+------------------------------------------------------------------------+
| locales                     | request        | List of available locales                                              |
+-----------------------------+----------------+------------------------------------------------------------------------+
| themeName                   | request        | Selected theme for the Channel, which is already registered in Sylius  |
+-----------------------------+----------------+------------------------------------------------------------------------+
| contactEmail                | request        | Email which will be used in sylius_shop_contact_request route          |
+-----------------------------+----------------+------------------------------------------------------------------------+
| skippingShippingStepAllowed | request        | Can customer skip the shipping step in checkout or not                 |
+-----------------------------+----------------+------------------------------------------------------------------------+
| skippingPaymentStepAllowed  | request        | Can customer skip the payment step in checkout or not                  |
+-----------------------------+----------------+------------------------------------------------------------------------+
| accountVerificationRequired | request        | Whether the customer can use his account without verification          |
+-----------------------------+----------------+------------------------------------------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/channels/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
                "code": "default",
                "name": "Default",
                "taxCalculationStrategy": "order_items_based",
                "baseCurrency": "EUR",
                "defaultLocale": "en_US"
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "id": 4,
        "code": "default",
        "name": "Default",
        "createdAt": "2017-10-23T09:05:31+00:00",
        "updatedAt": "2017-10-23T09:05:31+00:00",
        "enabled": false,
        "taxCalculationStrategy": "order_items_based",
        "_links": {
            "self": {
                "href": "\/api\/v1\/channels\/default"
            }
        }
    }

If you try to create a channel without all necessary fields you will receive a ``400 Bad Request`` error.

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/channels/ \
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
                        "Please enter channel name."
                    ]
                },
                "description": {},
                "enabled": {},
                "hostname": {},
                "color": {},
                "locales": {},
                "defaultLocale": {
                    "errors": [
                        "Please enter channel default locale."
                    ]
                },
                "currencies": {},
                "defaultTaxZone": {},
                "taxCalculationStrategy": {
                    "errors": [
                        "This value should not be blank."
                    ]
                },
                "themeName": {},
                "contactEmail": {},
                "skippingShippingStepAllowed": {},
                "skippingPaymentStepAllowed": {},
                "accountVerificationRequired": {},
                "code": {
                    "errors": [
                        "Please enter channel code."
                    ]
                },
                "baseCurrency": {
                    "errors": [
                        "Please enter channel base currency."
                    ]
                }
            }
        }
    }

You can also create a channel with additional (not required) fields:

+------------------------+----------------+------------------------------------------------------------------------+
| Parameter              | Parameter type | Description                                                            |
+========================+================+========================================================================+
| description            | request        | Description of the channel                                             |
+------------------------+----------------+------------------------------------------------------------------------+
| enabled                | request        | Gives an information about channel availability                        |
+------------------------+----------------+------------------------------------------------------------------------+
| hostname               | request        | Name of the host for the channel                                       |
+------------------------+----------------+------------------------------------------------------------------------+
| color                  | request        | Allows to recognize orders made in the channel                         |
+------------------------+----------------+------------------------------------------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/channels/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
                "name": "Default",
                "code": "default",
                "taxCalculationStrategy": "order_items_based",
                "baseCurrency": "EUR",
                "defaultLocale": "en_US",
                "hostname": "newshop.com",
                "enabled": true,
                "color": "MediumBlue",
                "description": "Planned new shop channel"
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "id": 3,
        "code": "default",
        "name": "Default",
        "description": "Planned new shop channel",
        "hostname": "newshop.com",
        "color": "MediumBlue",
        "createdAt": "2017-10-23T08:39:42+00:00",
        "updatedAt": "2017-10-23T08:39:42+00:00",
        "enabled": true,
        "taxCalculationStrategy": "order_items_based",
        "_links": {
            "self": {
                "href": "\/api\/v1\/channels\/default"
            }
        }
    }

Getting a Single Channel
------------------------

To retrieve the details of a specific channel you will need to call the ``/api/v1/channels/{code}`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/channels/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Code of requested channel            |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To see the details of the channel with ``code = US_WEB`` use the below method:

.. code-block:: bash

     $ curl http://demo.sylius.com/api/v1/channels/US_WEB \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

.. note::

    The *US_WEB* code is just an example. Your value can be different.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "id": 1,
        "code": "US_WEB",
        "name": "US Web Store",
        "hostname": "localhost",
        "color": "Wheat",
        "createdAt": "2017-02-10T13:14:20+0100",
        "updatedAt": "2017-02-10T13:14:20+0100",
        "enabled": true,
        "taxCalculationStrategy": "order_items_based",
        "_links": {
            "self": {
                "href": "\/api\/v1\/channels\/US_WEB"
            }
        }
    }

Collection of Channels
-----------------------

To retrieve a paginated list of channels you will need to call the ``/api/v1/channels/`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/channels/

+---------------+----------------+-------------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                       |
+===============+================+===================================================================+
| Authorization | header         | Token received during authentication                              |
+---------------+----------------+-------------------------------------------------------------------+
| page          | query          | *(optional)* Number of the page, by default = 1                   |
+---------------+----------------+-------------------------------------------------------------------+
| paginate      | query          | *(optional)* Number of items to display per page, by default = 10 |
+---------------+----------------+-------------------------------------------------------------------+

To see the first page of all channels use the below method:

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/channels/ \
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
                "href": "\/api\/v1\/channels\/?page=1&limit=10"
            },
            "first": {
                "href": "\/api\/v1\/channels\/?page=1&limit=10"
            },
            "last": {
                "href": "\/api\/v1\/channels\/?page=1&limit=10"
            }
        },
        "_embedded": {
            "items": [
                {
                    "id": 1,
                    "code": "US_WEB",
                    "name": "US Web Store",
                    "enabled": true,
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/channels\/US_WEB"
                        }
                    }
                },
                {
                    "id": 2,
                    "code": "default",
                    "name": "Default Channel",
                    "enabled": false,
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/channels\/default"
                        }
                    }
                }
            ]
        }
    }

Deleting a Channel
------------------

To delete a channel you will need to call the ``/api/v1/channels/{code}`` endpoint with the ``DELETE`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    DELETE /api/v1/channels/{code}

+---------------+----------------+-------------------------------------------+
| Parameter     | Parameter type | Description                               |
+===============+================+===========================================+
| Authorization | header         | Token received during authentication      |
+---------------+----------------+-------------------------------------------+
| code          | url attribute  | Code of the removed channel               |
+---------------+----------------+-------------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/channels/PL \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X DELETE

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content