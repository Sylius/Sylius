Promotions API
==============

Sylius promotion API endpoint is `/api/promotions`.

Index of all promotions
-----------------------

You can retrieve the full list of promotionns by making the following request:

.. code-block:: text

    GET /api/promotions

Parameters
~~~~~~~~~~

page
    Number of the page, by default = 1
limit
    Number of items to display per page

Response
~~~~~~~~

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "page":1,
        "limit":10,
        "pages":1,
        "total":3,
        "_links":{
            "self":{
                "href":"\/api\/promotions\/?page=1"
            },
            "first":{
                "href":"\/api\/promotions\/?page=1"
            },
            "last":{
                "href":"\/api\/promotions\/?page=12"
            },
            "next":{
                "href":"\/api\/promotions\/?page=2"
            }
        },
        "_embedded":{
            "items":[
                {
                    "_links": {
                        "coupons": {
                            "href": "/app_dev.php/api/promotions/1/coupons/"
                        },
                        "self": {
                            "href": "/app_dev.php/api/promotions/1"
                        }
                    },
                    "actions": [
                        {
                            "configuration": {
                                "amount": 500
                            },
                            "id": 1,
                            "type": "fixed_discount"
                        }
                    ],
                    "coupon_based": false,
                    "created_at": "2014-12-03T09:54:28+0000",
                    "exclusive": false,
                    "id": 1,
                    "name": "New Year",
                    "priority": 0,
                    "rules": [
                        {
                            "configuration": {
                                "count": 3
                            },
                            "id": 1,
                            "type": "item_count"
                        }
                    ],
                    "updated_at": "2014-12-03T09:54:28+0000",
                    "used": 0
                }
            ]
        }
    }

Getting a single promotion
--------------------------

You can view a single promotion by executing the following request:

.. code-block:: text

    GET /api/promotions/1

Response
~~~~~~~~

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "_links": {
            "coupons": {
                "href": "/app_dev.php/api/promotions/1/coupons/"
            },
            "self": {
                "href": "/app_dev.php/api/promotions/1"
            }
        },
        "actions": [
            {
                "configuration": {
                    "amount": 500
                },
                "id": 1,
                "type": "fixed_discount"
            }
        ],
        "coupon_based": false,
        "created_at": "2014-12-03T09:54:28+0000",
        "exclusive": false,
        "id": 1,
        "name": "New Year",
        "priority": 0,
        "rules": [
            {
                "configuration": {
                    "count": 3
                },
                "id": 1,
                "type": "item_count"
            }
        ],
        "updated_at": "2014-12-03T09:54:28+0000",
        "used": 0
    }

Deleting a promotion
--------------------

You can delete a promotion from the system by making the following DELETE call:

.. code-block:: text

    DELETE /api/promotions/1

Response
~~~~~~~~

.. code-block:: text

    STATUS: 204 NO CONTENT

Listing all coupons
-------------------

You can get the coupons associated with given promotion by performing the following request:

.. code-block:: text

    GET /api/promotions/1/coupons

Parameters
~~~~~~~~~~

page
    Number of the page, by default = 1
limit
    Number of items to display per page


Response
~~~~~~~~

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "_embedded": {
            "items": [
                {
                    "_links": {
                        "promotion": {
                            "href": "/api/promotions/1"
                        },
                        "self": {
                            "href": "/api/promotions/1/coupons/1"
                        }
                    },
                    "code": "XAETWESF",
                    "id": 1,
                    "usage_limit": 1,
                    "used": 0
                }
            ]
        },
        "_links": {
            "first": {
                "href": "/api/promotions/1/coupons/?page=1&limit=10"
            },
            "last": {
                "href": "/api/promotions/1/coupons/?page=1&limit=10"
            },
            "self": {
                "href": "/api/promotions/1/coupons/?page=1&limit=10"
            }
        },
        "limit": 10,
        "page": 1,
        "pages": 1,
        "total": 1
    }

Adding new coupon
-----------------

To create a new coupon for given promotion, you can execute the following request:

.. code-block:: text

    POST /api/promotion/1/coupons/

Parameters
~~~~~~~~~~

code
    Coupon code
usageLimit
    The number of times that coupon can be used

Response
~~~~~~~~

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "_links": {
            "promotion": {
                "href": "/api/promotions/1"
            },
            "self": {
                "href": "/api/promotions/1/coupons/2"
            }
        },
        "code": "SUPER-AWESOME-SALE",
        "id": 1,
        "usage_limit": 3,
        "used": 0
    }
