Product Variants API
====================

These endpoints will allow you to easily manage product variants. Base URI is `/api/v1/products/{productId}/variants/`.

When you get a collection of resources, "Default" serialization group will be used and the following fields will be exposed:

+-------------------+---------------------------------------------------------------------------+
| Field             | Description                                                               |
+===================+===========================================================================+
| id                | Id of product variant                                                     |
+-------------------+---------------------------------------------------------------------------+
| code              | Unique product variant's identifier                                       |
+-------------------+---------------------------------------------------------------------------+
| position          | Position of variant in product                                            |
|                   | (each product can have many variants and they can be ordered by position) |
+-------------------+---------------------------------------------------------------------------+
| tracked           | The information if the variant is tracked by inventory                    |
+-------------------+---------------------------------------------------------------------------+
| channel_pricings  | Collection of prices defined for all enabled channels                     |
+-------------------+---------------------------------------------------------------------------+
| tax_category      | Tax category to which variant is assigned                                 |
+-------------------+---------------------------------------------------------------------------+
| shipping_category | Shipping category to which variant is assigned                            |
+-------------------+---------------------------------------------------------------------------+

If you request more detailed data, you will receive an object with the following fields:

+-------------------+------------------------------------------------------------------------------------------------+
| Field             | Description                                                                                    |
+===================+================================================================================================+
| id                | Id of product variant                                                                          |
+-------------------+------------------------------------------------------------------------------------------------+
| code              | Unique product variant's identifier                                                            |
+-------------------+------------------------------------------------------------------------------------------------+
| position          | Position of variant in product                                                                 |
|                   | (each product can have many variant and they can be ordered by position)                       |
+-------------------+------------------------------------------------------------------------------------------------+
| tracked           | The information if the variant is tracked by inventory                                         |
+-------------------+------------------------------------------------------------------------------------------------+
| channel_pricings  | Collection of prices defined for all enabled channels                                          |
+-------------------+------------------------------------------------------------------------------------------------+
| tax_category      | Tax category to which variant is assigned                                                      |
+-------------------+------------------------------------------------------------------------------------------------+
| shipping_category | Shipping category to which variant is assigned                                                 |
+-------------------+------------------------------------------------------------------------------------------------+
| option_values     | Collection of options in which product is available (for example: small, medium and large mug) |
+-------------------+------------------------------------------------------------------------------------------------+
| translations      | Collection of translations (each contains name in given language)                              |
+-------------------+------------------------------------------------------------------------------------------------+
| on_hold           | Information about how many product are currently reserved by customer                          |
+-------------------+------------------------------------------------------------------------------------------------+
| on_hand           | Information about the number of product in given variant currently available in shop           |
+-------------------+------------------------------------------------------------------------------------------------+
| width             | The physical width of variant                                                                  |
+-------------------+------------------------------------------------------------------------------------------------+
| height            | The physical height of variant                                                                 |
+-------------------+------------------------------------------------------------------------------------------------+
| depth             | The physical depth of variant                                                                  |
+-------------------+------------------------------------------------------------------------------------------------+
| weight            | The physical weight of variant                                                                 |
+-------------------+------------------------------------------------------------------------------------------------+

.. note::

    Read more about :doc:`Product Variants <\components\Product\models#variant>`

Collection of Product Variants
------------------------------

You can retrieve the full product variants list for selected product by making the following request:

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/products/{productId}/variants/

+---------------------------------------+----------------+----------------------------------------------------------+
| Parameter                             | Parameter type | Description                                              |
+=======================================+================+==========================================================+
| Authorization                         | header         | Token received during authentication                     |
+---------------------------------------+----------------+----------------------------------------------------------+
| productId                             | url attribute  | Id of product for which the variants should be displayed |
+---------------------------------------+----------------+----------------------------------------------------------+
| limit                                 | query          | *(optional)* Number of items to display per page,        |
|                                       |                | by default = 10                                          |
+---------------------------------------+----------------+----------------------------------------------------------+
| sorting['name_of_field']['direction'] | query          | *(optional)* Field and direction of sorting,             |
|                                       |                | by default 'desc' and 'createdAt'                        |
+---------------------------------------+----------------+----------------------------------------------------------+

Example
.......

.. code-block:: bash

    $ curl http://sylius.dev/api/v1/products/1/variants/ \
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng" \
        -H "Accept: application/json"

Example Response
~~~~~~~~~~~~~~~~

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
                "href": "/api/v1/products/1/variants/?page=1&limit=10"
            },
            "first": {
                "href": "/api/v1/products/1/variants/?page=1&limit=10"
            },
            "last": {
                "href": "/api/v1/products/1/variants/?page=1&limit=10"
            }
        },
        "_embedded": {
            "items": [
                {
                    "id": 1,
                    "code": "319bf720-e91d-36f5-aa8e-8c674e3861fb-variant-0",
                    "option_values": [
                        {
                            "name": "Mug type",
                            "code": "mug_type_medium"
                        }
                    ],
                    "position": 0,
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 1,
                            "name": "blanditiis"
                        }
                    },
                    "on_hold": 0,
                    "on_hand": 1,
                    "tracked": false,
                    "channel_pricings": [
                        {
                            "id": 1,
                            "channel": {
                                "id": 1,
                                "code": "US_WEB",
                                "name": "US Web Store",
                                "hostname": "localhost:8000",
                                "color": "Sienna",
                                "created_at": "2017-01-27T11:09:49+0100",
                                "updated_at": "2017-01-27T11:09:49+0100",
                                "enabled": true,
                                "tax_calculation_strategy": "order_items_based",
                                "_links": {
                                    "self": {
                                        "href": "/api/v1/channels/1"
                                    }
                                }
                            },
                            "price": 872
                        }
                    ],
                    "_links": {
                        "self": {
                            "href": "/api/v1/products/1/variants/1"
                        }
                    }
                },
                {
                    "id": 2,
                    "code": "319bf720-e91d-36f5-aa8e-8c674e3861fb-variant-1",
                    "option_values": [
                        {
                            "name": "Mug type",
                            "code": "mug_type_double"
                        }
                    ],
                    "position": 1,
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 2,
                            "name": "tenetur"
                        }
                    },
                    "on_hold": 0,
                    "on_hand": 0,
                    "tracked": false,
                    "channel_pricings": [
                        {
                            "id": 2,
                            "channel": {
                                "id": 1,
                                "code": "US_WEB",
                                "name": "US Web Store",
                                "hostname": "localhost:8000",
                                "color": "Sienna",
                                "created_at": "2017-01-27T11:09:49+0100",
                                "updated_at": "2017-01-27T11:09:49+0100",
                                "enabled": true,
                                "tax_calculation_strategy": "order_items_based",
                                "_links": {
                                    "self": {
                                        "href": "/api/v1/channels/1"
                                    }
                                }
                            },
                            "price": 895
                        }
                    ],
                    "_links": {
                        "self": {
                            "href": "/api/v1/products/1/variants/2"
                        }
                    }
                }
            ]
	    }
    }

Getting a Single Product Variant
--------------------------------

You can request detailed product variant information by executing the following request:

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/products/{productId}/variants/{id}

+---------------+----------------+----------------------------------------------------------+
| Parameter     | Parameter type | Description                                              |
+===============+================+==========================================================+
| Authorization | header         | Token received during authentication                     |
+---------------+----------------+----------------------------------------------------------+
| id            | url attribute  | Id of requested resource                                 |
+---------------+----------------+----------------------------------------------------------+
| productId     | url attribute  | Id of product for which the variants should be displayed |
+---------------+----------------+----------------------------------------------------------+

Example
.......

.. code-block:: bash

    $ curl http://sylius.dev/api/v1/products/1/variants/1 \
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng" \
        -H "Accept: application/json"

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "id": 1,
        "code": "319bf720-e91d-36f5-aa8e-8c674e3861fb-variant-0",
        "option_values": [
            {
                "name": "Mug type",
                "code": "mug_type_medium"
            }
        ],
        "position": 0,
        "translations": {
            "en_US": {
                "locale": "en_US",
                "id": 1,
                "name": "blanditiis"
            }
        },
        "on_hold": 0,
        "on_hand": 1,
        "tracked": false,
        "channel_pricings": [
            {
                "id": 1,
                "channel": {
                    "id": 1,
                    "code": "US_WEB",
                    "name": "US Web Store",
                    "hostname": "localhost:8000",
                    "color": "Sienna",
                    "created_at": "2017-01-27T11:09:49+0100",
                    "updated_at": "2017-01-27T11:09:49+0100",
                    "enabled": true,
                    "tax_calculation_strategy": "order_items_based",
                    "_links": {
                        "self": {
                            "href": "/api/v1/channels/1"
                        }
                    }
                },
                "price": 872
            }
        ],
        "_links": {
            "self": {
                "href": "/api/v1/products/1/variants/1"
            }
	    }
    }

Creating a Product Variant
--------------------------

Definition
^^^^^^^^^^

.. code-block:: text

    POST http://sylius.dev/api/v1/products/1/variants/

+---------------+----------------+----------------------------------------------------------+
| Parameter     | Parameter type | Description                                              |
+===============+================+==========================================================+
| Authorization | header         | Token received during authentication                     |
+---------------+----------------+----------------------------------------------------------+
| id            | url attribute  | Id of requested resource                                 |
+---------------+----------------+----------------------------------------------------------+
| productId     | url attribute  | Id of product for which the variants should be displayed |
+---------------+----------------+----------------------------------------------------------+
| code          | request        | **(unique)** Product variant identifier                  |
+---------------+----------------+----------------------------------------------------------+

Example
.......

.. code-block:: bash

    $ curl http://sylius.dev/api/v1/products/62/variants/ \
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
	            "code": "MONSTER_MUG"
            }
        '

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 201 Created

.. code-block:: json

    {
        "id": 333,
        "code": "MONSTER_MUG",
        "option_values": [],
        "position": 3,
        "translations": [],
        "on_hold": 0,
        "on_hand": 0,
        "tracked": false,
        "channel_pricings": [],
        "_links": {
            "self": {
                "href": "/api/v1/products/1/variants/333"
            }
        }
    }

.. warning::

    If you try to create a resource without code, you will receive a `400 Bad Request` error.

Example
.......

.. code-block:: bash

    $ curl http://sylius.dev/api/v1/products/1/variants/ \
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng" \
        -H "Accept: application/json" \
        -X POST

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 400 Bad Request

.. code-block:: json

    {
        "code":400,
        "message":"Validation Failed",
        "errors": {
            "children": {
                "enabled":{},
                "translations":{},
                "attributes":{},
                "associations":{},
                "channels":{},
                "mainTaxon":{},
                "productTaxons":{},
                "images":{},
                "code":{
                    "errors":["Please enter product code."]
                },
                "options":{}
            }
        }
    }

You can also create a product variant with additional (not required) fields:

+--------------------------------------+----------------+------------------------------------------------------------------------------------------------+
| Parameter                            | Parameter type | Description                                                                                    |
+======================================+================+================================================================================================+
|translations['locale_code']['name']   | request        | Name of the product variant                                                                    |
+--------------------------------------+----------------+------------------------------------------------------------------------------------------------+
| position                             | request        | Position of variant in product                                                                 |
+--------------------------------------+----------------+------------------------------------------------------------------------------------------------+
| tracked                              | request        | The information if the variant is tracked by inventory (true or false)                         |
+--------------------------------------+----------------+------------------------------------------------------------------------------------------------+
| channel_pricings                     | request        | Collection of objects which contains prices for all enabled channels                           |
+--------------------------------------+----------------+------------------------------------------------------------------------------------------------+
| tax_category                         | request        | Code of object which provides information about tax category to which variant is assigned      |
+--------------------------------------+----------------+------------------------------------------------------------------------------------------------+
| shipping_category                    | request        | Code of object which provides information about shipping category to which variant is assigned |
+--------------------------------------+----------------+------------------------------------------------------------------------------------------------+
| option_values                        | request        | Object with information about ProductOption (by code) and ProductOptionValue (by code)         |
+--------------------------------------+----------------+------------------------------------------------------------------------------------------------+
| onHand                               | request        | Information about the number of product in given variant currently available in shop           |
+--------------------------------------+----------------+------------------------------------------------------------------------------------------------+
| width                                | request        | The width of variant                                                                           |
+--------------------------------------+----------------+------------------------------------------------------------------------------------------------+
| height                               | request        | The height of variant                                                                          |
+--------------------------------------+----------------+------------------------------------------------------------------------------------------------+
| depth                                | request        | The depth of variant                                                                           |
+--------------------------------------+----------------+------------------------------------------------------------------------------------------------+
| weight                               | request        | The weight of variant                                                                          |
+--------------------------------------+----------------+------------------------------------------------------------------------------------------------+

.. warning::

    The channel must be created and enabled before the prices will be defined for they.

Example
.......

.. code-block:: bash

    $ curl http://sylius.dev/api/v1/products/62/variants/ \
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
                "code": "Small_MUG",
                "translations": {
                        "en__US": {
                            "name": "Small Mug"
                    }
                },
                "channel_pricings": [
                    {
                        "price": "1243"
                    },
                    {
                        "price": "342"
                    }
                ],
                "tracked": true,
                "on_hand": 5,
                "tax_category": "other",
                "shipping_category": "default",
                "option_values": {
                    "type": "small"
                },
                "width": 5,
                "height": 10,
                "depth": 15,
                "weight": 20
            }
        '

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 201 Created

.. code-block:: json

    {
        "id": 345,
        "code": "SMALL_MUG",
        "option_values": [
            {
                "name": "type",
                "code": "small"
            }
        ],
        "position": 1,
        "translations": {
            "en_US": {
                "locale": "en_US",
                "id": 345,
                "name": "Small Mug"
            }
        },
        "on_hold": 0,
        "on_hand": 5,
        "tracked": true,
        "weight": 20,
        "width": 5,
        "height": 10,
        "depth": 15,
        "tax_category": {
            "id": 3,
            "code": "other",
            "name": "Other",
            "description": "Vel aut quam ut libero consequuntur. Sit aut ad soluta reprehenderit vel.",
            "created_at": "2017-01-27T11:09:49+0100",
            "updated_at": "2017-01-27T11:09:49+0100",
            "_links": {
                "self": {
                    "href": "/api/v1/tax-categories/3"
                }
            }
        },
        "shipping_category": {
            "id": 1,
            "code": "default",
            "name": "Default shipping category",
            "created_at": "2017-01-31T11:16:02+0100",
            "updated_at": "2017-01-31T11:16:02+0100",
            "_links": {
                "self": {
                    "href": "/api/v1/shipping-categories/1"
                }
            }
        },
        "channel_pricings": [
            {
                "id": 353,
                "channel": {
                    "id": 1,
                    "code": "US_WEB",
                    "name": "US Web Store",
                    "hostname": "localhost:8000",
                    "color": "Sienna",
                    "created_at": "2017-01-27T11:09:49+0100",
                    "updated_at": "2017-01-27T11:09:49+0100",
                    "enabled": true,
                    "tax_calculation_strategy": "order_items_based",
                    "_links": {
                        "self": {
                            "href": "/api/v1/channels/1"
                        }
                    }
                },
                "price": 124300
            },
            {
                "id": 354,
                "channel": {
                    "id": 2,
                    "code": "asdasd",
                    "name": "dasd",
                    "hostname": "localhost:8081",
                    "created_at": "2017-01-30T11:47:15+0100",
                    "updated_at": "2017-01-30T11:47:15+0100",
                    "enabled": true,
                    "tax_calculation_strategy": "order_items_based",
                    "_links": {
                        "self": {
                            "href": "/api/v1/channels/2"
                        }
                    }
                },
                "price": 34200
            }
        ],
        "_links": {
            "self": {
                "href": "/api/v1/products/62/variants/345"
            }
        }
    }

Updating Product Variant
------------------------

You can request full or partial update of resource. For full product variant update, you should use PUT method.

Definition
^^^^^^^^^^

.. code-block:: text

    PUT /api/v1/sylius.dev/api/v1/products/{productId}/variants/{id}

+---------------+----------------+----------------------------------------------------------+
| Parameter     | Parameter type | Description                                              |
+===============+================+==========================================================+
| Authorization | header         | Token received during authentication                     |
+---------------+----------------+----------------------------------------------------------+
| id            | url attribute  | Id of requested resource                                 |
+---------------+----------------+----------------------------------------------------------+
| productId     | url attribute  | Id of product for which the variants should be displayed |
+---------------+----------------+----------------------------------------------------------+
| code          | request        | **(unique)** Product variant identifier                  |
+---------------+----------------+----------------------------------------------------------+

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/v1/products/63/variants/342 \
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng" \
        -H "Content-Type: application/json" \
        -X PUT \
        --data '
            {
                "translations":{
                    "en__US": {
                        "name": "Small mug"
                    }
                },
                "channel_pricings": [
                    {
                        "price": "1243"
                    },
                    {
                        "price": "342"
                    }
                ],
                "tracked": true,
                "on_hand": 5,
                "tax_category": "other",
                "shipping_category": "default",
                "width": 5,
                "height": 10,
                "depth": 15,
                "weight": 20,
                "option_values": {
                    "type" :"small"
                }
            }
        '

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 204 No Content

In order to perform a partial update, you should use a PATCH method.

Definition
^^^^^^^^^^

.. code-block:: text

    PATCH /api/v1/sylius.dev/api/v1/products/{productId}/variants/{id}

+-------------------------------------+----------------+----------------------------------------------------------+
| Parameter                           | Parameter type | Description                                              |
+=====================================+================+==========================================================+
| Authorization                       | header         | Token received during authentication                     |
+-------------------------------------+----------------+----------------------------------------------------------+
| id                                  | url attribute  | Id of requested resource                                 |
+-------------------------------------+----------------+----------------------------------------------------------+
| productId                           | url attribute  | Id of product for which the variants should be displayed |
+-------------------------------------+----------------+----------------------------------------------------------+
| translations['locale_code']['name'] | request        | Name of product variant                                  |
+-------------------------------------+----------------+----------------------------------------------------------+

Example
.......

.. code-block:: bash

    $ curl http://sylius.dev/api/v1/products/62/variants/342 \
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng" \
        -H "Content-Type: application/json" \
        -X PATCH \
        --data '
            {
                "translations": {
                    "de": {
                        "name": "Monsterbecher"
                    }
                }
            }
        '

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 204 No Content

Deleting Product Variant
------------------------

Definition
^^^^^^^^^^

.. code-block:: text

    DELETE /api/v1/sylius.dev/api/v1/products/{productId}/variants/{id}

+-------------------------------------+----------------+----------------------------------------------------------+
| Parameter                           | Parameter type | Description                                              |
+=====================================+================+==========================================================+
| Authorization                       | header         | Token received during authentication                     |
+-------------------------------------+----------------+----------------------------------------------------------+
| id                                  | url attribute  | Id of requested resource                                 |
+-------------------------------------+----------------+----------------------------------------------------------+
| productId                           | url attribute  | Id of product for which the variants should be displayed |
+-------------------------------------+----------------+----------------------------------------------------------+

Example
.......

.. code-block:: bash

    $ curl http://sylius.dev/api/v1/products/1/variants/333 \
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng" \
        -H "Accept: application/json" \
        -X DELETE

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 204 No Content
