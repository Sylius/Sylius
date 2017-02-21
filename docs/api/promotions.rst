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

+-------------+-------------------------------------------------------------+
| Field       | Description                                                 |
+=============+=============================================================+
| id          | Id of the promotion                                         |
+-------------+-------------------------------------------------------------+
| code        | Unique promotion identifier                                 |
+-------------+-------------------------------------------------------------+
| name        | The name of the promotion                                   |
+-------------+-------------------------------------------------------------+
| startsAt    | Start date                                                  |
+-------------+-------------------------------------------------------------+
| endsAt      | End date                                                    |
+-------------+-------------------------------------------------------------+
| usageLimit  | Promotion's usage limit                                     |
+-------------+-------------------------------------------------------------+
| used        | Number of times this promotion has been used                |
+-------------+-------------------------------------------------------------+
| priority    | When exclusive, promotion with top priority will be applied |
+-------------+-------------------------------------------------------------+
| couponBased | Whether this promotion is triggered by a coupon             |
+-------------+-------------------------------------------------------------+
| exclusive   | Cannot be applied together with other promotions            |
+-------------+-------------------------------------------------------------+
| rules       | Associated rules                                            |
+-------------+-------------------------------------------------------------+
| actions     | Associated actions                                          |
+-------------+-------------------------------------------------------------+
| createdAt   | Date of creation                                            |
+-------------+-------------------------------------------------------------+
| updatedAt   | Date of last update                                         |
+-------------+-------------------------------------------------------------+
| channels    | Collection of channels in which the promotion is available  |
+-------------+-------------------------------------------------------------+

.. note::

    Read more about :doc:`Promotion in the component docs</components/Promotion/models>`.

Getting a Single Promotion
--------------------------

To retrieve the details of the promotion you will need to call the ``/api/v1/promotions/code`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/promotions/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Code of requested promotion  |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To see the details for the the promotion with ``code = christmas`` use the below method:

.. code-block:: bash

     $ curl http://demo.sylius.org/api/v1/promotions/christmas \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

.. note::

    *christmas* is just an example. Your value can be different.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

     STATUS: 200 OK

.. code-block:: json

    {
        "id": 1,
        "code": "christmas",
        "name": "Christmas",
        "priority": 0,
        "exclusive": false,
        "used": 19,
        "coupon_based": false,
        "rules": [
            {
                "id": 1,
                "type": "cart_quantity",
                "configuration": {
                    "count": 3
                }
            }
        ],
        "actions": [
            {
                "id": 1,
                "type": "order_percentage_discount",
                "configuration": {
                    "percentage": 0.05
                }
            }
        ],
        "created_at": "2017-02-17T15:01:15+0100",
        "updated_at": "2017-02-17T15:01:40+0100",
        "channels": [
            {
                "id": 1,
                "code": "US_WEB",
                "name": "US Web Store",
                "hostname": "localhost",
                "color": "Khaki",
                "created_at": "2017-02-17T15:01:14+0100",
                "updated_at": "2017-02-17T15:01:14+0100",
                "enabled": true,
                "tax_calculation_strategy": "order_items_based",
                "_links": {
                    "self": {
                        "href": "\/api\/v1\/channels\/US_WEB"
                    }
                }
            }
        ],
        "_links": {
            "self": {
                "href": "\/api\/v1\/promotions\/christmas"
            },
            "coupons": {
                "href": "\/api\/v1\/promotions\/christmas\/coupons\/"
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

+---------------+----------------+-------------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                       |
+===============+================+===================================================================+
| Authorization | header         | Token received during authentication                              |
+---------------+----------------+-------------------------------------------------------------------+
| page          | query          | *(optional)* Number of the page, by default = 1                   |
+---------------+----------------+-------------------------------------------------------------------+
| paginate      | query          | *(optional)* Number of items to display per page, by default = 10 |
+---------------+----------------+-------------------------------------------------------------------+

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
                    "id": 1,
                    "code": "christmas",
                    "name": "Christmas",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/promotions\/christmas"
                        },
                        "coupons": {
                            "href": "\/api\/v1\/promotions\/christmas\/coupons\/"
                        }
                    }
                },
                {
                    "id": 2,
                    "code": "new_year",
                    "name": "New Year",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/promotions\/new_year"
                        },
                        "coupons": {
                            "href": "\/api\/v1\/promotions\/new_year\/coupons\/"
                        }
                    }
                }
            ]
        }
    }
