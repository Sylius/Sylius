Taxons API
==========

These endpoints will allow you to easily manage taxons. Base URI is `/api/v1/taxons`.

When you get a collection of resources, "Default" serialization group will be used and following fields will be exposed:

+--------------+--------------------------------------------------------------------------------------------+
| Field        | Description                                                                                |
+==============+============================================================================================+
| id           | Id of taxon                                                                                |
+--------------+--------------------------------------------------------------------------------------------+
| code         | Unique taxon identifier                                                                    |
+--------------+--------------------------------------------------------------------------------------------+
| root         | The main ancestor of taxon                                                                 |
+--------------+--------------------------------------------------------------------------------------------+
| parent       | The parent of taxon                                                                        |
+--------------+--------------------------------------------------------------------------------------------+
| translations | Collection of translations (each contains slug and name and description in given language) |
+--------------+--------------------------------------------------------------------------------------------+
| position     | The position of taxon among other taxons                                                   |
+--------------+--------------------------------------------------------------------------------------------+
| images       | Images assigned to taxon                                                                   |
+--------------+--------------------------------------------------------------------------------------------+

If you request for a more detailed data, you will receive an object with following fields:

+--------------+--------------------------------------------------------------------------------------------+
| Field        | Description                                                                                |
+==============+============================================================================================+
| id           | Id of taxon                                                                                |
+--------------+--------------------------------------------------------------------------------------------+
| code         | Unique taxon identifier                                                                    |
+--------------+--------------------------------------------------------------------------------------------+
| root         | The main ancestor of taxon                                                                 |
+--------------+--------------------------------------------------------------------------------------------+
| parent       | The parent of taxon                                                                        |
+--------------+--------------------------------------------------------------------------------------------+
| translations | Collection of translations (each contains slug and name and description in given language) |
+--------------+--------------------------------------------------------------------------------------------+
| position     | The position of taxon among other taxons                                                   |
+--------------+--------------------------------------------------------------------------------------------+
| images       | Images assigned to taxon                                                                   |
+--------------+--------------------------------------------------------------------------------------------+
| left         | Location within taxonomy                                                                   |
+--------------+--------------------------------------------------------------------------------------------+
| right        | Location within taxonomy                                                                   |
+--------------+--------------------------------------------------------------------------------------------+
| level        | How deep taxon is in the tree                                                              |
+--------------+--------------------------------------------------------------------------------------------+
| children     | Sub taxons                                                                                 |
+--------------+--------------------------------------------------------------------------------------------+


.. note::

    Read more about `Taxon`__

__ http://docs.sylius.org/en/latest/components/Taxonomy/models.html#taxon

Collection of Taxons
--------------------

To retrieve the paginated list of taxons you will need to call the ``/api/v1/taxons/`` endpoint with ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/taxons/

+---------------------------------------+----------------+---------------------------------------------------+
| Parameter                             | Parameter type | Description                                       |
+=======================================+================+===================================================+
| Authorization                         | header         | Token received during authentication              |
+---------------------------------------+----------------+---------------------------------------------------+
| limit                                 | query          | *(optional)* Number of items to display per page, |
|                                       |                | by default = 10                                   |
+---------------------------------------+----------------+---------------------------------------------------+

To see the first page of all taxons use the method below.

Example
.......

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/taxons/ \
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
        "total": 4,
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
                    "id": 1031,
                    "code": "category",
                    "position": 0,
                    "translations": [],
                    "images": [],
                        "_links": {
                            "self": {
                                "href": "\/api\/v1\/taxons\/1031"
                            }
                        }
                },
                {
                    "id": 1032,
                    "code": "t-shirts",
                    "root": {
                        "id": 1031,
                        "code": "category",
                        "position": 0,
                        "translations": [],
                        "images": [],
                        "_links": {
                            "self": {
                                "href": "\/api\/v1\/taxons\/1031"
                            }
                        }
                    },
                    "parent": {
                        "id": 1031,
                        "code": "category",
                        "position": 0,
                        "translations": [],
                        "images": [],
                        "_links": {
                            "self": {
                                "href": "\/api\/v1\/taxons\/1031"
                            }
                        }
                    },
                    "position": 0,
                    "translations": [],
                    "images": [],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/taxons\/1032"
                        }
                    }
                },
                {
                    "id": 1033,
                    "code": "men",
                    "root": {
                        "id": 1031,
                        "code": "category",
                        "position": 0,
                        "translations": [],
                        "images": [],
                        "_links": {
                            "self": {
                                "href": "\/api\/v1\/taxons\/1031"
                            }
                        }
                    },
                    "parent": {
                        "id": 1032,
                        "code": "t-shirts",
                        "root": {
                            "id": 1031,
                            "code": "category",
                            "position": 0,
                            "translations": [],
                            "images": [],
                            "_links": {
                                "self": {
                                    "href": "\/api\/v1\/taxons\/1031"
                                }
                            }
                        },
                        "parent": {
                            "id": 1031,
                            "code": "category",
                            "position": 0,
                            "translations": [],
                            "images": [],
                        "_links": {
                            "self": {
                                "href": "\/api\/v1\/taxons\/1031"
                            }
                        }
                        },
                        "position": 0,
                        "translations": [],
                        "images": [],
                        "_links": {
                            "self": {
                                "href": "\/api\/v1\/taxons\/1032"
                            }
                        }
                    },
                    "position": 0,
                    "translations": [],
                    "images": [],
                        "_links": {
                            "self": {
                                "href": "\/api\/v1\/taxons\/1033"
                            }
                        }
                },
                {
                    "id": 1034,
                    "code": "women",
                    "root": {
                        "id": 1031,
                        "code": "category",
                        "position": 0,
                        "translations": [],
                        "images": [],
                        "_links": {
                            "self": {
                                "href": "\/api\/v1\/taxons\/1031"
                            }
                        }
                    },
                    "parent": {
                        "code": "t-shirts",
                        "root": {
                            "id": 1031,
                            "code": "category",
                            "position": 0,
                            "translations": [],
                            "images": [],
                        "_links": {
                            "self": {
                                "href": "\/api\/v1\/taxons\/1031"
                            }
                        }
                        },
                        "parent": {
                            "id": 1031,
                            "code": "category",
                            "position": 0,
                            "translations": [],
                            "images": [],
                        "_links": {
                            "self": {
                                "href": "\/api\/v1\/taxons\/1031"
                            }
                        }
                        },
                        "position": 0,
                        "translations": [],
                        "images": [],
                        "_links": {
                            "self": {
                                "href": "\/api\/v1\/taxons\/1032"
                            }
                        }
                    },
                    "position": 1,
                    "translations": [],
                    "images": [],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/taxons\/1034"
                        }
                    }
                }
            ]
        }
    }

Getting a Single Taxon
----------------------

To retrieve the details of the taxon you will need to call the ``/api/v1/taxons/taxon_id`` endpoint with ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/taxons/{id}

+---------------+----------------+-------------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                       |
+===============+================+===================================================================+
| Authorization | header         | Token received during authentication                              |
+---------------+----------------+-------------------------------------------------------------------+
| id            | url attribute  | Id of requested resource                                          |
+---------------+----------------+-------------------------------------------------------------------+

Example
.......

To see the details for the the taxon  with id equals to 987 use the method below.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/taxons/987 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "id": 1035,
        "code": "category",
        "children": [
            {
                "id": 1036,
                "code": "t-shirts",
                "children": [
                    {
                        "id": 1037,
                        "code": "men",
                        "children": [],
                        "left": 3,
                        "right": 4,
                        "level": 2,
                        "position": 0,
                        "translations": [],
                        "images": [],
                        "_links": {
                            "self": {
                                "href": "\/api\/v1\/taxons\/1037"
                            }
                        }
                    },
                    {
                        "id": 1038,
                        "code": "women",
                        "children": [],
                        "left": 5,
                        "right": 6,
                        "level": 2,
                        "position": 1,
                        "translations": [],
                        "images": [],
                        "_links": {
                            "self": {
                                "href": "\/api\/v1\/taxons\/1038"
                            }
                        }
                    }
                ],
                "left": 2,
                "right": 7,
                "level": 1,
                "position": 0,
                "translations": [],
                "images": [],
                "_links": {
                    "self": {
                        "href": "\/api\/v1\/taxons\/1036"
                    }
                }
            }
        ],
        "left": 1,
        "right": 8,
        "level": 0,
        "position": 0,
        "translations": [],
        "images": [],
        "_links": {
            "self": {
                "href": "\/api\/v1\/taxons\/1035"
            }
        }
    }

Creating Taxon
--------------

To create new taxon you will need to call the ``/api/v1/taxons/`` endpoint with ``POST`` method.

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
.......

To create new taxon use the below method.

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

    If you want to create you taxon under other taxon you should pass also a parent code.

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "id": 8,
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
                "href": "\/api\/v1\/taxons\/8"
            }
        }
    }

If you try to create a taxon without code  you will receive a 400 error.

Example
.......

.. code-block:: bash

    curl http://demo.sylius.org/api/v1/taxons/ \
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

+-------------------------------------------+----------------+-------------------------------------------------------+
| Parameter                                 | Parameter type | Description                                           |
+====================================+================+==============================================================+
| Authorization                             | header         | Token received during authentication                  |
+-------------------------------------------+----------------+-------------------------------------------------------+
| code                                      | request        | **(unique)** Taxon identifier                         |
+-------------------------------------------+----------------+-------------------------------------------------------+
|translations['locale_code']['name']        | request        |  *(optional)* Name of the taxon                       |
+-------------------------------------------+----------------+-------------------------------------------------------+
|translations['locale_code']['slug']        | request        | *(optional)* **(unique)** Slug                        |
+-------------------------------------------+----------------+-------------------------------------------------------+
|translations['locale_code']['description'] | request        | *(optional)* Description of taxon                     |
+-------------------------------------------+----------------+-------------------------------------------------------+
| parent                                    | request        | *(optional)* The parent's code of taxon               |
+-------------------------------------------+----------------+-------------------------------------------------------+
| images                                    | request        | *(optional)* Images codes assigned to taxon           |
+-------------------------------------------+----------------+-------------------------------------------------------+

Example
.......

.. code-block:: bash

    curl http://demo.sylius.org/api/v1/taxons/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X POST
        --data '
           {
                "code":"toys",
                "translations":{
                    "en_US": {
                        "name": "Toys",
                        "slug": "category/toys",
                        "description": "The Toys"
                    }
                },
                "parent": "category",
                "images": [
                    {
                        "code": "ford"
                    }
                ]
            }
        '

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "id": 1051,
        "code": "toys",
        "root": {
            "id": 1047,
            "code": "category",
            "children": [
                {
                    "id": 1048,
                    "code": "t-shirts",
                    "children": [
                        {
                            "id": 1049,
                            "code": "men",
                            "children": [],
                            "left": 3,
                            "right": 4,
                            "level": 2,
                            "position": 0,
                            "translations": [],
                            "images": [],
                            "_links": {
                                "self": {
                                    "href": "\/api\/v1\/taxons\/1049"
                                }
                            }
                        },
                        {
                            "id": 1050,
                            "code": "women",
                            "children": [],
                            "left": 5,
                            "right": 6,
                            "level": 2,
                            "position": 1,
                            "translations": [],
                            "images": [],
                            "_links": {
                                "self": {
                                    "href": "\/api\/v1\/taxons\/1050"
                                }
                            }
                        }
                    ],
                    "left": 2,
                    "right": 7,
                    "level": 1,
                    "position": 0,
                    "translations": [],
                    "images": [],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/taxons\/1050"
                        }
                    }
                }
            ],
            "left": 1,
            "right": 10,
            "level": 0,
            "position": 0,
            "translations": [],
            "images": [],
            "_links": {
                "self": {
                    "href": "\/api\/v1\/taxons\/1051"
                }
            }
        },
        "parent": {
            "id": 1047,
            "code": "category",
            "children": [
                {
                    "id": 1048,
                    "code": "t-shirts",
                    "children": [
                        {
                            "id": 1049,
                            "code": "men",
                            "children": [],
                            "left": 3,
                            "right": 4,
                            "level": 2,
                            "position": 0,
                            "translations": [],
                            "images": [],
                            "_links": {
                                "self": {
                                    "href": "\/api\/v1\/taxons\/1049"
                                }
                            }
                        },
                        {
                            "id": 1050,
                            "code": "women",
                            "children": [],
                            "left": 5,
                            "right": 6,
                            "level": 2,
                            "position": 1,
                            "translations": [],
                            "images": [],
                            "_links": {
                                "self": {
                                    "href": "\/api\/v1\/taxons\/1050"
                                }
                            }
                        }
                    ],
                    "left": 2,
                    "right": 7,
                    "level": 1,
                    "position": 0,
                    "translations": [],
                    "images": [],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/taxons\/1048"
                        }
                    }
                }
            ],
            "left": 1,
            "right": 10,
            "level": 0,
            "position": 0,
            "translations": [],
            "images": [],
            "_links": {
                "self": {
                    "href": "\/api\/v1\/taxons\/1047"
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
                "id": 74,
                "name": "Toys",
                "slug": "category\/toys",
                "description": "The Toys"
            }
        },
        "images": [
            {
                "id": 1,
                "code": "ford",
                "path": "b9/65/01cec3d87aa2b819e195331843f6.jpeg"
		    }
        ],
        "_links": {
            "self": {
                "href": "\/api\/v1\/taxons\/1051"
            }
        }
    }

Updating Taxon
--------------

To full update a taxon you will need to call the ``/api/v1/taxons/taxon_id`` endpoint with ``PUT`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    PUT /api/v1/taxons/{id}

+------------------------------------+----------------+--------------------------------------+
| Parameter                          | Parameter type | Description                          |
+====================================+================+======================================+
| Authorization                      | header         | Token received during authentication |
+------------------------------------+----------------+--------------------------------------+
| id                                 | url attribute  | Id of requested resource             |
+------------------------------------+----------------+--------------------------------------+
|translations['locale_code']['name'] | request        | Name of the taxon                    |
+------------------------------------+----------------+--------------------------------------+
|translations['locale_code']['slug'] | request        | **(unique)** Slug                    |
+------------------------------------+----------------+--------------------------------------+

Example
.......

To full update the taxon with id equals to 3 use the below method.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/taxons/3 \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PUT \
        --data '
            {
                "translations": {
                    "en_US": {
                        "name": "Books",
                        "slug": "category/books"
                    }
                }
	        }
        '

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 204 No Content

To partial update a taxon you will need to call the ``/api/v1/taxons/taxon_id`` endpoint with ``PATCH`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    PATCH /api/v1/taxons/{id}

+---------------+----------------+--------------------------------------------------------+
| Parameter     | Parameter type | Description                                            |
+===============+================+========================================================+
| Authorization | header         | Token received during authentication                   |
+---------------+----------------+--------------------------------------------------------+
| id            | url attribute  | Id of requested resource                               |
+---------------+----------------+--------------------------------------------------------+

Example
.......

To partial update the taxon with id equals to 3  use the below method.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/taxons/3 \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PATCH \
        --data '
            {
                "translations": {
                    "en_US": {
                        "name": "Books",
                        "slug": "books"
                    }
                }
            }
        '

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 204 No Content

Deleting Taxon
--------------

To delete a taxon you will need to call the ``/api/v1/taxons/taxon_id` endpoint with ``DELETE`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    DELETE /api/v1/taxons/{id}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| id            | url attribute  | Id of removed taxon                  |
+---------------+----------------+--------------------------------------+

Example
.......

To delete the taxon with id equals to 3 use the method below.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/taxons/3 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X DELETE

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 204 No Content
