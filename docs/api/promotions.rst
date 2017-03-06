Promotions API
==============

These endpoints will allow you to easily manage promotions. Base URI is `/api/v1/promotions`.

Promotion structure
-------------------

Promotion API response structure
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

If you request a promotion via API, you will receive an object with the following fields:

+-------+-----------------------------+
| Field | Description                 |
+=======+=============================+
| id    | Id of the promotion         |
+-------+-----------------------------+
| code  | Unique promotion identifier |
+-------+-----------------------------+
| name  | The name of the promotion   |
+-------+-----------------------------+

If you request for more detailed data, you will receive an object with the following fields:

+-------------+--------------------------------------------------------------------------+
| Field       | Description                                                              |
+=============+==========================================================================+
| id          | Id of the promotion                                                      |
+-------------+--------------------------------------------------------------------------+
| code        | Unique promotion identifier                                              |
+-------------+--------------------------------------------------------------------------+
| name        | The name of the promotion                                                |
+-------------+--------------------------------------------------------------------------+
| startsAt    | Start date                                                               |
+-------------+--------------------------------------------------------------------------+
| endsAt      | End date                                                                 |
+-------------+--------------------------------------------------------------------------+
| usageLimit  | Promotion's usage limit                                                  |
+-------------+--------------------------------------------------------------------------+
| used        | Number of times this promotion has been used                             |
+-------------+--------------------------------------------------------------------------+
| priority    | When exclusive, promotion with top priority will be applied              |
+-------------+--------------------------------------------------------------------------+
| couponBased | Whether this promotion is triggered by a coupon                          |
+-------------+--------------------------------------------------------------------------+
| exclusive   | When true the promotion cannot be applied together with other promotions |
+-------------+--------------------------------------------------------------------------+
| rules       | Associated rules                                                         |
+-------------+--------------------------------------------------------------------------+
| actions     | Associated actions                                                       |
+-------------+--------------------------------------------------------------------------+
| createdAt   | Date of creation                                                         |
+-------------+--------------------------------------------------------------------------+
| updatedAt   | Date of last update                                                      |
+-------------+--------------------------------------------------------------------------+
| channels    | Collection of channels in which the promotion is available               |
+-------------+--------------------------------------------------------------------------+

.. note::

    Read more about :doc:`Promotions in the component docs</components/Promotion/models>`.

Creating a Promotion
---------------------

To create a new promotion you will need to call the ``/api/v1/promotions/`` endpoint with the ``POST`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    POST /api/v1/promotions/

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | request        | **(unique)** Promotion identifier    |
+---------------+----------------+--------------------------------------+
| name          | request        | Name of the promotion                |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To create a new promotion use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/promotions/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
                "code": "sd-promo",
                "name": "Sunday promotion"
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "id": 6,
        "code": "sd-promo",
        "name": "Sunday promotion",
        "priority": 4,
        "exclusive": false,
        "used": 0,
        "couponBased": false,
        "rules": [],
        "actions": [],
        "createdAt": "2017-02-28T12:05:12+0100",
        "updatedAt": "2017-02-28T12:05:13+0100",
        "channels": [],
        "_links": {
            "self": {
                "href": "\/api\/v1\/promotions\/sd-promo"
            }
        }
    }


.. warning::

    If you try to create a promotion without name or code, you will receive a ``400 Bad Request`` error, that will contain validation errors.

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/promotions/ \
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
                        "Please enter promotion name."
                    ]
                },
                "description": {},
                "exclusive": {},
                "usageLimit": {},
                "startsAt": {
                    "children": {
                        "date": {},
                        "time": {}
                    }
                },
                "endsAt": {
                    "children": {
                        "date": {},
                        "time": {}
                    }
                },
                "priority": {},
                "couponBased": {},
                "rules": {},
                "actions": {},
                "channels": {
                    "children": [
                        {},
                        {}
                    ]
                },
                "code": {
                    "errors": [
                        "Please enter promotion code."
                    ]
                }
            }
        }
    }

You can also create a promotion with additional (not required) fields:

+---------------+----------------+--------------------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                              |
+===============+================+==========================================================================+
| startsAt      | request        | Object with date and time fields                                         |
+---------------+----------------+--------------------------------------------------------------------------+
| endsAt        | request        | Object with date and time fields                                         |
+---------------+----------------+--------------------------------------------------------------------------+
| usageLimit    | request        | Promotion's usage limit                                                  |
+---------------+----------------+--------------------------------------------------------------------------+
| used          | request        | Number of times this promotion has been used                             |
+---------------+----------------+--------------------------------------------------------------------------+
| priority      | request        | When exclusive, promotion with top priority will be applied              |
+---------------+----------------+--------------------------------------------------------------------------+
| couponBased   | request        | Whether this promotion is triggered by a coupon                          |
+---------------+----------------+--------------------------------------------------------------------------+
| exclusive     | request        | When true the promotion cannot be applied together with other promotions |
+---------------+----------------+--------------------------------------------------------------------------+
| rules         | request        | Collection of rules which determines when the promotion will be applied  |
+---------------+----------------+--------------------------------------------------------------------------+
| actions       | request        | Collections of actions which will be done when the promotion will be     |
+---------------+----------------+--------------------------------------------------------------------------+
| channels      | request        | Collection of channels in which the promotion is available               |
+---------------+----------------+--------------------------------------------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/promotions/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
                "code": "christmas-promotion",
                "name": "Christmas Promotion",
                "exclusive": true,
                "priority": 0,
                "couponBased": true,
                "channels": [
                    "US_WEB"
                ],
                "startsAt": {
                "date": "2017-12-05",
                "time": "11:00"
                },
                "endsAt": {
                    "date": "2017-12-31",
                    "time": "11:00"
                },
                "rules": [
                    {
                        "type": "nth_order",
                        "configuration": {
                            "nth": 3
                        }
                    }
                ],
                "actions": [
                    {
                        "type": "order_fixed_discount",
                        "configuration": {
                            "US_WEB": {
                                "amount": 12
                            }
                        }
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
        "id": 7,
        "code": "christmas-promotion",
        "name": "Christmas Promotion",
        "priority": 0,
        "exclusive": true,
        "used": 0,
        "startsAt": "2017-12-05T11:00:00+0100",
        "endsAt": "2017-12-31T11:00:00+0100",
        "couponBased": true,
        "rules": [
            {
                "id": 3,
                "type": "nth_order",
                "configuration": {
                    "nth": 3
                }
            }
        ],
        "actions": [
            {
                "id": 3,
                "type": "order_fixed_discount",
                "configuration": {
                    "US_WEB": {
                        "amount": 1200
                    }
                }
            }
        ],
        "createdAt": "2017-03-06T11:40:38+0100",
        "updatedAt": "2017-03-06T11:40:39+0100",
        "channels": [
            {
                "id": 1,
                "code": "US_WEB",
                "name": "US Web Store",
                "hostname": "localhost",
                "color": "LawnGreen",
                "createdAt": "2017-03-06T11:20:32+0100",
                "updatedAt": "2017-03-06T11:24:37+0100",
                "enabled": true,
                "taxCalculationStrategy": "order_items_based",
                "_links": {
                    "self": {
                        "href": "\/api\/v1\/channels\/US_WEB"
                    }
                }
            }
        ],
        "_links": {
            "self": {
                "href": "\/api\/v1\/promotions\/christmas-promotion"
            },
            "coupons": {
                "href": "\/api\/v1\/promotions\/christmas-promotion\/coupons\/"
            }
        }
    }

Getting a Single Promotion
--------------------------

To retrieve the details of a promotion you will need to call the ``/api/v1/promotions/{code}`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/promotions/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Code of the requested promotion      |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To see the details of the promotion with ``code = sd-promo`` use the below method:

.. code-block:: bash

     $ curl http://demo.sylius.org/api/v1/promotions/sd-promo \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

.. note::

    The *sd-promo* code is just an example. Your value can be different.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "id": 6,
        "code": "sd-promo",
        "name": "Sunday promotion",
        "priority": 2,
        "exclusive": false,
        "used": 0,
        "couponBased": false,
        "rules": [],
        "actions": [],
        "createdAt": "2017-02-28T12:05:12+0100",
        "updatedAt": "2017-02-28T12:05:13+0100",
        "channels": [],
        "_links": {
            "self": {
                "href": "\/api\/v1\/promotions\/sd-promo"
            }
        }
    }

Collection of Promotions
------------------------

To retrieve a paginated list of promotions you will need to call the ``/api/v1/promotions/`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/promotions/

+------------------------------------------------+------------------+-------------------------------------------------------------+
| Parameter                                      | Parameter type   | Description                                                 |
+================================================+==================+=============================================================+
| Authorization                                  | header           | Token received during authentication                        |
+------------------------------------------------+------------------+-------------------------------------------------------------+
| limit                                          | query            | *(optional)* Number of items to display per page,           |
|                                                |                  | by default = 10                                             |
+------------------------------------------------+------------------+-------------------------------------------------------------+
| sorting['nameOfField']['direction']            | query            | *(optional)* Field and direction of sorting,                |
|                                                |                  | by default 'desc' and 'priority'                            |
+------------------------------------------------+------------------+-------------------------------------------------------------+
| criteria['nameOfCriterion']['searchOption']    | query            | *(optional)* Criterion, option and phrase of filtering,     |
| criteria['nameOfCriterion']['searchingPhrase'] |                  | the criteria can be for example 'couponBased' and 'search', |
|                                                |                  | option can be 'equal', 'contains'.                          |
+------------------------------------------------+------------------+-------------------------------------------------------------+

To see the first page of all promotions use the below method:

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/promotions/ \
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
                "href": "\/api\/v1\/promotions\/?page=1&limit=10"
            },
            "first": {
                "href": "\/api\/v1\/promotions\/?page=1&limit=10"
            },
            "last": {
                "href": "\/api\/v1\/promotions\/?page=1&limit=10"
            }
        },
        "_embedded": {
            "items": [
                {
                    "id": 6,
                    "code": "sd-promo",
                    "name": "Sunday promotion",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/promotions\/sd-promo"
                        }
                    }
                },
                {
                    "id": 7,
                    "code": "christmas-promotion",
                    "name": "Christmas Promotion",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/promotions\/christmas-promotion"
                        },
                        "coupons": {
                            "href": "\/api\/v1\/promotions\/christmas-promotion\/coupons\/"
                        }
                    }
                }
            ]
        }
    }

Updating a Promotion
--------------------

To fully update a promotion you will need to call the ``/api/v1/promotions/{code}`` endpoint with the ``PUT`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    PUT /api/v1/promotions/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Unique promotion identifier          |
+---------------+----------------+--------------------------------------+
| name          | request        | Name of the promotion                |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

 To fully update the promotion with ``code = christmas-promotion`` use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/promotions/christmas-promotion \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PUT \
        --data '
            {
                "name": "Christmas special promotion"
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

If you try to perform a full promotion update without all the required fields specified, you will receive a ``400 Bad Request`` error.

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/promotions/christmas-promotion \
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
                        "Please enter promotion name."
                    ]
                },
                "description": {},
                "exclusive": {},
                "usageLimit": {},
                "startsAt": {
                    "children": {
                        "date": {},
                        "time": {}
                    }
                },
                "endsAt": {
                    "children": {
                        "date": {},
                        "time": {}
                    }
                },
                "priority": {},
                "couponBased": {},
                "rules": {},
                "actions": {},
                "channels": {
                    "children": [
                        {},
                        {}
                    ]
                },
                "code": {}
            }
        }
    }

To update a promotion partially you will need to call the ``/api/v1/promotions/{code}`` endpoint with the ``PATCH`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    PATCH /api/v1/promotions/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Unique promotion identifier          |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To partially update the promotion with ``code = christmas-promotion`` use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/promotions/christmas-promotion \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PATCH \
        --data '
            {
                "exclusive": true,
                "priority": 1
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

Deleting a Promotion
--------------------

To delete a promotion you will need to call the ``/api/v1/promotions/{code}`` endpoint with the ``DELETE`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    DELETE /api/v1/promotions/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Unique promotion identifier          |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To delete the promotion with ``code = christmas-promotion`` use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/promotions/christmas-promotion \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X DELETE

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content
