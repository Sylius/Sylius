Promotion Coupons API
=====================

These endpoints will allow you to easily manage promotion coupons. Base URI is `/api/v1/promotions/{promotionCode}/coupons`.

Promotion Coupon API response structure
---------------------------------------

If you request a promotion coupon via API, you will receive an object with the following fields:

+------------+--------------------------------------------------+
| Field      | Description                                      |
+============+==================================================+
| id         | Id of the coupon                                 |
+------------+--------------------------------------------------+
| code       | Unique coupon identifier                         |
+------------+--------------------------------------------------+
| used       | Number of times this coupon has been used        |
+------------+--------------------------------------------------+
| expiresAt  | The date when the coupon will be no longer valid |
+------------+--------------------------------------------------+
| usageLimit | Number of times this coupon has been used        |
+------------+--------------------------------------------------+

If you request for more detailed data, you will receive an object with the following fields:

+-----------------------+--------------------------------------------------+
| Field                 | Description                                      |
+=======================+==================================================+
| id                    | Id of the coupon                                 |
+-----------------------+--------------------------------------------------+
| code                  | Unique coupon identifier                         |
+-----------------------+--------------------------------------------------+
| used                  | Number of times this coupon has been used        |
+-----------------------+--------------------------------------------------+
| expiresAt             | The date when the coupon will be no longer valid |
+-----------------------+--------------------------------------------------+
| usageLimit            | Number of times this coupon has been used        |
+-----------------------+--------------------------------------------------+
| createdAt             | Date of creation                                 |
+-----------------------+--------------------------------------------------+
| updatedAt             | Date of last update                              |
+-----------------------+--------------------------------------------------+
| perCustomerUsageLimit | Limit of the coupon usage by single customer     |
+-----------------------+--------------------------------------------------+

.. note::

    Read more about :doc:`Promotion Coupons in the component docs</components/Promotion/models>`.

Creating a Promotion Coupon
---------------------------

To create a new promotion coupon you will need to call the ``/api/v1/promotions/{promotionCode}/coupons/`` endpoint with the ``POST`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    POST /api/v1/promotions/{promotionCode}/coupons/

+---------------+----------------+--------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                  |
+===============+================+==============================================================+
| Authorization | header         | Token received during authentication                         |
+---------------+----------------+--------------------------------------------------------------+
| promotionCode | url attribute  | Code of the promotion for which the coupon should be created |
+---------------+----------------+--------------------------------------------------------------+
| code          | request        | **(unique)** Promotion coupon identifier                     |
+---------------+----------------+--------------------------------------------------------------+

Example
^^^^^^^

To create a new promotion coupon for the promotion with ``code = HOLIDAY-SALE`` use the below method.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/promotions/HOLIDAY-SALE/coupons/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
                "code": "A3BCB"
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 201 Created

.. code-block:: json

    {
        "id": 5,
        "code": "A3BCB",
        "used": 0,
        "createdAt": "2017-03-06T13:14:19+0100",
        "updatedAt": "2017-03-06T13:14:19+0100",
        "_links": {
            "self": {
                "href": "\/api\/v1\/promotions\/HOLIDAY-SALE\/coupons\/A3BCB"
            },
            "promotion": {
                "href": "\/api\/v1\/promotions\/HOLIDAY-SALE"
            }
        }
    }

.. warning::

    If you try to create a resource without code, you will receive a ``400 Bad Request`` error.

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/promotions/HOLIDAY-SALE/coupons/ \
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
                "usageLimit": {},
                "expiresAt": {},
                "perCustomerUsageLimit": {},
                "code": {
                    "errors": [
                        "Please enter coupon code."
                    ]
                }
            }
        }
    }

You can also create a promotion coupon with additional (not required) fields:

+-----------------------+----------------+--------------------------------------------------------------------------+
| Parameter             | Parameter type | Description                                                              |
+=======================+================+==========================================================================+
| usageLimit            | request        | The information on how many times the coupon can be used                 |
+-----------------------+----------------+--------------------------------------------------------------------------+
| perCustomerUsageLimit | request        | The information on how many times the coupon can be used by one customer |
+-----------------------+----------------+--------------------------------------------------------------------------+
| expiresAt             | request        | The information on when the coupon expires                               |
+-----------------------+----------------+--------------------------------------------------------------------------+

Example
^^^^^^^

Here is an example of creating a promotion coupon with additional data for the promotion with ``code = HOLIDAY-SALE``.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/promotions/HOLIDAY-SALE/coupons/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
             {
                "code": "A8BAB",
                "expiresAt": "2020-01-01",
                "usageLimit": 10,
                "perCustomerUsageLimit": 1
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 201 Created

.. code-block:: json

    {
        "id": 6,
        "code": "A8BAB",
        "usageLimit": 10,
        "used": 0,
        "expiresAt": "2020-01-01T00:00:00+0100",
        "createdAt": "2017-03-06T13:15:27+0100",
        "updatedAt": "2017-03-06T13:15:27+0100",
        "perCustomerUsageLimit": 1,
        "_links": {
            "self": {
                "href": "\/api\/v1\/promotions\/HOLIDAY-SALE\/coupons\/A8BAB"
            },
            "promotion": {
                "href": "\/api\/v1\/promotions\/HOLIDAY-SALE"
            }
        }
    }

Getting a Single Promotion Coupon
---------------------------------

To retrieve the details of a promotion coupon you will need to call the ``/api/v1/promotions/{promotionCode}/coupons/{code}`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/promotions/{promotionCode}/coupons/{code}

+---------------+----------------+---------------------------------------------------+
| Parameter     | Parameter type | Description                                       |
+===============+================+===================================================+
| Authorization | header         | Token received during authentication              |
+---------------+----------------+---------------------------------------------------+
| code          | url attribute  | Code of the requested coupon                      |
+---------------+----------------+---------------------------------------------------+
| promotionCode | url attribute  | Code of promotion to which the coupon is assigned |
+---------------+----------------+---------------------------------------------------+

Example
^^^^^^^

To see the details of the promotion coupon with ``code = A3BCB`` which belongs to the promotion with ``code = HOLIDAY-SALE`` use the below method:

.. code-block:: bash

     $ curl http://demo.sylius.org/api/v1/promotions/HOLIDAY-SALE/coupons/A3BCB \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

.. note::

    The *A3BCB* and *HOLIDAY-SALE* codes are just examples. Your value can be different.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

     STATUS: 200 OK

.. code-block:: json

    {
        "id": 5,
        "code": "A3BCB",
        "used": 0,
        "createdAt": "2017-03-06T13:14:19+0100",
        "updatedAt": "2017-03-06T13:14:19+0100",
        "_links": {
            "self": {
                "href": "\/api\/v1\/promotions\/HOLIDAY-SALE\/coupons\/A3BCB"
            },
            "promotion": {
                "href": "\/api\/v1\/promotions\/HOLIDAY-SALE"
            }
        }
    }

Collection of Promotion Coupons
-------------------------------

To retrieve a paginated list of promotion coupons you will need to call the ``/api/v1/promotions/{promotionCode}/coupons`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/promotions/{promotionCode}/coupons

+---------------+----------------+-------------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                       |
+===============+================+===================================================================+
| Authorization | header         | Token received during authentication                              |
+---------------+----------------+-------------------------------------------------------------------+
| promotionCode | url attribute  | Code of promotion to which the coupons are assigned               |
+---------------+----------------+-------------------------------------------------------------------+
| page          | query          | *(optional)* Number of the page, by default = 1                   |
+---------------+----------------+-------------------------------------------------------------------+
| paginate      | query          | *(optional)* Number of items to display per page, by default = 10 |
+---------------+----------------+-------------------------------------------------------------------+

To see the first page of all promotion coupons assigned to the promotion with ``code = HOLIDAY-SALE`` use the below method:

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/promotions/HOLIDAY-SALE/coupons/ \
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
                "href": "\/api\/v1\/promotions\/HOLIDAY-SALE\/coupons\/?sorting%5Bcode%5D=desc&page=1&limit=4"
            },
            "first": {
                "href": "\/api\/v1\/promotions\/HOLIDAY-SALE\/coupons\/?sorting%5Bcode%5D=desc&page=1&limit=4"
            },
            "last": {
                "href": "\/api\/v1\/promotions\/HOLIDAY-SALE\/coupons\/?sorting%5Bcode%5D=desc&page=1&limit=4"
            }
        },
        "_embedded": {
            "items": [
                {
                    "id": 5,
                    "code": "A3BCB",
                    "used": 0,
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/promotions\/HOLIDAY-SALE\/coupons\/A3BCB"
                        }
                    }
                },
                {
                    "id": 6,
                    "code": "A8BAB",
                    "usageLimit": 10,
                    "used": 0,
                    "expiresAt": "2020-01-01T00:00:00+0100",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/promotions\/HOLIDAY-SALE\/coupons\/A8BAB"
                        }
                    }
                }
            ]
        }
    }

Updating Promotion Coupon
-------------------------

To fully update a promotion coupon you will need to call the ``/api/v1/promotions/{promotionCode}/coupons/{code}`` endpoint with the ``PUT`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    PUT /api/v1/promotions/{promotionCode}/coupons/{code}

+-----------------------+----------------+--------------------------------------------------------------------------+
| Parameter             | Parameter type | Description                                                              |
+=======================+================+==========================================================================+
| Authorization         | header         | Token received during authentication                                     |
+-----------------------+----------------+--------------------------------------------------------------------------+
| code                  | url attribute  | Promotion coupon identifier                                              |
+-----------------------+----------------+--------------------------------------------------------------------------+
| promotionCode         | url attribute  | Code of the promotion to which the coupon is assigned                    |
+-----------------------+----------------+--------------------------------------------------------------------------+
| usageLimit            | request        | The information on how many times the coupon can be used                 |
+-----------------------+----------------+--------------------------------------------------------------------------+
| perCustomerUsageLimit | request        | The information on how many times the coupon can be used by one customer |
+-----------------------+----------------+--------------------------------------------------------------------------+
| expiresAt             | request        | The information on when the coupon expires                               |
+-----------------------+----------------+--------------------------------------------------------------------------+

Example
^^^^^^^

To fully update the promotion coupon with ``code = A3BCB`` for the promotion with ``code = HOLIDAY-SALE`` use the below method.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/promotions/HOLIDAY-SALE/coupons/A3BCB \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PUT \
        --data '
            {
                "expiresAt": "2020-01-01",
                "usageLimit": 30,
                "perCustomerUsageLimit": 2
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

To partially update a promotion coupon you will need to call the ``/api/v1/promotions/{promotionCode}/coupons/{code}`` endpoint with the ``PATCH`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    PATCH /api/v1/promotions/{promotionCode}/coupons/{code}

+-----------------------+----------------+----------------------------------------------------------+
| Parameter             | Parameter type | Description                                              |
+=======================+================+==========================================================+
| Authorization         | header         | Token received during authentication                     |
+-----------------------+----------------+----------------------------------------------------------+
| code                  | url attribute  | Promotion coupon identifier                              |
+-----------------------+----------------+----------------------------------------------------------+
| promotionCode         | url attribute  | Code of promotion to which the coupon is assigned        |
+-----------------------+----------------+----------------------------------------------------------+
| usageLimit            | request        | The information how many times the coupon can be used    |
+-----------------------+----------------+----------------------------------------------------------+

Example
^^^^^^^

To partially update the promotion coupon with ``code = A3BCB`` for the promotion with ``code = HOLIDAY-SALE`` use the below method.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/promotions/HOLIDAY-SALE/coupons/A3BCB \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PATCH \
        --data '
            {
                "usageLimit": 30
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

Deleting a Promotion coupon
---------------------------

To delete a promotion coupon you will need to call the ``/api/v1/promotions/{promotionCode}/coupons/{code}`` endpoint with the ``DELETE`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    DELETE /api/v1/promotions/{promotionCode}/coupons/{code}

+---------------+----------------+----------------------------------------------------------+
| Parameter     | Parameter type | Description                                              |
+===============+================+==========================================================+
| Authorization | header         | Token received during authentication                     |
+---------------+----------------+----------------------------------------------------------+
| code          | url attribute  | Promotion coupon identifier                              |
+---------------+----------------+----------------------------------------------------------+
| promotionCode | url attribute  | Code of promotion to which the coupon is assigned        |
+---------------+----------------+----------------------------------------------------------+

Example
^^^^^^^

To delete the promotion coupon with ``code = A3BCB`` from the promotion with ``code = HOLIDAY-SALE`` use the below method.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/promotions/HOLIDAY-SALE/coupons/A3BCB \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X DELETE

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content
