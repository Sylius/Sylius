Products API
============

These endpoints will allow you to easily manage products. Base URI is `/api/v1/products`.

Product structure
-----------------

Product API response structure
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

If you request a product via API, you will receive an object with the following fields:

+----------------+----------------------------------------------------------------------------+
| Field          | Description                                                                |
+================+============================================================================+
| id             | Id of the product                                                          |
+----------------+----------------------------------------------------------------------------+
| code           | Unique product identifier (for example SKU)                                |
+----------------+----------------------------------------------------------------------------+
| average_rating | Average from accepted ratings given by customer                            |
+----------------+----------------------------------------------------------------------------+
| channels       | Collection of channels to which the product was assigned                   |
+----------------+----------------------------------------------------------------------------+
| translations   | Collection of translations (each contains slug and name in given language) |
+----------------+----------------------------------------------------------------------------+
| options        | Options assigned to the product                                            |
+----------------+----------------------------------------------------------------------------+
| images         | Images assigned to the product                                             |
+----------------+----------------------------------------------------------------------------+

If you request for more detailed data, you will receive an object with the following fields:

+-----------------+----------------------------------------------------------------------------+
| Field           | Description                                                                |
+=================+============================================================================+
| id              | Id of the product                                                          |
+-----------------+----------------------------------------------------------------------------+
| code            | Unique product identifier                                                  |
+-----------------+----------------------------------------------------------------------------+
| average_rating  | Average from ratings given by customer                                     |
+-----------------+----------------------------------------------------------------------------+
| channels        | Collection of channels to which the product was assigned                   |
+-----------------+----------------------------------------------------------------------------+
| translations    | Collection of translations (each contains slug and name in given language) |
+-----------------+----------------------------------------------------------------------------+
| attributes      | Collection of attributes connected with the product (for example material) |
+-----------------+----------------------------------------------------------------------------+
| associations    | Collection of products associated with the created product                 |
|                 | (for example accessories to this product)                                  |
+-----------------+----------------------------------------------------------------------------+
| variants        | Collection of variants connected with the product                          |
+-----------------+----------------------------------------------------------------------------+
| reviews         | Collection of reviews passed by customers                                  |
+-----------------+----------------------------------------------------------------------------+
| product_taxons  | Collection of relations between product and taxons                         |
+-----------------+----------------------------------------------------------------------------+
| main_taxon      | The main taxon to whose the product is assigned                            |
+-----------------+----------------------------------------------------------------------------+


.. note::

    Read more about :doc: `Product </components/Product/models>`

Creating a Product
------------------

To create a new product you will need to call the ``/api/v1/products/`` endpoint with the ``POST`` method.

Definition
^^^^^^^^^^

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
^^^^^^^

To create new product use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/ \
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

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "id": 61,
        "name": "Truck Simulator",
        "code": "TS3",
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
.. warning::

    If you try to create a product without name, code or slug, you will receive a ``400 Bad Request`` error, that will contain validation errors.

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/ \
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

You can also create a product with additional (not required) fields:

+-------------------------------------+----------------+-----------------------------------------------------------------------------------+
| Parameter                           | Parameter type | Description                                                                       |
+=====================================+================+===================================================================================+
| channels                            | request        | Collection of channels codes, which we want to associate with created product     |
+-------------------------------------+----------------+-----------------------------------------------------------------------------------+
| translations['locale_code']['name'] | request        | Collection of translations (each contains slug and name in given locale).         |
| translations['locale_code']['slug'] |                | Only the translation for default locale is required, the rest are optional        |
+-------------------------------------+----------------+-----------------------------------------------------------------------------------+
| options                             | request        | Collection of options codes, which we want to associate with created product      |
+-------------------------------------+----------------+-----------------------------------------------------------------------------------+
| images                              | request        | Collection of images codes, which we want to associate with created product       |
+-------------------------------------+----------------+-----------------------------------------------------------------------------------+
| attributes                          | request        | Array of attributes (each object has information about selected attribute's code, |
|                                     |                | its value and locale in which it was defined)                                     |
+-------------------------------------+----------------+-----------------------------------------------------------------------------------+
| associations                        | request        | Object with code of productAssociationType and string in which the codes of       |
|                                     |                | associated products was written down.                                             |
+-------------------------------------+----------------+-----------------------------------------------------------------------------------+
| product_taxons                      | request        | String in which the codes of taxons was written down (separated by comma)         |
+-------------------------------------+----------------+-----------------------------------------------------------------------------------+
| main_taxon                          | request        | The main taxon's code to whose product is assigned                                |
+-------------------------------------+----------------+-----------------------------------------------------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X POST \
        --data '
            {
                "code": "MUG_TH",
                "main_taxon": "mugs",
                "product_taxons": "mugs",
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
                     "accessories": "f1fd2fab,f1fd2fab-c024"
                 },
                "translations": {
                    "en__US": {
                        "name": "Theme Mug",
                        "slug": "theme-mug"
                    },
                    "pl__PL": {
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
        "id": 62,
        "code": "MUG_TH",
        "attributes": [
            {
                "code": "mug_material",
                "name": "Mug material",
                "value": "concrete",
                "type": "text",
                "id": 136
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
                "id": 11,
                "type": {
                    "id": 2,
                    "code": "accessories",
                    "created_at": "2017-02-01T14:38:13+0100",
                    "updated_at": "2017-02-01T14:38:13+0100",
                    "translations": [
                        {
                            "locale": "en_US",
                            "id": 2,
                            "name": "Accessories"
                        }
                    ],
                    "current_locale": "en_US",
                    "fallback_locale": "en_US"
                },
                "associated_products": [
                    {
                        "name": "Mug \"perspiciatis\"",
                        "id": 1,
                        "code": "c67af0cf-2f5e-30a1-ba80-6be7a253b500",
                        "attributes": [
                            {
                                "code": "mug_material",
                                "name": "Mug material",
                                "value": "Banana skin",
                                "type": "text",
                                "id": 1
                            }
                        ],
                        "variants": [
                            {
                                "id": 1,
                                "on_hold": 0,
                                "tracked": false
                            },
                            {
                                "id": 2,
                                "on_hold": 0,
                                "tracked": false
                            },
                            {
                                "id": 3,
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
                                "id": 1,
                                "name": "Mug \"perspiciatis\"",
                                "slug": "mug-perspiciatis",
                                "description": " Voluptatum et rerum necessitatibus modi non vel.\n\nQuae modi cumque.",
                                "short_description": "Vitae minima ut."
                            }
                        },
                        "product_taxons": [
                            {
                                "id": 1,
                                "taxon": {
                                    "name": "Mugs",
                                    "id": 2,
                                    "code": "mugs",
                                    "children": []
                                },
                                "position": 0
                            }
                        ],
                        "main_taxon": {
                            "name": "Mugs",
                            "id": 2,
                            "code": "mugs",
                            "children": []
                        },
                        "reviews": [],
                        "average_rating": 0,
                        "images": [
                            {
                                "id": 1,
                                "code": "main",
                                "path": "2d/39/f32ac66cd2e5e69ef8a87f9490b2.jpeg"
                            },
                            {
                                "id": 2,
                                "code": "thumbnail",
                                "path": "b8/d0/c80dabb28dfc53795be8fa88444c.jpeg"
                            }
                        ],
                        "_links": {
                            "self": {
                                "href": "/api/v1/products/1"
                            }
                        }
                    },
                    {
                        "name": "Mug \"et\"",
                        "id": 2,
                        "code": "e5e45464-c35f-3c05-b3ea-4743ccafb28e",
                        "attributes": [
                            {
                                "code": "mug_material",
                                "name": "Mug material",
                                "value": "Invisible porcelain",
                                "type": "text",
                                "id": 2
                            }
                        ],
                        "variants": [
                            {
                                "id": 4,
                                "on_hold": 0,
                                "tracked": false
                            },
                            {
                                "id": 5,
                                "on_hold": 0,
                                "tracked": false
                            },
                            {
                                "id": 6,
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
                                "id": 2,
                                "name": "Mug \"et\"",
                                "slug": "mug-et",
                                "description": "Omnis perspiciatis quia aperiam magni occaecati",
                                "short_description": "Laboriosam blanditiis."
                            }
                        },
                        "product_taxons": [
                            {
                                "id": 2,
                                "taxon": {
                                    "name": "Mugs",
                                    "id": 2,
                                    "code": "mugs",
                                    "children": []
                                },
                                "position": 1
                            }
                        ],
                        "main_taxon": {
                            "name": "Mugs",
                            "id": 2,
                            "code": "mugs",
                            "children": []
                        },
                        "reviews": [],
                        "average_rating": 0,
                        "images": [
                            {
                                "id": 3,
                                "code": "main",
                                "path": "bc/93/e2986698753c469277570a416ad2.jpeg"
                            },
                            {
                                "id": 4,
                                "code": "thumbnail",
                                "path": "86/78/092031fdb34daeac17f7da621424.jpeg"
                            }
                        ],
                        "_links": {
                            "self": {
                                "href": "/api/v1/products/2"
                            }
                        }
                    }
                ],
                "created_at": "2017-02-01T14:39:29+0100",
                "updated_at": "2017-02-01T14:39:29+0100"
            }
        ],
        "translations": {
            "en_US": {
                "locale": "en_US",
                "id": 62,
                "name": "Theme Mug",
                "slug": "theme-mug"
            },
            "pl_PL": {
                "locale": "pl_PL",
                "id": 63,
                "name": "Kubek z motywem",
                "slug": "kubek-z-motywem"
            }
        },
        "product_taxons": [
            {
                "id": 76,
                "taxon": {
                    "name": "Mugs",
                    "id": 2,
                    "code": "mugs",
                    "children": []
                },
                "position": 15
            }
        ],
        "main_taxon": {
            "name": "Mugs",
            "id": 2,
            "code": "mugs",
            "children": []
        },
        "reviews": [],
        "average_rating": 0,
        "images": [
            {
                "id": 1,
                "type": "ford",
                "path": "b9/65/01cec3d87aa2b819e195331843f6.jpeg"
            }
        ],
        "_links": {
            "self": {
                "href": "/api/v1/products/62"
            }
        }
    }

.. note::

    The images should be passed in array as an attribute (files) of request. See how it is done in Sylius
    `here <https://github.com/Sylius/Sylius/blob/master/tests/Controller/ProductApiTest.php>`_.

Getting a Single Product
------------------------

To retrieve the details of the product you will need to call the ``/api/v1/product/product_id`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/products/{id}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| id            | url attribute  | Id of requested resource             |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/2 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

.. note::

    The *2* is an exemplary value. Your value can be different.
    Check in the list of all products if you are not sure which id should be used.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "id": 2,
        "name": "Mug \"earum\"",
        "code": "d6e6efaf",
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

Collection of Products
----------------------

To retrieve a paginated list of products you will need to call the ``/api/v1/products/`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

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
                     "code": "d6e6efaf",
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

Updating a Product
------------------

To fully update a product you will need to call the ``/api/v1/products/product_id`` endpoint with ``PUT`` method.

Definition
^^^^^^^^^^

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
^^^^^^^

 To full update the product with ``id = 3`` use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/3 \
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

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

If you try to perform full product update without all required fields specified, you will receive a ``400 Bad Request`` error.

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/3 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
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

To update a product partially you will need to call the ``/api/v1/products/product_id`` endpoint with the ``PATCH`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    PATCH /api/v1/products/{id}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| id            | url attribute  | Id of requested resource             |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To partial update the product with ``id = 3`` use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/3 \
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

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

Deleting a Product
------------------

To delete a product you will need to call the ``/api/v1/products/product_id`` endpoint with the ``DELETE`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    DELETE /api/v1/products/{id}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| id            | url attribute  | Id of removed product                |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/3 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X DELETE

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content
