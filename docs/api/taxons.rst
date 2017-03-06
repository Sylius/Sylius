Taxons API
==========

These endpoints will allow you to easily manage taxons. Base URI is `/api/v1/taxons`.

Taxon API response structure
----------------------------

If you request a taxon via API, you will receive an object with the following fields:

+--------------+--------------------------------------------------------------------------------------------------+
| Field        | Description                                                                                      |
+==============+==================================================================================================+
| id           | Id of the taxon                                                                                  |
+--------------+--------------------------------------------------------------------------------------------------+
| code         | Unique taxon identifier                                                                          |
+--------------+--------------------------------------------------------------------------------------------------+
| root         | The main ancestor of the taxon                                                                   |
+--------------+--------------------------------------------------------------------------------------------------+
| parent       | Parent of the taxon                                                                              |
+--------------+--------------------------------------------------------------------------------------------------+
| translations | Collection of translations (each contains slug, name and description in the respective language) |
+--------------+--------------------------------------------------------------------------------------------------+
| position     | The position of the taxon among other taxons                                                     |
+--------------+--------------------------------------------------------------------------------------------------+
| images       | Images assigned to the taxon                                                                     |
+--------------+--------------------------------------------------------------------------------------------------+

If you request for more detailed data, you will receive an object with the following fields:

+--------------+--------------------------------------------------------------------------------------------------+
| Field        | Description                                                                                      |
+==============+==================================================================================================+
| id           | Id of the taxon                                                                                  |
+--------------+--------------------------------------------------------------------------------------------------+
| code         | Unique taxon identifier                                                                          |
+--------------+--------------------------------------------------------------------------------------------------+
| root         | The main ancestor of the taxon                                                                   |
+--------------+--------------------------------------------------------------------------------------------------+
| parent       | Parent of the taxon                                                                              |
+--------------+--------------------------------------------------------------------------------------------------+
| translations | Collection of translations (each contains slug, name and description in the respective language) |
+--------------+--------------------------------------------------------------------------------------------------+
| position     | Position of the taxon among other taxons                                                         |
+--------------+--------------------------------------------------------------------------------------------------+
| images       | Images assigned to the taxon                                                                     |
+--------------+--------------------------------------------------------------------------------------------------+
| left         | Location within the whole taxonomy                                                               |
+--------------+--------------------------------------------------------------------------------------------------+
| right        | Location within the whole taxonomy                                                               |
+--------------+--------------------------------------------------------------------------------------------------+
| level        | How deep the taxon is in the tree                                                                |
+--------------+--------------------------------------------------------------------------------------------------+
| children     | Descendants of the taxon                                                                         |
+--------------+--------------------------------------------------------------------------------------------------+

.. note::

    Read more about :doc:`Taxons </components/Taxonomy/models>`.

Creating a Taxon
----------------

To create a new taxon you will need to call the ``/api/v1/taxons/`` endpoint with the ``POST`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    POST /api/v1/taxons/

+------------------------------------+----------------+--------------------------------------+
| Parameter                          | Parameter type | Description                          |
+====================================+================+======================================+
| Authorization                      | header         | Token received during authentication |
+------------------------------------+----------------+--------------------------------------+
| code                               | request        | **(unique)** Taxon identifier        |
+------------------------------------+----------------+--------------------------------------+

Example
^^^^^^^

To create new taxon use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/taxons/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
                "code": "toys"
            }
        '

.. note::

    If you want to create your taxon as a child of another taxon, you should pass also the parent taxon's code.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "id": 11,
        "code": "toys",
        "children": [],
        "left": 1,
        "right": 2,
        "level": 0,
        "position": 1,
        "translations": [],
        "images": [],
        "_links": {
            "self": {
                "href": "/api/v1/taxons/11"
            }
        }
    }

.. warning::

    If you try to create a taxon without code you will receive a ``400 Bad Request`` error, that will contain validation errors.

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/taxons/ \
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
                "translations": {},
                "images": {},
                "code": {
                    "errors": [
                        "Please enter taxon code."
                    ]
                },
                "parent": {}
            }
        }
    }

You can also create a taxon with additional (not required) fields:

+-------------------------------------------+----------------+------------------------------------+
| Parameter                                 | Parameter type | Description                        |
+===========================================+================+====================================+
| translations['localeCode']['name']        | request        | Name of the taxon                  |
+-------------------------------------------+----------------+------------------------------------+
| translations['localeCode']['slug']        | request        | **(unique)** Slug                  |
+-------------------------------------------+----------------+------------------------------------+
| translations['localeCode']['description'] | request        | Description of the taxon           |
+-------------------------------------------+----------------+------------------------------------+
| parent                                    | request        | The parent taxon's code            |
+-------------------------------------------+----------------+------------------------------------+
| images                                    | request        | Images codes assigned to the taxon |
+-------------------------------------------+----------------+------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/taxons/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
                "code":"toys",
                "translations":{
                    "en_US": {
                        "name": "Toys",
                        "slug": "category/toys",
                        "description": "Toys for boys"
                    }
                },
                "parent": "category",
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
        "name": "toys",
        "id": 9,
        "code": "toys",
        "root": {
            "name": "Category",
            "id": 1,
            "code": "category",
            "children": [
                {
                    "name": "T-Shirts",
                    "id": 5,
                    "code": "t_shirts",
                    "children": [],
                    "left": 2,
                    "right": 7,
                    "level": 1,
                    "position": 0,
                    "translations": [],
                    "images": [],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/taxons\/5"
                        }
                    }
                }
            ],
            "left": 1,
            "right": 10,
            "level": 0,
            "position": 0,
            "translations": {
                "en_US": {
                    "locale": "en_US",
                    "id": 1,
                    "name": "Category",
                    "slug": "category",
                    "description": "Consequatur illo amet aliquam."
                }
            },
            "images": [],
            "_links": {
                "self": {
                    "href": "\/api\/v1\/taxons\/1"
                }
            }
        },
        "parent": {
            "name": "Category",
            "id": 1,
            "code": "category",
            "children": [
                {
                    "name": "T-Shirts",
                    "id": 5,
                    "code": "t_shirts",
                    "children": [],
                    "left": 2,
                    "right": 7,
                    "level": 1,
                    "position": 0,
                    "translations": [],
                    "images": [],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/taxons\/5"
                        }
                    }
                }
            ],
            "left": 1,
            "right": 10,
            "level": 0,
            "position": 0,
            "translations": {
                "en_US": {
                    "locale": "en_US",
                    "id": 1,
                    "name": "Category",
                    "slug": "category",
                    "description": "Consequatur illo amet aliquam."
                }
            },
            "images": [],
            "_links": {
                "self": {
                    "href": "\/api\/v1\/taxons\/1"
                }
            }
        },
        "children": [],
        "left": 8,
        "right": 9,
        "level": 1,
        "position": 1,
        "translations": {
            "en_US": {
                "locale": "en_US",
                "id": 9,
                "name": "toys",
                "slug": "toys",
                "description": "Toys for boys"
            }
        },
        "images": [
            {
                "id": 1,
                "type": "ford",
                "path": "b9/65/01cec3d87aa2b819e195331843f6.jpeg"
            }
        ],
        "_links": {
            "self": {
                "href": "\/api\/v1\/taxons\/9"
            }
        }
    }

.. note::

    The images should be passed in array as an attribute (files) of request. See how it is done in Sylius
    `here <https://github.com/Sylius/Sylius/blob/master/tests/Controller/TaxonApiTest.php>`_.

Getting a Single Taxon
----------------------

To retrieve the details of a taxon you will need to call the ``/api/v1/taxons/{code}`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/taxons/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Identifier of the requested taxon    |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To see the details of the taxon with ``code = toys`` use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/taxons/toys \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

.. note::

    The *toys* value was taken from the previous create response. Your value can be different.
    Check in the list of all taxons if you are not sure which id should be used.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "name": "toys",
        "id": 9,
        "code": "toys",
        "root": {
            "name": "Category",
            "id": 1,
            "code": "category",
            "children": [
                {
                    "name": "T-Shirts",
                    "id": 5,
                    "code": "t_shirts",
                    "children": [],
                    "left": 2,
                    "right": 7,
                    "level": 1,
                    "position": 0,
                    "translations": [],
                    "images": [],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/taxons\/5"
                        }
                    }
                }
            ],
            "left": 1,
            "right": 10,
            "level": 0,
            "position": 0,
            "translations": {
                "en_US": {
                    "locale": "en_US",
                    "id": 1,
                    "name": "Category",
                    "slug": "category",
                    "description": "Consequatur illo amet aliquam."
                }
            },
            "images": [],
            "_links": {
                "self": {
                    "href": "\/api\/v1\/taxons\/1"
                }
            }
        },
        "parent": {
            "name": "Category",
            "id": 1,
            "code": "category",
            "children": [
                {
                    "name": "T-Shirts",
                    "id": 5,
                    "code": "t_shirts",
                    "children": [],
                    "left": 2,
                    "right": 7,
                    "level": 1,
                    "position": 0,
                    "translations": [],
                    "images": [],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/taxons\/5"
                        }
                    }
                }
            ],
            "left": 1,
            "right": 10,
            "level": 0,
            "position": 0,
            "translations": {
                "en_US": {
                    "locale": "en_US",
                    "id": 1,
                    "name": "Category",
                    "slug": "category",
                    "description": "Consequatur illo amet aliquam."
                }
            },
            "images": [],
            "_links": {
                "self": {
                    "href": "\/api\/v1\/taxons\/1"
                }
            }
        },
        "children": [],
        "left": 8,
        "right": 9,
        "level": 1,
        "position": 1,
        "translations": {
            "en_US": {
                "locale": "en_US",
                "id": 9,
                "name": "toys",
                "slug": "toys",
                "description": "Toys for boys"
            }
        },
        "images": [
            {
                "id": 1,
                "type": "ford",
                "path": "b9/65/01cec3d87aa2b819e195331843f6.jpeg"
            }
        ],
        "_links": {
            "self": {
                "href": "\/api\/v1\/taxons\/9"
            }
        }
    }

Collection of Taxons
--------------------

To retrieve a paginated list of taxons you will need to call the ``/api/v1/taxons/`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/taxons/

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

To see the first page of all taxons use the below method:

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/taxons/ \
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
        "total": 5,
        "_links": {
            "self": {
                "href": "\/api\/v1\/taxons\/?page=1&limit=10"
            },
            "first": {
                "href": "\/api\/v1\/taxons\/?page=1&limit=10"
            },
            "last": {
                "href": "\/api\/v1\/taxons\/?page=1&limit=10"
            }
        },
        "_embedded": {
            "items": [
                {
                    "name": "Category",
                    "id": 1,
                    "code": "category",
                    "position": 0,
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 1,
                            "name": "Category",
                            "slug": "category",
                            "description": "Consequatur illo amet aliquam."
                        }
                    },
                    "images": [],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/taxons\/1"
                        }
                    }
                },
                {
                    "name": "T-Shirts",
                    "id": 5,
                    "code": "t_shirts",
                    "root": {
                        "name": "Category",
                        "id": 1,
                        "code": "category",
                        "position": 0,
                        "translations": [],
                        "images": [],
                        "_links": {
                            "self": {
                                "href": "\/api\/v1\/taxons\/1"
                            }
                        }
                    },
                    "parent": {
                        "name": "Category",
                        "id": 1,
                        "code": "category",
                        "position": 0,
                        "translations": [],
                        "images": [],
                        "_links": {
                            "self": {
                                "href": "\/api\/v1\/taxons\/1"
                            }
                        }
                    },
                    "position": 0,
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 5,
                            "name": "T-Shirts",
                            "slug": "t-shirts",
                            "description": "Modi aut laborum aut sint aut ea itaque porro."
                        }
                    },
                    "images": [],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/taxons\/5"
                        }
                    }
                },
                {
                    "name": "Men",
                    "id": 6,
                    "code": "mens_t_shirts",
                    "root": {
                        "name": "Category",
                        "id": 1,
                        "code": "category",
                        "position": 0,
                        "translations": [],
                        "images": [],
                        "_links": {
                            "self": {
                                "href": "\/api\/v1\/taxons\/1"
                            }
                        }
                    },
                    "parent": {
                        "name": "T-Shirts",
                        "id": 5,
                        "code": "t_shirts",
                        "position": 0,
                        "translations": [],
                        "images": [],
                        "_links": {
                            "self": {
                                "href": "\/api\/v1\/taxons\/5"
                            }
                        }
                    },
                    "position": 0,
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 6,
                            "name": "Men",
                            "slug": "t-shirts\/men",
                            "description": "Reprehenderit vero atque eaque sunt perferendis est."
                        }
                    },
                    "images": [],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/taxons\/6"
                        }
                    }
                },
                {
                    "name": "Women",
                    "id": 7,
                    "code": "womens_t_shirts",
                    "root": {
                        "name": "Category",
                        "id": 1,
                        "code": "category",
                        "position": 0,
                        "translations": [],
                        "images": [],
                        "_links": {
                            "self": {
                                "href": "\/api\/v1\/taxons\/1"
                            }
                        }
                    },
                    "parent": {
                        "name": "T-Shirts",
                        "id": 5,
                        "code": "t_shirts",
                        "position": 0,
                        "translations": [],
                        "images": [],
                        "_links": {
                            "self": {
                                "href": "\/api\/v1\/taxons\/5"
                            }
                        }
                    },
                    "position": 1,
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 7,
                            "name": "Women",
                            "slug": "t-shirts\/women",
                            "description": "Illum quia beatae assumenda impedit."
                        }
                    },
                    "images": [],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/taxons\/7"
                        }
                    }
                },
                {
                    "name": "toys",
                    "id": 9,
                    "code": "toys",
                    "root": {
                        "name": "Category",
                        "id": 1,
                        "code": "category",
                        "position": 0,
                        "translations": [],
                        "images": [],
                        "_links": {
                            "self": {
                                "href": "\/api\/v1\/taxons\/1"
                            }
                        }
                    },
                    "parent": {
                        "name": "Category",
                        "id": 1,
                        "code": "category",
                        "position": 0,
                        "translations": [],
                        "images": [],
                        "_links": {
                            "self": {
                                "href": "\/api\/v1\/taxons\/1"
                            }
                        }
                    },
                    "position": 1,
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 9,
                            "name": "toys",
                            "slug": "toys",
                            "description": "Toys for boys"
                        }
                    },
                    "images": [],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/taxons\/9"
                        }
                    }
                }
            ]
        }
    }

Updating Taxon
--------------

To fully update a taxon you will need to call the ``/api/v1/taxons/{code}`` endpoint with the ``PUT`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    PUT /api/v1/taxons/{code}

+-------------------------------------------+----------------+----------------------------------------------------+
| Parameter                                 | Parameter type | Description                                        |
+===========================================+================+====================================================+
| Authorization                             | header         | Token received during authentication               |
+-------------------------------------------+----------------+----------------------------------------------------+
| code                                      | url attribute  | **(unique)** Identifier of the requested taxon     |
+-------------------------------------------+----------------+----------------------------------------------------+
| translations['localeCode']['name']        | request        | *(optional)* Name of the taxon                     |
+-------------------------------------------+----------------+----------------------------------------------------+
| translations['localeCode']['slug']        | request        | *(optional)* **(unique)** Slug                     |
+-------------------------------------------+----------------+----------------------------------------------------+
| translations['localeCode']['description'] | request        | *(optional)* Description of the taxon              |
+-------------------------------------------+----------------+----------------------------------------------------+
| parent                                    | request        | *(optional)* The parent taxon's code               |
+-------------------------------------------+----------------+----------------------------------------------------+
| images                                    | request        | *(optional)* Images codes assigned to the taxon    |
+-------------------------------------------+----------------+----------------------------------------------------+

Example
^^^^^^^

To fully update the taxon with ``code = toys`` use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/taxons/toys \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PUT \
        --data '
            {
                "translations": {
                    "en_US": {
                        "name": "Dolls",
                        "slug": "dolls"
                    }
                }
           }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

To update a taxon partially you will need to call the ``/api/v1/taxons/{code}`` endpoint with the ``PATCH`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    PATCH /api/v1/taxons/{code}

+---------------+----------------+----------------------------------------------------+
| Parameter     | Parameter type | Description                                        |
+===============+================+====================================================+
| Authorization | header         | Token received during authentication               |
+---------------+----------------+----------------------------------------------------+
| code          | url attribute  | **(unique)** Identifier of the requested taxon     |
+---------------+----------------+----------------------------------------------------+

Example
^^^^^^^

To partially update the taxon with ``code = toys`` use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/taxons/toys \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PATCH \
        --data '
            {
                "translations": {
                    "en_US": {
                        "name": "Dolls"
                    }
                }
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

Deleting a Taxon
----------------

To delete a taxon you will need to call the ``/api/v1/taxons/{code}`` endpoint with the ``DELETE`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    DELETE /api/v1/taxons/{id}

+---------------+----------------+----------------------------------------------------+
| Parameter     | Parameter type | Description                                        |
+===============+================+====================================================+
| Authorization | header         | Token received during authentication               |
+---------------+----------------+----------------------------------------------------+
| code          | url attribute  | **(unique)** Identifier of the requested taxon     |
+---------------+----------------+----------------------------------------------------+

Example
^^^^^^^

To delete the taxon with ``code = toys`` use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/taxons/toys \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X DELETE

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

Set position of product in a Taxon
----------------------------------

The products in Sylius can by grouped by taxon, therefore for every product there is a relation between the product and the assigned taxon.
What is more, every product can have a specific position in the taxon to which it belongs. To put products in a specific order
you will need to call the ``/api/v1/taxons/{code}/products`` endpoint wih the ``PUT`` method.

Definition
^^^^^^^^^^
.. code-block:: text

    PUT /api/v1/taxons/{code}/products

+---------------+----------------+-----------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                     |
+===============+================+=================================================================+
| Authorization | header         | Token received during authentication                            |
+---------------+----------------+-----------------------------------------------------------------+
| code          | url attribute  | Code of the taxon in which the order of product will be changed |
+---------------+----------------+-----------------------------------------------------------------+

Example
^^^^^^^

To change the order of products with codes ``yellow_t_shirt`` and ``princess_t_shirt`` in taxon with code ``womens_t_shirts`` use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/taxons/womens_t_shirts/products \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PUT \
        --data '
            {
                "productsPositions": [
                    {
                        "productCode": "yellow_t_shirt",
                        "position": 3
                    },
                    {
                        "productCode": "princess_t_shirt",
                        "position": 0
                    }
                ]
            }
        '

.. note::

    Remember the *yellow_t_shirt* and *princess_t_shirt* and *womens_t_shirts*
    are just exemplary codes and you can change them for the ones you need.
    Check in the list of all products if you are not sure which codes should be used.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 NO CONTENT
