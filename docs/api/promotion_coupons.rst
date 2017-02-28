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

Getting a Single Promotion Coupon
---------------------------------

To retrieve the details of a promotion coupon you will need to call the ``/api/v1/promotions/promotionCode/coupons/code`` endpoint with the ``GET`` method.

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

To see the details of the promotion coupon with ``code = A3BCB`` which belongs to the promotion with ``code = autumnal_promo`` use the below method:

.. code-block:: bash

     $ curl http://demo.sylius.org/api/v1/promotions/autumnal_promo/coupons/A3BCB \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

.. note::

    The *A3BCB* and *autumnal_promo* codes are just examples. Your value can be different.

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
                "href": "\/api\/v1\/promotions\/autumnal_promo\/coupons\/A3BCB"
            },
            "promotion": {
                "href": "\/api\/v1\/promotions\/autumnal_promo"
            }
        }
    }

Collection of Promotion Coupons
-------------------------------

To retrieve a paginated list of promotion coupons you will need to call the ``/api/v1/promotions/promotionCode/coupons`` endpoint with the ``GET`` method.

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

To see the first page of all promotion coupons assigned to the promotion with ``code = autumnal_promo`` use the below method:

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/promotions/autumnal_promo/coupons \
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
                "href": "\/api\/v1\/promotions\/autumnal_promo\/coupons\/?page=1&limit=10"
            },
            "first": {
                "href": "\/api\/v1\/promotions\/autumnal_promo\/coupons\/?page=1&limit=10"
            },
            "last": {
                "href": "\/api\/v1\/promotions\/autumnal_promo\/coupons\/?page=1&limit=10"
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
                            "href": "\/api\/v1\/promotions\/autumnal_promo\/coupons\/A3BCB"
                        },
                        "promotion": {
                            "href": "\/api\/v1\/promotions\/autumnal_promo"
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
                            "href": "\/api\/v1\/promotions\/autumnal_promo\/coupons\/C9596"
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
                            "href": "\/api\/v1\/promotions\/autumnal_promo\/coupons\/53385"
                        }
                    }
                }
            ]
        }
    }
