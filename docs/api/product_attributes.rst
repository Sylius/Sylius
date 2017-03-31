Product Attributes API
======================

These endpoints will allow you to easily manage product attributes. Base URI is `/api/v1/product-attributes`.

Product Attribute API response structure
----------------------------------------

If you request a product attribute via API, you will receive an object with the following fields:

+--------------+----------------------------------------------------------------------+
| Field        | Description                                                          |
+==============+======================================================================+
| id           | Id of the product attribute                                          |
+--------------+----------------------------------------------------------------------+
| code         | Unique product attribute identifier                                  |
+--------------+----------------------------------------------------------------------+
| position     | The position of the product attribute among other product attributes |
+--------------+----------------------------------------------------------------------+
| type         | Type of the product attribute (for example text)                     |
+--------------+----------------------------------------------------------------------+
| translations | Collection of translations (each contains name in given language)    |
+--------------+----------------------------------------------------------------------+

If you request for more detailed data, you will receive an object with the following fields:

+--------------+----------------------------------------------------------------------+
| Field        | Description                                                          |
+==============+======================================================================+
| id           | Id of the product attribute                                          |
+--------------+----------------------------------------------------------------------+
| code         | Unique product attribute identifier                                  |
+--------------+----------------------------------------------------------------------+
| position     | The position of the product attribute among other product attributes |
+--------------+----------------------------------------------------------------------+
| type         | Type of the product attribute (for example text)                     |
+--------------+----------------------------------------------------------------------+
| translations | Collection of translations (each contains name in given language)    |
+--------------+----------------------------------------------------------------------+
| updatedAt    | Last update date of the product attribute                            |
+--------------+----------------------------------------------------------------------+
| createdAt    | Creation date of the product attribute                               |
+--------------+----------------------------------------------------------------------+

.. note::

    Read more about :doc:`Product Attributes in the component docs</components/Product/models>`.

Creating a Product Attribute
----------------------------

To create a new product attribute you will need to call the ``/api/v1/products-attributes/{type}`` endpoint with the ``POST`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    POST /api/v1/product-attributes/{type}

+---------------+----------------+--------------------------------------------------+
| Parameter     | Parameter type | Description                                      |
+===============+================+==================================================+
| Authorization | header         | Token received during authentication             |
+---------------+----------------+--------------------------------------------------+
| type          | url attribute  | Type of the product attribute (for example text) |
+---------------+----------------+--------------------------------------------------+
| code          | request        | **(unique)** Product attribute identifier        |
+---------------+----------------+--------------------------------------------------+

Example
^^^^^^^

To create a new text product attribute use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/product-attributes/text \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
                "code": "mug_material"
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "id": 1,
        "code": "mug_material",
        "type": "text",
        "configuration": [],
        "position": 0,
        "translations": {},
        "_links": {
            "self": {
                "href": "\/api\/v1\/product-attributes\/mug_material"
            }
        }
    }

.. warning::

    If you try to create a product attribute without code you will receive a ``400 Bad Request`` error, that will contain validation errors.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/product-attributes/text \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 400 BAD REQUEST

.. code-block:: json

    {
        "code": 400,
        "message": "Validation Failed",
        "errors": {
            "children": {
                "type": {},
                "position": {},
                "translations": {},
                "code": {
                    "errors": [
                        "Please enter attribute code."
                    ]
                },
                "configuration": {
                    "children": {
                        "min": {},
                        "max": {}
                    }
                }
            }
        }
    }

You can also create a product attribute with additional (not required) fields:

+------------------------------------+----------------+----------------------------------------------------------------------------+
| Parameter                          | Parameter type | Description                                                                |
+====================================+================+============================================================================+
| position                           | request        | Position within sorted product attribute list of the new product attribute |
+------------------------------------+----------------+----------------------------------------------------------------------------+
| translations['localeCode']['name'] | request        | Name of the product attribute                                              |
+------------------------------------+----------------+----------------------------------------------------------------------------+

Some of product attributes have also their own (optional) configuration:

+------------------------+----------------------------+---------------------------------------------------------------------------------+
| Product attribute type | configuration construction | Description                                                                     |
+========================+============================+=================================================================================+
| text                   | configuration['min']       | Both field must be defined together.                                            |
|                        | configuration['max']       | They described minimal and maximal length of the text attribute.                |
+------------------------+----------------------------+---------------------------------------------------------------------------------+
| select                 | configuration['multiple']  | The *multiple*, *min*, and *max* must be defined together. They allow to select |
|                        | configuration['min']       | several values, limited by minimal and maximal amount of entries.               |
|                        | configuration['max']       | The *choices* is an array of available options in the product attribute.        |
|                        | configuration['choices']   |                                                                                 |
+------------------------+----------------------------+---------------------------------------------------------------------------------+

.. note::

    You can also see exemplary request about creating configured select product attribute
    `here <https://github.com/Sylius/Sylius/blob/master/tests/Controller/ProductAttributeApiTest.php>`_.

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/product-attributes/text/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
                "code": "mug_material",
                "translations": {
                    "de_CH": {
                        "name": "Becher Material"
                    },
                    "en_US": {
                        "name": "Mug material"
                    }
                }
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "id": 1,
        "code": "mug_material",
        "type": "text",
        "configuration": [],
        "position": 0,
        "createdAt": "2017-02-24T16:14:05+0100",
        "updatedAt": "2017-02-24T16:14:05+0100",
        "translations": {
            "de_CH": {
                "id": 1,
                "locale": "de_CH",
                "name": "Becher Material"
            },
            "en_US": {
                "id": 2,
                "locale": "en_US",
                "name": "Mug material"
            }
        },
        "_links": {
            "self": {
                "href": "\/api\/v1\/product-attributes\/mug_material"
            }
        }
    }

Getting a Single Product Attribute
----------------------------------

To retrieve the details of a product attribute you will need to call the ``/api/v1/product-attributes/code`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/product-attributes/{code}

+---------------+----------------+------------------------------------------+
| Parameter     | Parameter type | Description                              |
+===============+================+==========================================+
| Authorization | header         | Token received during authentication     |
+---------------+----------------+------------------------------------------+
| code          | url attribute  | Code of the requested product attribute  |
+---------------+----------------+------------------------------------------+

Example
^^^^^^^

To see the details of the product attribute with ``code = sticker_paper`` use the below method:

.. code-block:: bash

     $ curl http://demo.sylius.org/api/v1/product-attributes/sticker_paper \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

.. note::

    The *sticker_paper* code is just an example. Your value can be different.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

     STATUS: 200 OK

.. code-block:: json

    {
        "id": 2,
        "code": "sticker_paper",
        "type": "text",
        "configuration": [],
        "position": 1,
        "createdAt": "2017-03-29T10:05:00+0200",
        "updatedAt": "2017-03-31T09:48:37+0200",
        "translations": {
            "en_US": {
                "locale": "en_US",
                "id": 2,
                "name": "Sticker paper"
            }
        },
        "_links": {
            "self": {
                "href": "\/api\/v1\/product-attributes\/sticker_paper"
            }
        }
    }

Collection of Product Attributes
--------------------------------

To retrieve a paginated list of product attributes you will need to call the ``/api/v1/product-attributes/`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/product-attributes/

+---------------+----------------+-------------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                       |
+===============+================+===================================================================+
| Authorization | header         | Token received during authentication                              |
+---------------+----------------+-------------------------------------------------------------------+
| page          | query          | *(optional)* Number of the page, by default = 1                   |
+---------------+----------------+-------------------------------------------------------------------+
| paginate      | query          | *(optional)* Number of items to display per page, by default = 10 |
+---------------+----------------+-------------------------------------------------------------------+

To see the first page of all product attributes use the below method:

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/product-attributes/ \
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
        "total": 10,
        "_links": {
            "self": {
                "href": "\/api\/v1\/product-attributes\/?page=1&limit=10"
            },
            "first": {
                "href": "\/api\/v1\/product-attributes\/?page=1&limit=10"
            },
            "last": {
                "href": "\/api\/v1\/product-attributes\/?page=1&limit=10"
            }
        },
        "_embedded": {
            "items": [
                {
                    "id": 1,
                    "code": "mug_material",
                    "type": "select",
                    "position": 0,
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 1,
                            "name": "Mug material"
                        }
                    },
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/product-attributes\/mug_material"
                        }
                    }
                },
                {
                    "id": 2,
                    "code": "sticker_paper",
                    "type": "text",
                    "position": 1,
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 2,
                            "name": "Sticker paper"
                        }
                    },
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/product-attributes\/sticker_paper"
                        }
                    }
                },
                {
                    "id": 3,
                    "code": "sticker_resolution",
                    "type": "text",
                    "position": 2,
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 3,
                            "name": "Sticker resolution"
                        }
                    },
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/product-attributes\/sticker_resolution"
                        }
                    }
                },
                {
                    "id": 4,
                    "code": "book_author",
                    "type": "text",
                    "position": 3,
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 4,
                            "name": "Book author"
                        }
                    },
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/product-attributes\/book_author"
                        }
                    }
                },
                {
                    "id": 5,
                    "code": "book_isbn",
                    "type": "text",
                    "position": 4,
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 5,
                            "name": "Book ISBN"
                        }
                    },
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/product-attributes\/book_isbn"
                        }
                    }
                },
                {
                    "id": 6,
                    "code": "book_pages",
                    "type": "integer",
                    "position": 5,
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 6,
                            "name": "Book pages"
                        }
                    },
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/product-attributes\/book_pages"
                        }
                    }
                },
                {
                    "id": 7,
                    "code": "book_genre",
                    "type": "select",
                    "position": 6,
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 7,
                            "name": "Book genre"
                        }
                    },
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/product-attributes\/book_genre"
                        }
                    }
                },
                {
                    "id": 8,
                    "code": "t_shirt_brand",
                    "type": "text",
                    "position": 7,
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 8,
                            "name": "T-Shirt brand"
                        }
                    },
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/product-attributes\/t_shirt_brand"
                        }
                    }
                },
                {
                    "id": 9,
                    "code": "t_shirt_collection",
                    "type": "text",
                    "position": 8,
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 9,
                            "name": "T-Shirt collection"
                        }
                    },
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/product-attributes\/t_shirt_collection"
                        }
                    }
                },
                {
                    "id": 10,
                    "code": "t_shirt_material",
                    "type": "text",
                    "position": 9,
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 10,
                            "name": "T-Shirt material"
                        }
                    },
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/product-attributes\/t_shirt_material"
                        }
                    }
                }
            ]
        }
    }

Updating a Product Attribute
----------------------------

To fully update a product attribute you will need to call the ``/api/v1/product-attributes/code`` endpoint with the ``PUT`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    PUT /api/v1/product-attributes/{code}

+-----------------------------------+----------------+--------------------------------------+
| Parameter                         | Parameter type | Description                          |
+===================================+================+======================================+
| Authorization                     | header         | Token received during authentication |
+-----------------------------------+----------------+--------------------------------------+
| code                              | url attribute  | Unique product attribute identifier  |
+-----------------------------------+----------------+--------------------------------------+

Example
^^^^^^^

 To fully update the product attribute with ``code = mug_material`` use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/product-attributes/mug_material \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PUT \
        --data '
            {
                "translations": {
                    "en_US": {
                        "name": "Mug material"
                    }
                }
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

To update a product attribute partially you will need to call the ``/api/v1/product-attributes/code`` endpoint with the ``PATCH`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    PATCH /api/v1/product-attributes/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Unique product attribute identifier  |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To partially update the product attribute with ``code = mug_material`` use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/product-attributes/mug_material \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PATCH \
        --data '
            {
                "translations": {
                    "en_US": {
                        "name": "Mug material"
                    }
                }
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

Deleting a Product Attribute
----------------------------

To delete a product attribute you will need to call the ``/api/v1/product-attributes/code`` endpoint with the ``DELETE`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    DELETE /api/v1/product-attributes/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Unique product attribute identifier  |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To delete the product attribute with ``code = mug_material`` use the below method:

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/product-attributes/mug_material \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X DELETE

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content
