Products API
============

These endpoints will allow you to easily manage products. Base URI is `/api/v1/products`.

When you get a collection of resources, "Default" serialization group will be used and following fields will be exposed:

+----------------+----------------------------------------------------------------------------+
| Field          | Description                                                                |
+================+============================================================================+
| id             | Id of product                                                              |
+----------------+----------------------------------------------------------------------------+
| code           | Unique product identifier (for example SKU)                                |
+----------------+----------------------------------------------------------------------------+
| average_rating | Average from accepted ratings given by customer                            |
+----------------+----------------------------------------------------------------------------+
| channels       | Collection of channels to which product was assigned                       |
+----------------+----------------------------------------------------------------------------+
| translations   | Collection of translations (each contains slug and name in given language) |
+----------------+----------------------------------------------------------------------------+
| options        | Options assigned to this product                                           |
+----------------+----------------------------------------------------------------------------+
| images         | Images assigned to product                                                 |
+----------------+----------------------------------------------------------------------------+

If you request for a more detailed data, you will receive an object with following fields:

+-----------------+----------------------------------------------------------------------------+
| Field           | Description                                                                |
+=================+============================================================================+
| id              | Id of product                                                              |
+-----------------+----------------------------------------------------------------------------+
| code            | Unique product identifier                                                  |
+-----------------+----------------------------------------------------------------------------+
| average_rating  | Average from ratings given by customer                                     |
+-----------------+----------------------------------------------------------------------------+
| channels        | Collection of channels to which product was assigned                       |
+-----------------+----------------------------------------------------------------------------+
| translations    | Collection of translations (each contains slug and name in given language) |
+-----------------+----------------------------------------------------------------------------+
| attributes      | Collection of attributes connected with product (for example material)     |
+-----------------+----------------------------------------------------------------------------+
| associations    | Collection of products associated with created product                     |
|                 | (for example accessories to this product)                                  |
+-----------------+----------------------------------------------------------------------------+
| variants        | Collection of variants connected with product                              |
+-----------------+----------------------------------------------------------------------------+
| reviews         | Collection of reviews passed by customers                                  |
+-----------------+----------------------------------------------------------------------------+
| available_on    | When the product is available                                              |
+-----------------+----------------------------------------------------------------------------+
| available_until | Till when the product is available                                         |
+-----------------+----------------------------------------------------------------------------+
| product_taxons  | Collection of relations between product and taxons                         |
+-----------------+----------------------------------------------------------------------------+
| main_taxon      | The main taxon to whose product is assigned                                |
+-----------------+----------------------------------------------------------------------------+


.. note::

    Read more about `Products`__

__ http://docs.sylius.org/en/latest/components/Product/models.html#product

Collection of Products
----------------------

You can retrieve the full products list by making the following request:

Definition
..........

.. code-block:: text

    GET /api/v1/products/

+---------------------------------------+----------------+---------------------------------------------------+
| Parameter                             | Parameter type | Description                                       |
+=======================================+================+===================================================+
| Authorization                         | header         | Token received during authentication              |
+---------------------------------------+----------------+---------------------------------------------------+
| limit                                 | query          | *(optional)* Number of items to display per page, |
|                                       |                | by default = 10                                   |
+---------------------------------------+----------------+---------------------------------------------------+
| sorting['name_of_field']['direction'] | query          | *(optional)* Field and direction of sorting,      |
|                                       |                | by default 'desc' and 'createdAt'                 |
+---------------------------------------+----------------+---------------------------------------------------+


Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/v1/products/ \
        -H "Authorization: Bearer SampleToken" \
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
         "total": 1,
         "_links": {
             "self": {
                 "href": "/api/v1/products/?page=1&limit=10"
             },
             "first": {
                 "href": "/api/v1/products/?page=1&limit=10"
             },
             "last": {
                 "href": "/api/v1/products/?page=6&limit=10"
             },
             "next": {
                 "href": "/api/v1/products/?page=2&limit=10"
             }
         },
         "_embedded": {
             "items": [
                 {
                     "name": "Mug \"earum\"",
                     "id": 2,
                     "code": "d6e6efaf-f3ef-34cf-86b3-646586aa62ef",
                     "options": [
                         {
                             "code": "mug_type"
                         }
                     ],
                     "average_rating": 2,
                     "images": [
                         {
                             "id": 3,
                             "code": "main",
                             "path": "af/ae/88f740736b8b79696513a5fe9c31.jpeg"
                         },
                         {
                             "id": 4,
                             "code": "thumbnail",
                             "path": "71/8d/9dd518beda0571b133dbdf7f5d0a.jpeg"
                         }
                     ],
                     "_links": {
                         "self": {
                             "href": "/api/v1/products/2"
                         }
                     }
                 }
             ]
         }
     }

Getting a Single Product
------------------------

You can request detailed product information by executing the following request:

Definition
..........

.. code-block:: text

    GET /api/v1/products/{id}

+---------------+----------------+-------------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                       |
+===============+================+===================================================================+
| Authorization | header         | Token received during authentication                              |
+---------------+----------------+-------------------------------------------------------------------+
| id            | url attribute  | Id of requested resource                                          |
+---------------+----------------+-------------------------------------------------------------------+

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/v1/products/2 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "id": 2,
        "name": "Mug \"earum\"",
        "code": "d6e6efaf-f3ef-34cf-86b3-646586aa62ef",
        "available_on": "2017-01-18T10:32:17+0100",
        "attributes": [
            {
                "code": "mug_material",
                "name": "Mug material",
                "value": "Invisible porcelain",
                "id": 2
            }
        ],
        "variants": [
            {
                "id": 4,
                "on_hold": 0,
                "tracked": false
            }
        ],
        "options": [
            {
                "code": "mug_type"
            }
        ],
        "translations": {
            "en_US": {
                "locale": "en_US",
                "id": 2,
                "name": "Mug \"earum\"",
                "slug": "mug-earum",
                "description": "Et qui neque at sit voluptate sint omnis. Quos assumenda magni eos nemo qui accusamus.",
                "short_description": "Molestiae quaerat in voluptate."
            }
        },
        "product_taxons": [
            {
                "id": 2,
                "position": 1
            }
        ],
        "main_taxon": {
            "name": "Mugs",
            "id": 2,
            "code": "mugs",
            "children": []
        },
        "reviews": [
            {
                "id": 41,
                "title": "Nice",
                "rating": 2,
                "comment": "Nice",
                "author": {
                    "id": 22,
                    "email": "banana@exmp.com",
                    "email_canonical": "banana@exmp.com",
                    "gender": "u"
                },
                "status": "new",
                "created_at": "2017-01-18T11:15:44+0100",
                "updated_at": "2017-01-18T11:15:45+0100"
            }
        ],
        "average_rating": 2,
        "images": [
            {
                "id": 3,
                "code": "main",
                "path": "af/ae/88f740736b8b79696513a5fe9c31.jpeg"
            }
        ],
        "_links": {
            "self": {
                "href": "/api/v1/products/2"
            }
        }
    }

Creating Product
----------------

Definition
..........

.. code-block:: text

    POST /api/v1/products/

+------------------------------------+----------------+--------------------------------------+
| Parameter                          | Parameter type | Description                          |
+====================================+================+======================================+
| Authorization                      | header         | Token received during authentication |
+------------------------------------+----------------+--------------------------------------+
| code                               | request        | **(unique)** Product identifier      |
+------------------------------------+----------------+--------------------------------------+
|translations['locale_code']['name'] | request        | Name of the product                  |
+------------------------------------+----------------+--------------------------------------+
|translations['locale_code']['slug'] | request        | **(unique)** Slug                    |
+------------------------------------+----------------+--------------------------------------+

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/v1/products/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
                "translations": {
                    "en__US": {
                        "name": "Truck Simulator",
                        "slug": "truck-simulator"
                    }
                },
                "code": "TS3"
            }
        '

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "id": 61,
        "name": "Truck Simulator",
        "code": "TS3",
        "available_on": "2017-01-18T14:05:52+0100",
        "attributes": [],
        "variants": [],
        "options": [],
        "translations": {
            "en_US": {
                "locale": "en_US",
                "id": 61,
                "name": "Truck Simulator",
                "slug": "truck-simulator"
            }
        },
        "product_taxons": [],
        "reviews": [],
        "average_rating": 0,
        "images": [],
        "_links": {
            "self": {
                "href": "/api/v1/products/61"
            }
        }
    }

If you try to create a resource without name, code or slug, you will receive a 400 error.

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/v1/products/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X POST

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 400 Bad Request

.. code-block:: json

    {
        "code": 400,
        "message": "Validation Failed",
        "errors": {
            "children": {
                "enabled": {},
                "translations": {
                    "children": {
                        "en_US": {
                            "children": {
                                "name": {
                                    "errors": [
                                        "Please enter product name."
                                    ]
                                },
                                "slug": {
                                    "errors": [
                                        "Please enter product slug."
                                    ]
                                },
                                "description": {},
                                "metaKeywords": {},
                                "metaDescription": {},
                                "shortDescription": {}
                            }
                        }
                    }
                },
                "attributes": {},
                "associations": {
                    "children": {
                        "similar_products": {}
                    }
                },
                "channels": {
                    "children": [
                        {}
                    ]
                },
                "mainTaxon": {},
                "productTaxons": {},
                "images": {},
                "code": {
                    "errors": [
                        "Please enter product code."
                    ]
                },
                "options": {}
            }
        }
    }

You can also create a product with additional (not required) fields, like:

+----------------+-----------------------------------------------------------------------------+
| Field          | Description                                                                 |
+================+=============================================================================+
| channels       | Collection of channels to which product was assigned                        |
+----------------+-----------------------------------------------------------------------------+
| translations   | Collection of translations (each contains slug and name in given language). |
|                | Only the translation for default locale is required, the rest are optional  |
+----------------+-----------------------------------------------------------------------------+
| options        | Options assigned to product                                                 |
+----------------+-----------------------------------------------------------------------------+
| images         | Images assigned to product                                                  |
+----------------+-----------------------------------------------------------------------------+
| attributes     | Collection of attributes connected with product (for example material)      |
+----------------+-----------------------------------------------------------------------------+
| associations   | Collection of products associated with created product                      |
|                | (for example accessories to this product)                                   |
+----------------+-----------------------------------------------------------------------------+
| product_taxons | Collection of relations between product and taxons                          |
+----------------+-----------------------------------------------------------------------------+
| main_taxon     | The main taxon to whose product is assigned                                 |
+----------------+-----------------------------------------------------------------------------+

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/v1/products/
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng"
        -H “Accept: application/json”
        -X POST
        --data '
            {
                "code": "MUG_TH",
                "main_taxon": "mugs",
                "product_taxons": "category,mugs",
                "channels": [
                    "US_WEB"
                ],
                "attributes": [
                     {
                         "attribute": "mug_material",
                         "locale_code": "en_US",
                         "value": "concrete"
                     }
                 ],
                "options": [
                    "mug_type"
                ],
                 "associations": {
                     "accessories": "f1fd2fab-c024-3192-9505-dfc8f2aef872,f1fd2fab-c024-3192-9505-dfc8f2aef872"
                 },
                "translations": {
                    "en__US": {
                        "name": "Theme Mug",
                        "slug": "theme-mug"
                    },
                    "pl": {
                        "name": "Kubek z motywem",
                        "slug": "kubek-z-motywem"
                    }
                }
            }
        '

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "name": "Theme Mug",
        "id": 129,
        "code": "MUG_TH",
        "available_on": "2017-01-25T14:03:37+0100",
        "attributes": [
            {
                "code": "mug_material",
                "name": "Mug material",
                "value": "concrete",
                "type": "text",
                "id": 275
            }
        ],
        "variants": [],
        "options": [
            {
                "code": "mug_type"
            }
        ],
        "associations": [
            {
                "id": 26,
                "type": {
                    "id": 3,
                    "code": "accessories",
                    "created_at": "2017-01-25T11:51:39+0100",
                    "updated_at": "2017-01-25T11:51:39+0100",
                    "translations": [
                        {
                            "locale": "en_US",
                            "id": 3,
                            "name": "Accessories"
                        }
                    ],
                    "current_locale": "en_US",
                    "fallback_locale": "en_US"
                },
                "associated_products": [
                    {
                        "name": "Mug \"molestias\"",
                        "id": 61,
                        "code": "f1fd2fab-c024-3192-9505-dfc8f2aef872",
                        "available_on": "2017-01-20T14:52:03+0100",
                        "attributes": [
                            {
                                "code": "mug_material",
                                "name": "Mug material",
                                "value": "Banana skin",
                                "type": "text",
                                "id": 136
                            }
                        ],
                        "variants": [
                            {
                                "id": 331,
                                "on_hold": 0,
                                "tracked": false
                            },
                            {
                                "id": 332,
                                "on_hold": 0,
                                "tracked": false
                            },
                            {
                                "id": 333,
                                "on_hold": 0,
                                "tracked": false
                            }
                        ],
                        "options": [
                            {
                                "code": "mug_type"
                            }
                        ],
                        "associations": [],
                        "translations": {
                            "en_US": {
                                "locale": "en_US",
                                "id": 61,
                                "name": "Mug \"molestias\"",
                                "slug": "mug-molestias",
                                "description": "Aut non quos esse ut non. Ducimus cumque ut libero molestiae velit.",
                                "short_description": "Odio aliquam voluptatem sed consequatur."
                            }
                        },
                        "product_taxons": [
                            {
                                "id": 79,
                                "position": 0
                            }
                        ],
                        "main_taxon": {
                            "name": "Mugs",
                            "id": 9,
                            "code": "mugs",
                            "children": []
                        },
                        "reviews": [],
                        "average_rating": 0,
                        "images": [
                            {
                                "id": 121,
                                "code": "main",
                                "path": "88/63/409aa25d19ffeb598978850bbadf.jpeg"
                            },
                            {
                                "id": 122,
                                "code": "thumbnail",
                                "path": "55/ef/9e30653e7cfae268ebed7ba3b099.jpeg"
                            }
                        ],
                        "_links": {
                            "self": {
                                "href": "/api/v1/products/61"
                            }
                        }
                    }
                ],
                "created_at": "2017-01-25T14:03:38+0100",
                "updated_at": "2017-01-25T14:03:38+0100"
            }
        ],
        "translations": {
            "en_US": {
                "locale": "en_US",
                "id": 130,
                "name": "Theme Mug",
                "slug": "theme-mug"
            },
            "pl": {
                "locale": "pl",
                "id": 131,
                "name": "Kubek z motywem",
                "slug": "kubek-z-motywem"
            }
        },
        "product_taxons": [
            {
                "id": 155,
                "position": 0
            },
            {
                "id": 156,
                "position": 15
            }
        ],
        "main_taxon": {
            "name": "Mugs",
            "id": 9,
            "code": "mugs",
            "children": []
        },
        "reviews": [],
        "average_rating": 0,
        "images": [],
        "_links": {
            "self": {
                "href": "/api/v1/products/129"
            }
        }
    }

.. note::

    The images should be passed in array as an attribute (files) of request. See how it is done in Sylius
    `here <https://github.com/Sylius/Sylius/blob/master/tests/Controller/ProductApiTest.php>`_.

Updating Product
----------------

You can request full or partial update of resource. For full product update, you should use PUT method.

Definition
..........

.. code-block:: text

    PUT /api/v1/products/{id}

+------------------------------------+----------------+--------------------------------------+
| Parameter                          | Parameter type | Description                          |
+====================================+================+======================================+
| Authorization                      | header         | Token received during authentication |
+------------------------------------+----------------+--------------------------------------+
| id                                 | url attribute  | Id of requested resource             |
+------------------------------------+----------------+--------------------------------------+
|translations['locale_code']['name'] | request        | Name of the product                  |
+------------------------------------+----------------+--------------------------------------+
|translations['locale_code']['slug'] | request        | **(unique)** Slug                    |
+------------------------------------+----------------+--------------------------------------+

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/v1/product/3 \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PUT \
        --data '
            {
                "translations": {
                    "en__US": {
                        "name": "nice banana",
                        "slug": "nice-banana"
                    }
                }
	        }
        '

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 204 No Content

If you try to perform full product update without all required fields specified, you will receive a 400 error.

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/v1/products/3 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X PUT

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 400 Bad Request

.. code-block:: json

    {
        "code": 400,
        "message": "Validation Failed",
        "errors": {
            "children": {
                "enabled": {},
                "translations": {
                    "children": {
                        "en_US": {
                            "children": {
                                "name": {
                                    "errors": [
                                        "Please enter product name."
                                    ]
                                },
                                "slug": {
                                    "errors": [
                                        "Please enter product slug."
                                    ]
                                },
                                "description": {},
                                "metaKeywords": {},
                                "metaDescription": {},
                                "shortDescription": {}
                            }
                        }
                    }
                },
                "attributes": {},
                "associations": {
                    "children": {
                        "similar_products": {}
                    }
                },
                "channels": {
                    "children": [
                        {}
                    ]
                },
                "mainTaxon": {},
                "productTaxons": {},
                "images": {},
                "code": {},
                "options": {}
            }
	    }
    }

In order to perform a partial update, you should use a PATCH method.

Definition
..........

.. code-block:: text

    PATCH /api/v1/products/{id}

+---------------+----------------+--------------------------------------------------------+
| Parameter     | Parameter type | Description                                            |
+===============+================+========================================================+
| Authorization | header         | Token received during authentication                   |
+---------------+----------------+--------------------------------------------------------+
| id            | url attribute  | Id of requested resource                               |
+---------------+----------------+--------------------------------------------------------+

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/v1/product/3 \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PATCH \
        --data '
            {
                "translations": {
                    "en__US": {
                        "name": "nice banana"
                    }
                }
            }
        '

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 204 No Content

Deleting Product
----------------

Definition
..........

.. code-block:: text

    DELETE /api/v1/products/{id}

+---------------+----------------+-------------------------------------------+
| Parameter     | Parameter type | Description                               |
+===============+================+===========================================+
| Authorization | header         | Token received during authentication      |
+---------------+----------------+-------------------------------------------+
| id            | url attribute  | Id of removed product                     |
+---------------+----------------+-------------------------------------------+

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/v1/products/3 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X DELETE

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 204 No Content
