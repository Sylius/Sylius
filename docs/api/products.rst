Products API
============

These endpoints will allow you to easily manage products. Base URI is `/api/v1/products`.

Product API response structure
------------------------------

If you request a product via API, you will receive an object with the following fields:

+---------------+----------------------------------------------------------------------------+
| Field         | Description                                                                |
+===============+============================================================================+
| id            | Id of the product                                                          |
+---------------+----------------------------------------------------------------------------+
| code          | Unique product identifier (for example SKU)                                |
+---------------+----------------------------------------------------------------------------+
| averageRating | Average from accepted ratings given by customer                            |
+---------------+----------------------------------------------------------------------------+
| channels      | Collection of channels to which the product was assigned                   |
+---------------+----------------------------------------------------------------------------+
| translations  | Collection of translations (each contains slug and name in given language) |
+---------------+----------------------------------------------------------------------------+
| options       | Options assigned to the product                                            |
+---------------+----------------------------------------------------------------------------+
| images        | Images assigned to the product                                             |
+---------------+----------------------------------------------------------------------------+

If you request for more detailed data, you will receive an object with the following fields:

+---------------+----------------------------------------------------------------------------+
| Field         | Description                                                                |
+===============+============================================================================+
| id            | Id of the product                                                          |
+---------------+----------------------------------------------------------------------------+
| code          | Unique product identifier                                                  |
+---------------+----------------------------------------------------------------------------+
| averageRating | Average from ratings given by customer                                     |
+---------------+----------------------------------------------------------------------------+
| channels      | Collection of channels to which the product was assigned                   |
+---------------+----------------------------------------------------------------------------+
| translations  | Collection of translations (each contains slug and name in given language) |
+---------------+----------------------------------------------------------------------------+
| attributes    | Collection of attributes connected with the product (for example material) |
+---------------+----------------------------------------------------------------------------+
| associations  | Collection of products associated with the created product                 |
|               | (for example accessories to this product)                                  |
+---------------+----------------------------------------------------------------------------+
| variants      | Collection of variants connected with the product                          |
+---------------+----------------------------------------------------------------------------+
| reviews       | Collection of reviews passed by customers                                  |
+---------------+----------------------------------------------------------------------------+
| productTaxons | Collection of relations between product and taxons                         |
+---------------+----------------------------------------------------------------------------+
| mainTaxon     | The main taxon to whose the product is assigned                            |
+---------------+----------------------------------------------------------------------------+


.. note::

    Read more about :doc:`Product model in the component docs</components/Product/models>`.

Creating a Product
------------------

To create a new product you will need to call the ``/api/v1/products/`` endpoint with the ``POST`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    POST /api/v1/products/

+-----------------------------------+----------------+--------------------------------------+
| Parameter                         | Parameter type | Description                          |
+===================================+================+======================================+
| Authorization                     | header         | Token received during authentication |
+-----------------------------------+----------------+--------------------------------------+
| code                              | request        | **(unique)** Product identifier      |
+-----------------------------------+----------------+--------------------------------------+

Example
^^^^^^^

To create a new product use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
                "code": "TS3"
            }
    '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "id": 61,
        "code": "TS3",
        "attributes": [],
        "options": [],
        "associations": [],
        "productTaxons": [],
        "channels": [],
        "reviews": [],
        "averageRating": 0,
        "images": [],
        "_links": {
            "self": {
                "href": "\/api\/v1\/products\/TS3"
            },
            "variants": {
                "href": "\/api\/v1\/products\/TS3\/variants\/"
            }
        }
    }

.. warning::

    If you try to create a product without name, code or slug, you will receive a ``400 Bad Request`` error, that will contain validation errors.

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/ \
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
                "enabled": {},
                "translations": {},
                "attributes": {},
                "associations": {},
                "channels": {},
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

You can also create a product with additional (not required) fields:

+------------------------------------+----------------+-------------------------------------------------------------------------------+
| Parameter                          | Parameter type | Description                                                                   |
+====================================+================+===============================================================================+
| channels                           | request        | Collection of channels codes, which we want to associate with created product |
+------------------------------------+----------------+-------------------------------------------------------------------------------+
| translations['localeCode']['name'] | request        | Name of the product                                                           |
+------------------------------------+----------------+-------------------------------------------------------------------------------+
| translations['localeCode']['slug'] | request        | **(unique)** Slug for the product                                             |
+------------------------------------+----------------+-------------------------------------------------------------------------------+
| options                            | request        | Collection of options codes, which we want to associate with created product  |
+------------------------------------+----------------+-------------------------------------------------------------------------------+
| images                             | request        | Collection of images types, which we want to associate with created product   |
+------------------------------------+----------------+-------------------------------------------------------------------------------+
| attributes                         | request        | Array of attributes (each object has information about selected attribute's   |
|                                    |                | code, its value and locale in which it was defined)                           |
+------------------------------------+----------------+-------------------------------------------------------------------------------+
| associations                       | request        | Object with code of productAssociationType and string in which the codes      |
|                                    |                | of associated products was written down.                                      |
+------------------------------------+----------------+-------------------------------------------------------------------------------+
| productTaxons                      | request        | String in which the codes of taxons was written down (separated by comma)     |
+------------------------------------+----------------+-------------------------------------------------------------------------------+
| mainTaxon                          | request        | The main taxon's code to whose product is assigned                            |
+------------------------------------+----------------+-------------------------------------------------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
                "code": "MUG_TH",
                "mainTaxon": "mugs",
                "productTaxons": "mugs",
                "channels": [
                    "US_WEB"
                ],
                "attributes": [
                     {
                         "attribute": "mug_color",
                         "localeCode": "en_US",
                         "value": "yellow"
                     }
                 ],
                "options": [
                    "mug_type"
                ],
                 "associations": {
                     "similar_products": "SMM,BMM"
                 },
                "translations": {
                    "en_US": {
                        "name": "Theme Mug",
                        "slug": "theme-mug"
                    },
                    "pl": {
                        "name": "Kubek z motywem",
                        "slug": "kubek-z-motywem"
                    }
                },
                "images": [
                    {
                        "type": "ford"
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
        "name": "Theme Mug",
        "id": 69,
        "code": "MUG_TH",
        "attributes": [
            {
                "code": "mug_material",
                "name": "Mug material",
                "value": "concrete",
                "type": "text",
                "id": 155
            }
        ],
        "options": [
            {
                "id": 1,
                "code": "mug_type",
                "position": 0,
                "values": [
                    {
                        "name": "Mug type",
                        "code": "mug_type_medium"
                    },
                    {
                        "name": "Mug type",
                        "code": "mug_type_double"
                    },
                    {
                        "name": "Mug type",
                        "code": "mug_type_monster"
                    }
                ],
                "_links": {
                    "self": {
                        "href": "\/api\/v1\/products\/mug_type"
                    }
                }
            }
        ],
        "associations": [
            {
                "id": 13,
                "type": {
                    "name": "Similar products",
                    "id": 1,
                    "code": "similar_products",
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 1,
                            "name": "Similar products"
                        }
                    }
                },
                "associatedProducts": [
                    {
                        "name": "Batman mug",
                        "id": 63,
                        "code": "BMM",
                        "attributes": [],
                        "options": [],
                        "associations": [],
                        "translations": {
                            "en_US": {
                                "locale": "en_US",
                                "id": 63,
                                "name": "Batman mug",
                                "slug": "batman-mug"
                            }
                        },
                        "productTaxons": [],
                        "channels": [],
                        "reviews": [],
                        "averageRating": 0,
                        "images": [],
                        "_links": {
                            "self": {
                                "href": "\/api\/v1\/products\/BMM"
                            },
                            "variants": {
                                "href": "\/api\/v1\/products\/BMM\/variants\/"
                            }
                        }
                    },
                    {
                        "name": "Spider-Man Mug",
                        "id": 68,
                        "code": "SMM",
                        "attributes": [],
                        "options": [],
                        "associations": [],
                        "translations": {
                            "en_US": {
                                "locale": "en_US",
                                "id": 70,
                                "name": "Spider-Man Mug",
                                "slug": "spider-man-mug"
                            }
                        },
                        "productTaxons": [],
                        "channels": [],
                        "reviews": [],
                        "averageRating": 0,
                        "images": [],
                        "_links": {
                            "self": {
                                "href": "\/api\/v1\/products\/SMM"
                            },
                            "variants": {
                                "href": "\/api\/v1\/products\/SMM\/variants\/"
                            }
                        }
                    }
                ]
            }
        ],
        "translations": {
            "en_US": {
                "locale": "en_US",
                "id": 71,
                "name": "Theme Mug",
                "slug": "theme-mug"
            },
            "pl": {
                "locale": "pl",
                "id": 72,
                "name": "Kubek z motywem",
                "slug": "kubek-z-motywem"
            }
        },
        "productTaxons": [
            {
                "id": 78,
                "taxon": {
                    "name": "Mugs",
                    "id": 2,
                    "code": "mugs",
                    "root": {
                        "name": "Category",
                        "id": 1,
                        "code": "category",
                        "children": {
                            "1": {
                                "name": "T-Shirts",
                                "id": 5,
                                "code": "t_shirts",
                                "children": [],
                                "left": 4,
                                "right": 5,
                                "level": 1,
                                "position": 1,
                                "translations": [],
                                "images": [],
                                "_links": {
                                    "self": {
                                        "href": "\/api\/v1\/taxons\/t_shirts"
                                    }
                                }
                            }
                        },
                        "left": 1,
                        "right": 6,
                        "level": 0,
                        "position": 0,
                        "translations": {
                            "en_US": {
                                "locale": "en_US",
                                "id": 1,
                                "name": "Category",
                                "slug": "category",
                                "description": "Cupiditate ut esse perspiciatis. Aspernatur nihil ducimus maxime doloremque. Ut aut ad unde necessitatibus voluptatibus id in."
                            }
                        },
                        "images": [],
                        "_links": {
                            "self": {
                                "href": "\/api\/v1\/taxons\/category"
                            }
                        }
                    },
                    "parent": {
                        "name": "Category",
                        "id": 1,
                        "code": "category",
                        "children": {
                            "1": {
                                "name": "T-Shirts",
                                "id": 5,
                                "code": "t_shirts",
                                "children": [],
                                "left": 4,
                                "right": 5,
                                "level": 1,
                                "position": 1,
                                "translations": [],
                                "images": [],
                                "_links": {
                                    "self": {
                                        "href": "\/api\/v1\/taxons\/t_shirts"
                                    }
                                }
                            }
                        },
                        "left": 1,
                        "right": 6,
                        "level": 0,
                        "position": 0,
                        "translations": {
                            "en_US": {
                                "locale": "en_US",
                                "id": 1,
                                "name": "Category",
                                "slug": "category",
                                "description": "Cupiditate ut esse perspiciatis. Aspernatur nihil ducimus maxime doloremque. Ut aut ad unde necessitatibus voluptatibus id in."
                            }
                        },
                        "images": [],
                        "_links": {
                            "self": {
                                "href": "\/api\/v1\/taxons\/category"
                            }
                        }
                    },
                    "children": [],
                    "left": 2,
                    "right": 3,
                    "level": 1,
                    "position": 0,
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 2,
                            "name": "Mugs",
                            "slug": "mugs",
                            "description": "Non omnis vel impedit eaque necessitatibus et eveniet. Fugiat distinctio quos aut commodi ea minima. Et natus ratione sit aperiam a molestiae. Eligendi sed cumque deleniti unde magnam."
                        }
                    },
                    "images": [],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/taxons\/mugs"
                        }
                    }
                },
                "position": 0
            }
        ],
        "channels": [
            {
                "id": 1,
                "code": "US_WEB",
                "name": "US Web Store",
                "hostname": "localhost",
                "color": "DarkSeaGreen",
                "createdAt": "2017-02-27T09:12:16+0100",
                "updatedAt": "2017-02-27T09:12:16+0100",
                "enabled": true,
                "taxCalculationStrategy": "order_items_based",
                "_links": {
                    "self": {
                        "href": "\/api\/v1\/channels\/US_WEB"
                    }
                }
            }
        ],
        "mainTaxon": {
            "name": "Mugs",
            "id": 2,
            "code": "mugs",
            "root": {
                "name": "Category",
                "id": 1,
                "code": "category",
                "children": {
                    "1": {
                        "name": "T-Shirts",
                        "id": 5,
                        "code": "t_shirts",
                        "children": [],
                        "left": 4,
                        "right": 5,
                        "level": 1,
                        "position": 1,
                        "translations": [],
                        "images": [],
                        "_links": {
                            "self": {
                                "href": "\/api\/v1\/taxons\/t_shirts"
                            }
                        }
                    }
                },
                "left": 1,
                "right": 6,
                "level": 0,
                "position": 0,
                "translations": {
                    "en_US": {
                        "locale": "en_US",
                        "id": 1,
                        "name": "Category",
                        "slug": "category",
                        "description": "Cupiditate ut esse perspiciatis. Aspernatur nihil ducimus maxime doloremque. Ut aut ad unde necessitatibus voluptatibus id in."
                    }
                },
                "images": [],
                "_links": {
                    "self": {
                        "href": "\/api\/v1\/taxons\/category"
                    }
                }
            },
            "parent": {
                "name": "Category",
                "id": 1,
                "code": "category",
                "children": {
                    "1": {
                        "name": "T-Shirts",
                        "id": 5,
                        "code": "t_shirts",
                        "children": [],
                        "left": 4,
                        "right": 5,
                        "level": 1,
                        "position": 1,
                        "translations": [],
                        "images": [],
                        "_links": {
                            "self": {
                                "href": "\/api\/v1\/taxons\/t_shirts"
                            }
                        }
                    }
                },
                "left": 1,
                "right": 6,
                "level": 0,
                "position": 0,
                "translations": {
                    "en_US": {
                        "locale": "en_US",
                        "id": 1,
                        "name": "Category",
                        "slug": "category",
                        "description": "Cupiditate ut esse perspiciatis. Aspernatur nihil ducimus maxime doloremque. Ut aut ad unde necessitatibus voluptatibus id in."
                    }
                },
                "images": [],
                "_links": {
                    "self": {
                        "href": "\/api\/v1\/taxons\/category"
                    }
                }
            },
            "children": [],
            "left": 2,
            "right": 3,
            "level": 1,
            "position": 0,
            "translations": {
                "en_US": {
                    "locale": "en_US",
                    "id": 2,
                    "name": "Mugs",
                    "slug": "mugs",
                    "description": "Non omnis vel impedit eaque necessitatibus et eveniet. Fugiat distinctio quos aut commodi ea minima. Et natus ratione sit aperiam a molestiae. Eligendi sed cumque deleniti unde magnam."
                }
            },
            "images": [],
            "_links": {
                "self": {
                    "href": "\/api\/v1\/taxons\/mugs"
                }
            }
        },
        "reviews": [],
        "averageRating": 0,
        "images": [
            {
                "id": 121,
                "type": "ford",
                "path": "65\/f6\/1e3b25f3721768b535e5c37ac005.jpeg"
            }
        ],
        "_links": {
            "self": {
                "href": "\/api\/v1\/products\/MUG_TH"
            },
            "variants": {
                "href": "\/api\/v1\/products\/MUG_TH\/variants\/"
            }
        }
    }

.. note::

    The images (files) should be passed in an array as an attribute of request. See how it is done in Sylius
    `here <https://github.com/Sylius/Sylius/blob/master/tests/Controller/ProductApiTest.php>`_.

Getting a Single Product
------------------------

To retrieve the details of a product you will need to call the ``/api/v1/product/code`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/products/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Unique product identifier            |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To see the details for the product with ``code = BMM`` use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/BMM \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

.. note::

    The *BMM* code is an exemplary value. Your value can be different.
    Check in the list of all products if you are not sure which code should be used.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "name": "Batman mug",
        "id": 63,
        "code": "BMM",
        "attributes": [],
        "options": [],
        "associations": [],
        "translations": {
            "en_US": {
                "locale": "en_US",
                "id": 63,
                "name": "Batman mug",
                "slug": "batman-mug"
            }
        },
        "productTaxons": [],
        "channels": [],
        "reviews": [],
        "averageRating": 0,
        "images": [],
        "_links": {
            "self": {
                "href": "\/api\/v1\/products\/BMM"
            },
            "variants": {
                "href": "\/api\/v1\/products\/BMM\/variants\/"
            }
        }
    }

Collection of Products
----------------------

To retrieve a paginated list of products you will need to call the ``/api/v1/products/`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/products/

+-------------------------------------+----------------+---------------------------------------------------+
| Parameter                           | Parameter type | Description                                       |
+=====================================+================+===================================================+
| Authorization                       | header         | Token received during authentication              |
+-------------------------------------+----------------+---------------------------------------------------+
| limit                               | query          | *(optional)* Number of items to display per page, |
|                                     |                | by default = 10                                   |
+-------------------------------------+----------------+---------------------------------------------------+
| sorting['nameOfField']['direction'] | query          | *(optional)* Field and direction of sorting,      |
|                                     |                | by default 'desc' and 'createdAt'                 |
+-------------------------------------+----------------+---------------------------------------------------+

To see the first page of all products use the below method:

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/ \
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
        "pages": 16,
        "total": 63,
        "_links": {
            "self": {
                "href": "\/api\/v1\/products\/?sorting%5Bcode%5D=desc&page=1&limit=4"
            },
            "first": {
                "href": "\/api\/v1\/products\/?sorting%5Bcode%5D=desc&page=1&limit=4"
            },
            "last": {
                "href": "\/api\/v1\/products\/?sorting%5Bcode%5D=desc&page=16&limit=4"
            },
            "next": {
                "href": "\/api\/v1\/products\/?sorting%5Bcode%5D=desc&page=2&limit=4"
            }
        },
        "_embedded": {
            "items": [
                {
                    "name": "Spiderman Mug",
                    "id": 61,
                    "code": "SMM",
                    "options": [],
                    "averageRating": 0,
                    "images": [],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/products\/SMM"
                        }
                    }
                },
                {
                    "name": "Theme Mug",
                    "id": 63,
                    "code": "MUG_TH",
                    "options": [
                        {
                            "id": 1,
                            "code": "mug_type",
                            "position": 0,
                            "values": [
                                {
                                    "code": "mug_type_medium",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 1,
                                            "value": "Medium mug"
                                        }
                                    }
                                },
                                {
                                    "code": "mug_type_double",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 2,
                                            "value": "Double mug"
                                        }
                                    }
                                },
                                {
                                    "code": "mug_type_monster",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 3,
                                            "value": "Monster mug"
                                        }
                                    }
                                }
                            ],
                            "_links": {
                                "self": {
                                    "href": "\/api\/v1\/products\/mug_type"
                                }
                            }
                        }
                    ],
                    "averageRating": 0,
                    "images": [],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/products\/MUG_TH"
                        }
                    }
                },
                {
                    "name": "Sticker \"quis\"",
                    "id": 16,
                    "code": "fe06f44e-2169-328f-8cd2-cd5495b4b6ad",
                    "options": [
                        {
                            "id": 2,
                            "code": "sticker_size",
                            "position": 1,
                            "values": [
                                {
                                    "code": "sticker_size-3",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 4,
                                            "value": "3\""
                                        }
                                    }
                                },
                                {
                                    "code": "sticker_size_5",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 5,
                                            "value": "5\""
                                        }
                                    }
                                },
                                {
                                    "code": "sticker_size_7",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 6,
                                            "value": "7\""
                                        }
                                    }
                                }
                            ],
                            "_links": {
                                "self": {
                                    "href": "\/api\/v1\/products\/sticker_size"
                                }
                            }
                        }
                    ],
                    "averageRating": 0,
                    "images": [
                        {
                            "id": 31,
                            "type": "main"
                        },
                        {
                            "id": 32,
                            "type": "thumbnail"
                        }
                    ],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/products\/fe06f44e-2169-328f-8cd2-cd5495b4b6ad"
                        }
                    }
                },
                {
                    "name": "T-Shirt \"vel\"",
                    "id": 51,
                    "code": "f6858e9c-2f48-3d59-9f54-e7ac9898c0bd",
                    "options": [
                        {
                            "id": 3,
                            "code": "t_shirt_color",
                            "position": 2,
                            "values": [
                                {
                                    "code": "t_shirt_color_red",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 7,
                                            "value": "Red"
                                        }
                                    }
                                },
                                {
                                    "code": "t_shirt_color_black",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 8,
                                            "value": "Black"
                                        }
                                    }
                                },
                                {
                                    "code": "t_shirt_color_white",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 9,
                                            "value": "White"
                                        }
                                    }
                                }
                            ],
                            "_links": {
                                "self": {
                                    "href": "\/api\/v1\/products\/t_shirt_color"
                                }
                            }
                        },
                        {
                            "id": 4,
                            "code": "t_shirt_size",
                            "position": 3,
                            "values": [
                                {
                                    "code": "t_shirt_size_s",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 10,
                                            "value": "S"
                                        }
                                    }
                                },
                                {
                                    "code": "t_shirt_size_m",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 11,
                                            "value": "M"
                                        }
                                    }
                                },
                                {
                                    "code": "t_shirt_size_l",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 12,
                                            "value": "L"
                                        }
                                    }
                                },
                                {
                                    "code": "t_shirt_size_xl",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 13,
                                            "value": "XL"
                                        }
                                    }
                                },
                                {
                                    "code": "t_shirt_size_xxl",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 14,
                                            "value": "XXL"
                                        }
                                    }
                                }
                            ],
                            "_links": {
                                "self": {
                                    "href": "\/api\/v1\/products\/t_shirt_size"
                                }
                            }
                        }
                    ],
                    "averageRating": 0,
                    "images": [
                        {
                            "id": 101,
                            "type": "main"
                        },
                        {
                            "id": 102,
                            "type": "thumbnail"
                        }
                    ],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/products\/f6858e9c-2f48-3d59-9f54-e7ac9898c0bd"
                        }
                    }
                }
            ]
        }
    }

Updating a Product
------------------

To fully update a product you will need to call the ``/api/v1/products/code`` endpoint with the ``PUT`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    PUT /api/v1/products/{code}

+-----------------------------------+----------------+--------------------------------------+
| Parameter                         | Parameter type | Description                          |
+===================================+================+======================================+
| Authorization                     | header         | Token received during authentication |
+-----------------------------------+----------------+--------------------------------------+
| code                              | url attribute  | Unique product identifier            |
+-----------------------------------+----------------+--------------------------------------+
|translations['localeCode']['name'] | request        | Name of the product                  |
+-----------------------------------+----------------+--------------------------------------+
|translations['localeCode']['slug'] | request        | **(unique)** Slug                    |
+-----------------------------------+----------------+--------------------------------------+

Example
^^^^^^^

 To fully update the product with ``code = BMM`` use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/BMM \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PUT \
        --data '
            {
                "translations": {
                    "en_US": {
                        "name": "Batman mug",
                        "slug": "batman-mug"
                    }
                }
            }
    '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

To update a product partially you will need to call the ``/api/v1/products/code`` endpoint with the ``PATCH`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    PATCH /api/v1/products/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Unique product identifier            |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To partially update the product with ``code = BMM`` use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/BMM \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PATCH \
        --data '
            {
                "translations": {
                    "en_US": {
                        "name": "Batman mug"
                    }
                }
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

Deleting a Product
------------------

To delete a product you will need to call the ``/api/v1/products/code`` endpoint with the ``DELETE`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    DELETE /api/v1/products/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Unique product identifier            |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To delete the product with ``code = MUG_TH`` use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/MUG_TH \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X DELETE

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content
