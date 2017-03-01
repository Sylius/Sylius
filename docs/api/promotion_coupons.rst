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

+---------------+----------------+----------------------------------------------------------+
| Parameter     | Parameter type | Description                                              |
+===============+================+==========================================================+
| Authorization | header         | Token received during authentication                     |
+---------------+----------------+----------------------------------------------------------+
| promotionCode | url attribute  | Code of promotion for which the coupon should be created |
+---------------+----------------+----------------------------------------------------------+
| code          | request        | **(unique)** Promotion coupon identifier                 |
+---------------+----------------+----------------------------------------------------------+

Example
^^^^^^^

To create new promotion coupon for the promotion with ``code = HOLIDAY-SALE`` use the below method.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/promotions/HOLIDAY-SALE/coupons/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
                "code": "1234"
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 201 Created

.. code-block:: json

    {
        "id": 300,
        "code": "1234",
        "used": 0,
        "createdAt": "@string@.isDateTime()",
        "updatedAt": "@string@.isDateTime()",
        "_links": {
            "self": {
                "href": "\/api\/v1\/promotions\/HOLIDAY-SALE\/coupons\/1234"
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
        -H "Accept: application/json" \
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

+-----------------------+----------------+------------------------------------------------------------------------+
| Parameter             | Parameter type | Description                                                            |
+=======================+================+========================================================================+
| Authorization         | header         | Token received during authentication                                   |
+-----------------------+----------------+------------------------------------------------------------------------+
| promotionCode         | url attribute  | Code of promotion for which the coupon should be created               |
+-----------------------+----------------+------------------------------------------------------------------------+
| code                  | request        | **(unique)** Promotion coupon identifier                               |
+-----------------------+----------------+------------------------------------------------------------------------+
| usageLimit            | request        | The information how many times the coupon can be used                  |
+---------------------- +----------------+------------------------------------------------------------------------+
| perCustomerUsageLimit | request        | The information how many times the coupon can be used by one customer  |
+-----------------------+----------------+------------------------------------------------------------------------+
| expiresAt             | request        | The information when does the coupon expire                            |
+-----------------------+----------------+------------------------------------------------------------------------+

.. warning::

    Channels must be created and enabled before the prices will be defined for they.

Example
^^^^^^^

Here is an example of creating a promotion coupon with additional data for the promotion with ``code = MUG-TH``.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/promotions/MUG-TH/coupons/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
             {
                "code": "1234",
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
        "id": @integer@,
        "code": "1234",
        "usageLimit": 10,
        "used": 0,
        "expiresAt": "2020-01-01T00:00:00+0100",
        "createdAt": "@string@.isDateTime()",
        "updatedAt": "@string@.isDateTime()",
        "perCustomerUsageLimit": 1,
        "_links": {
            "self": {
                "href": "\/api\/v1\/promotions\/Holliday\/coupons\/1234"
            },
            "promotion": {
                "href": "\/api\/v1\/promotions\/Holliday"
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
        "id": 6,
        "code": "A3BCB",
        "usageLimit": 5,
        "used": 0,
        "expiresAt": "2017-11-12T00:00:00+0100",
        "createdAt": "2017-02-21T11:11:59+0100",
        "updatedAt": "2017-02-21T11:11:59+0100",
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

    $ curl http://demo.sylius.org/api/v1/promotions/HOLIDAY-SALE/coupons \
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
                "href": "\/api\/v1\/promotions\/HOLIDAY-SALE\/coupons\/?page=1&limit=10"
            },
            "first": {
                "href": "\/api\/v1\/promotions\/HOLIDAY-SALE\/coupons\/?page=1&limit=10"
            },
            "last": {
                "href": "\/api\/v1\/promotions\/HOLIDAY-SALE\/coupons\/?page=1&limit=10"
            }
        },
        "_embedded": {
            "items": [
                {
                    "id": 6,
                    "code": "A3BCB",
                    "usageLimit": 5,
                    "used": 0,
                    "expiresAt": "2017-11-12T00:00:00+0100",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/promotions\/HOLIDAY-SALE\/coupons\/A3BCB"
                        },
                        "promotion": {
                            "href": "\/api\/v1\/promotions\/HOLIDAY-SALE"
                        }
                    }
                },
                {
                    "id": 7,
                    "code": "C9596",
                    "usageLimit": 5,
                    "used": 0,
                    "expiresAt": "2017-11-12T00:00:00+0100",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/promotions\/HOLIDAY-SALE\/coupons\/C9596"
                        }
                    }
                },
                {
                    "id": 8,
                    "code": "53385",
                    "usageLimit": 5,
                    "used": 0,
                    "expiresAt": "2017-11-12T00:00:00+0100",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/promotions\/HOLIDAY-SALE\/coupons\/53385"
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

+-----------------------+----------------+-----------------------------------------------------------------------+
| Parameter             | Parameter type | Description                                                           |
+=======================+================+=======================================================================+
| Authorization         | header         | Token received during authentication                                  |
+-----------------------+----------------+-----------------------------------------------------------------------+
| code                  | url attribute  | Promotion coupon identifier                                           |
+-----------------------+----------------+-----------------------------------------------------------------------+
| promotionCode         | url attribute  | Code of promotion to which the coupon is assigned                     |
+-----------------------+----------------+-----------------------------------------------------------------------+
| usageLimit            | request        | The information how many times the coupon can be used                 |
+-----------------------+----------------+-----------------------------------------------------------------------+
| perCustomerUsageLimit | request        | The information how many times the coupon can be used by one customer |
+-----------------------+----------------+-----------------------------------------------------------------------+
| expiresAt             | request        | The information when does the coupon expire                           |
+-----------------------+----------------+-----------------------------------------------------------------------+

Example
^^^^^^^

To fully update the promotion coupon with ``code = ABCD`` for the promotion with ``code = HOLIDAY-SALE`` use the below method.

.. code-block:: bash

    curl http://demo.sylius.org/api/v1/promotions/HOLIDAY-SALE/coupons/ABCD \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PUT \
        --data '
            {
                "expiresAt": 2020-01-01,
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

To partially update the promotion coupon with ``code = ABCD`` for the promotion with ``code = HOLIDAY-SALE`` use the below method.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/promotions/HOLIDAY-SALE/coupons/ABCD \
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

To delete a promotion copupon you will need to call the ``/api/v1/promotions/{promotionCode}/coupons/{code}`` endpoint with the ``DELETE`` method.

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

To delete the promotion coupon with ``code = ABCD`` from the promotion with ``code = HOLIDAY-SALE`` use the below method.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/promotions/HOLIDAY-SALE/coupons/ABCD \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X DELETE

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content
